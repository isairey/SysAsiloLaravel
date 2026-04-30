<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rol;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener los roles
        $adminRole = Rol::where('nombre_rol', 'admin')->first();
        $responsableRole = Rol::where('nombre_rol', 'responsable')->first();
        $legalRole = Rol::where('nombre_rol', 'legal')->first();

        // --- 1. Permisos para Administrador (TODOS) ---
        if ($adminRole) {
            // Asigna todos los permisos existentes al rol de admin
            $allPermissions = Permission::pluck('id');
            $adminRole->permissions()->sync($allPermissions);
            $this->command->info('Todos los permisos han sido asignados al rol "admin".');
        }

        // --- 2. Permisos para Rol Legal ---
        if ($legalRole) {
            $legalPermissions = Permission::whereIn('name', [
                'dashboard.view',
                'adulto_mayor.view',
                'adulto_mayor.create',
                'adulto_mayor.edit',
                'adulto_mayor.delete',
                'proteccion.view',
                'proteccion.create',
                'proteccion.edit',
                'proteccion.delete',
                'proteccion.reportes',
            ])->pluck('id');
            $legalRole->permissions()->sync($legalPermissions);
            $this->command->info('Permisos específicos asignados al rol "legal".');
        }

        // --- 3. Permisos para Rol Responsable de Salud ---
        if ($responsableRole) {
            $responsablePermissions = Permission::whereIn('name', [
                'dashboard.view',
                'salud.view',
                'salud.servicios',
                'salud.historias',
                'salud.reportes',
            ])->pluck('id');
            $responsableRole->permissions()->sync($responsablePermissions);
            $this->command->info('Permisos específicos asignados al rol "responsable".');
        }
    }
}
