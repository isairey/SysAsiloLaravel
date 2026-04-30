<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class EspecialidadMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $especialidad  La especialidad requerida (ej. "Enfermeria", "Fisioterapia").
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $especialidad): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var User $user */
        $user = Auth::user();
        $userRole = strtolower($user->role_name ?? optional($user->rol)->nombre_rol);

        // NUEVO: Permitir al administrador el acceso a todas las rutas de especialidad.
        if ($userRole === 'admin') {
            return $next($request);
        }

        // La lógica existente se aplica solo si el usuario no es admin.
        if ($userRole !== 'responsable') {
            Log::warning("Acceso denegado: El usuario {$user->ci} con rol '{$userRole}' intentó acceder a una ruta protegida por especialidad.");
            abort(403, 'Acción no autorizada.');
        }

        if (!$user->persona || !$user->persona->area_especialidad) {
            Log::warning("Acceso denegado: El usuario responsable {$user->ci} no tiene una especialidad asignada.");
            abort(403, 'No tienes una especialidad asignada para acceder a este recurso.');
        }
        
        $userEspecialidad = $user->persona->area_especialidad;
        if ($userEspecialidad !== $especialidad) {
            Log::warning("Acceso denegado: El usuario {$user->ci} con especialidad '{$userEspecialidad}' intentó acceder a un recurso para '{$especialidad}'.");
            abort(403, 'No tienes la especialidad requerida para esta sección.');
        }

        return $next($request);
    }
}