<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Maneja una solicitud de inicio de sesión.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'ci' => 'required|string',
            'password' => 'required|string',
        ], [
            'ci.required' => 'El carnet de identidad es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $ci = $request->input('ci');
        $password = $request->input('password');

        // Buscar usuario por CI
        $user = User::where('ci', $ci)->first();

        if (!$user) {
            Log::warning("Intento de login con CI inexistente: {$ci}");
            return back()->withErrors(['ci' => 'Credenciales incorrectas.']);
        }

        // Verificar si el usuario puede hacer login (activo y no bloqueado)
        if (!$user->canLogin()) {
            if (!$user->active && !$user->temporary_lockout_until) {
                Log::warning("Intento de login de usuario desactivado: {$ci}");
                return back()->withErrors(['ci' => 'Su cuenta ha sido desactivada. Contacte al administrador.']);
            }
            if ($user->isTemporarilyLocked()) {
                $minutesLeft = $user->getTimeUntilUnlock();
                Log::warning("Intento de login de usuario temporalmente bloqueado: {$ci}");
                return back()->withErrors(['ci' => "Su cuenta está temporalmente bloqueada. Intente nuevamente en {$minutesLeft} minutos."]);
            }
        }

        // Verificar contraseña
        if (!Hash::check($password, $user->password)) {
            $user->incrementLoginAttempts();
            $attemptsLeft = 3 - $user->login_attempts;
            
            Log::warning("Intento de login fallido para CI: {$ci}. Intentos: {$user->login_attempts}");
            
            if ($user->login_attempts >= 3) {
                return back()->withErrors(['ci' => 'Ha excedido el número máximo de intentos. Su cuenta ha sido bloqueada por 10 minutos.']);
            } else {
                return back()->withErrors(['ci' => "Credenciales incorrectas. Le quedan {$attemptsLeft} intentos."]);
            }
        }

        // Login exitoso
        $user->resetLoginAttempts();
        Auth::login($user);

        $roleName = strtolower($user->role_name ?? optional($user->rol)->nombre_rol);
        Log::info("Login exitoso para usuario: {$ci} - Rol: {$roleName}");

        // Redireccionar según el rol
        return $this->redirectBasedOnRole($user);
    }

    /**
     * Redirige al usuario a su dashboard correspondiente basado en su rol.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectBasedOnRole($user)
    {
        $roleName = strtolower($user->role_name ?? optional($user->rol)->nombre_rol);
        
        switch ($roleName) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'responsable':
                return redirect()->route('responsable.dashboard');
            case 'legal':
                return redirect()->route('legal.dashboard');
            // El caso para 'asistente-social' ha sido eliminado.
            default:
                Log::error("Rol no reconocido '{$roleName}' para el usuario CI: {$user->ci}. Cerrando sesión.");
                Auth::logout();
                return redirect()->route('login')->withErrors(['ci' => 'Tu rol no es válido. Contacta al administrador.']);
        }
    }

    /**
     * Cierra la sesión del usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            Log::info("Logout de usuario: {$user->ci}");
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}