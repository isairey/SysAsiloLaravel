<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB; // Importante: Importar la fachada de DB
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        //
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        try {
            if (Schema::hasTable('permissions')) {
                
                // SOLUCIÓN DEFINITIVA: Se realiza una consulta directa a la base de datos
                // para obtener el rol del usuario. Esto evita cualquier problema de carga de
                // relaciones de Eloquent que pueda ocurrir durante el ciclo de vida de la petición.
                Gate::before(function (User $user, $ability) {
                    $roleName = DB::table('rol')
                                  ->join('usuario', 'rol.id_rol', '=', 'usuario.id_rol')
                                  ->where('usuario.id_usuario', $user->id_usuario)
                                  ->value('rol.nombre_rol');
                    
                    if ($roleName && strtolower($roleName) === 'admin') {
                        return true;
                    }
                    
                    return null;
                });

                // Se definen los Gates para todos los demás permisos de forma dinámica.
                $permissions = Cache::rememberForever('permissions', function () {
                    return Permission::all();
                });

                foreach ($permissions as $permission) {
                    Gate::define($permission->name, function (User $user) use ($permission) {
                        return $user->hasPermission($permission->name);
                    });
                }
            }
        } catch (\Exception $e) {
            Log::error("Error crítico al registrar los Gates en AuthServiceProvider: " . $e->getMessage());
        }
    }
}
