<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User; // Asegúrate de que el modelo User está importado

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles Los nombres de los roles permitidos.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Lógica mejorada: Itera sobre los roles permitidos y usa el método hasRole()
        // del modelo User, que ahora está garantizado que existe.
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request); // Permiso concedido
            }
        }

        // Si el bucle termina, el usuario no tiene ninguno de los roles requeridos.
        abort(403, 'Acceso denegado. No tienes el rol requerido.');
    }
}