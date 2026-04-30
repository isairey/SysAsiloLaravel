<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

/**
 * Comprueba que el usuario autenticado tenga un permiso concreto.
 *
 * Se usa así en las rutas:
 *   Route::middleware(['auth','permission:nombre.del.permiso'])->group(function () { ... });
 *
 * Si el usuario no lo tiene, devuelve 403.
 * Si el usuario está inactivo o bloqueado, lo desloguea y lo redirige al login.
 */
class PermissionMiddleware
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure                  $next
     * @param  string                    $permission  Nombre del permiso (p. ej.  "modulo.proteccion.registrar")
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // 1) Usuario debe estar logueado
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        /** @var User $user */
        $user = Auth::user();

        // 2) Verificar estado y bloqueos
        if (! $user->active || $user->isTemporarilyLocked()) {
            Auth::logout();
            return redirect()
                ->route('login')
                ->withErrors(['ci' => 'Su cuenta está inactiva o bloqueada temporalmente.']);
        }

        // 3) ¿Tiene el permiso?
        //    El Gate::before que pusiste en AppServiceProvider ya permite que el admin pase siempre,
        //    pero añadimos el mismo “atajo” aquí por claridad.
        if ($user->hasRole('admin') || $user->hasPermission($permission)) {
            return $next($request);
        }

        abort(403, 'No tienes permisos para acceder a esta sección.');
    }
}
