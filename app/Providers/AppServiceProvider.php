<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // SOLUCIÓN: Centralizamos toda la lógica de autorización aquí,
        // combinando el enfoque que funcionaba antes con la lógica de permisos real.
        try {
            if (Schema::hasTable('permissions')) {

                // Gate::before se ejecuta primero. Si el usuario es 'admin', concede todos los permisos.
                // Esta es la forma más eficiente de manejar un super-administrador.
                Gate::before(function (User $user, $ability) {
                    if ($user->hasRole('admin')) {
                        return true;
                    }
                    return null;
                });

                // Se definen los Gates dinámicamente para todos los demás permisos.
                $permissions = Permission::all();
                foreach ($permissions as $permission) {
                    Gate::define($permission->name, function (User $user) use ($permission) {
                        return $user->hasPermission($permission->name);
                    });
                }
            }
        } catch (\Exception $e) {
            Log::error("Error al registrar los Gates en AppServiceProvider: " . $e->getMessage());
        }
    }
}
