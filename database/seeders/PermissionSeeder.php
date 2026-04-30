<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Limpiar la tabla de permisos para evitar duplicados en re-seedings
        Permission::query()->delete();

        $permissions = [
            // Permisos del Dashboard
            ['name' => 'dashboard.view', 'description' => 'Ver el dashboard principal'],

            // Permisos para Roles
            ['name' => 'roles.view', 'description' => 'Ver listado de roles'],
            ['name' => 'roles.create', 'description' => 'Crear nuevos roles'],
            ['name' => 'roles.edit', 'description' => 'Editar roles existentes'],
            // SOLUCIÓN 2: Corregido de 'roles.delete' a 'roles.destroy' para coincidir con las rutas y vistas.
            ['name' => 'roles.destroy', 'description' => 'Eliminar roles'],

            // Permisos para Usuarios
            ['name' => 'users.view', 'description' => 'Ver listado de usuarios'],
            ['name' => 'users.create', 'description' => 'Crear nuevos usuarios'],
            ['name' => 'users.edit', 'description' => 'Editar usuarios existentes'],
            ['name' => 'users.delete', 'description' => 'Eliminar usuarios'],
            ['name' => 'users.toggle_activity', 'description' => 'Activar/desactivar usuarios'],

            // Permisos para Adulto Mayor
            ['name' => 'adulto_mayor.view', 'description' => 'Ver listado de adultos mayores'],
            ['name' => 'adulto_mayor.create', 'description' => 'Registrar nuevos adultos mayores'],
            ['name' => 'adulto_mayor.edit', 'description' => 'Editar datos de adultos mayores'],
            ['name' => 'adulto_mayor.delete', 'description' => 'Eliminar registros de adultos mayores'],

            // Permisos para Módulo de Protección
            ['name' => 'proteccion.view', 'description' => 'Acceder al módulo de protección'],
            ['name' => 'proteccion.create', 'description' => 'Registrar nuevos casos de protección'],
            ['name' => 'proteccion.edit', 'description' => 'Editar casos de protección'],
            ['name' => 'proteccion.delete', 'description' => 'Eliminar casos de protección'],
            ['name' => 'proteccion.reportes', 'description' => 'Generar reportes de protección'],

            // Permisos para Módulo de Salud
            ['name' => 'salud.view', 'description' => 'Acceder al módulo de salud'],
            ['name' => 'salud.servicios', 'description' => 'Gestionar servicios de salud'],
            ['name' => 'salud.historias', 'description' => 'Ver historias clínicas'],
            ['name' => 'salud.reportes', 'description' => 'Generar reportes de salud'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}