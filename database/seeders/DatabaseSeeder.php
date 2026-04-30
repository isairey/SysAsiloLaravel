<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // La migración de Rol ya los crea, pero los seeders necesitan ejecutarse en este orden:
            // 1. Crear los usuarios y roles (los roles ya se crean en su migración).
            AdminUserSeeder::class,
            
            // 2. Crear todos los permisos disponibles.
            PermissionSeeder::class,
            
            // 3. Asignar los permisos creados a los roles existentes.
            RolePermissionSeeder::class,
        ]);
    }
}
