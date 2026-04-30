<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class CheckUserPermissions extends Command
{
    protected $signature = 'app:check-user-permissions';
    protected $description = 'Verifica los permisos de un usuario específico desde la consola para depuración.';

    public function handle()
    {
        try {
            $this->info("--- [PASO 0] Iniciando Verificación de Permisos ---");

            // SOLUCIÓN: Se busca al usuario por su 'ci', que es el identificador correcto en tu base de datos.
            // El CI '12345678' se define en tu AdminUserSeeder.
            $user = User::where('ci', '12345678')->first();

            if (!$user) {
                $this->error("¡ERROR CRÍTICO! No se pudo encontrar al usuario con CI '12345678'.");
                $this->warn("Asegúrate de que el seeder AdminUserSeeder está creando este usuario.");
                return 1;
            }

            $this->line("Usuario encontrado: CI " . $user->ci . " (ID: " . $user->id_usuario . ")");

            // A partir de aquí, la lógica de diagnóstico continúa como antes.
            $rol = $user->rol;
            if (!$rol) {
                $this->error("¡FALLO CRÍTICO! El usuario 'admin' NO tiene ningún rol asignado.");
                return 1;
            }
            $this->info("[PASO 1] Verificación de Rol Directa -> OK");
            $this->line("  - Rol Asignado: '" . $rol->nombre_rol . "'");

            if ($user->hasRole('Admin')) {
                $this->info("[PASO 2] Verificación con hasRole('Admin') -> OK");
            } else {
                $this->error("¡FALLO CRÍTICO! El método hasRole('Admin') está devolviendo FALSE.");
            }

            $permissionCount = $rol->permissions()->count();
            if ($permissionCount > 0) {
                $this->info("[PASO 3] Conteo de Permisos del Rol en BD -> OK");
                $this->line("  - Número de permisos para '{$rol->nombre_rol}': " . $permissionCount);
            } else {
                $this->error("¡FALLO CRÍTICO! El rol '{$rol->nombre_rol}' NO tiene permisos en la tabla 'permission_role'.");
            }

            if ($user->hasPermission('roles.create')) {
                $this->info("[PASO 4] Verificación con hasPermission('roles.create') -> OK");
            } else {
                $this->error("¡FALLO CRÍTICO! El método hasPermission('roles.create') está devolviendo FALSE.");
            }
            
            if (Gate::forUser($user)->allows('roles.create')) {
                $this->info("[PASO 5] Verificación final con Gate::allows('roles.create') -> OK");
            } else {
                $this->error("¡FALLO CRÍTICO! Gate::allows('roles.create') está devolviendo FALSE.");
            }

        } catch (\Exception $e) {
            $this->error("\n--- ¡ERROR INESPERADO DENTRO DEL COMANDO! ---");
            $this->error("Mensaje: " . $e->getMessage());
            $this->line("Archivo: " . $e->getFile() . " en la línea " . $e->getLine());
            Log::error("Error en CheckUserPermissions command: " . $e->getMessage());
            return 1;
        }

        $this->info("\n--- Verificación Terminada ---");
        return 0;
    }
}