<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- 1) ADMINISTRADOR (CI: 12345678) ---
        $ciAdmin = '12345678';
        if (!DB::table('persona')->where('ci', $ciAdmin)->exists()) {
            DB::table('persona')->insert([
                'ci' => $ciAdmin,
                'primer_apellido' => 'Admin',
                'segundo_apellido' => 'Sistema',
                'nombres' => 'Administrador',
                'sexo' => 'M',
                'fecha_nacimiento' => '1980-01-01',
                'edad' => 44,
                'estado_civil' => 'soltero',
                'domicilio' => 'Dirección administrativa',
                'telefono' => '12345678',
                'zona_comunidad' => 'Centro',
                'area_especialidad' => null,
                'area_especialidad_legal' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        if (!DB::table('usuario')->where('ci', $ciAdmin)->exists()) {
            User::create([
                'ci' => $ciAdmin,
                'id_rol' => 1, // admin
                'password' => Hash::make('admin123'),
                'active' => true,
                'login_attempts' => 0,
            ]);
        }

        // --- 2) RESPONSABLE (CI: 87654321) ---
        $ciResp = '87654321';
        if (!DB::table('persona')->where('ci', $ciResp)->exists()) {
            DB::table('persona')->insert([
                'ci' => $ciResp,
                'primer_apellido' => 'González',
                'segundo_apellido' => 'Pérez',
                'nombres' => 'María Elena',
                'sexo' => 'F',
                'fecha_nacimiento' => '1985-05-15',
                'edad' => 39,
                'estado_civil' => 'casado',
                'domicilio' => 'Av. Principal 123',
                'telefono' => '87654321',
                'zona_comunidad' => 'Norte',
                'area_especialidad' => 'Fisioterapia-Kinesiologia',
                'area_especialidad_legal' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        if (!DB::table('usuario')->where('ci', $ciResp)->exists()) {
            User::create([
                'ci' => $ciResp,
                'id_rol' => 2, // responsable
                'password' => Hash::make('responsable123'),
                'active' => true,
                'login_attempts' => 0,
            ]);
        }

        // --- 3) LEGAL (CI: 87654350) ---
        $ciLegal = '87654350';
        if (!DB::table('persona')->where('ci', $ciLegal)->exists()) {
            DB::table('persona')->insert([
                'ci' => $ciLegal,
                'primer_apellido' => 'Salazar',
                'segundo_apellido' => 'Lopez',
                'nombres' => 'Carlos Andrés',
                'sexo' => 'M',
                'fecha_nacimiento' => '1990-10-20',
                'edad' => 33,
                'estado_civil' => 'soltero',
                'domicilio' => 'Av. Legalista 456',
                'telefono' => '87654350',
                'zona_comunidad' => 'Sur',
                'area_especialidad' => null,
                'area_especialidad_legal' => 'Derecho',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        if (!DB::table('usuario')->where('ci', $ciLegal)->exists()) {
            User::create([
                'ci' => $ciLegal,
                'id_rol' => 3, // legal
                'password' => Hash::make('legal123'),
                'active' => true,
                'login_attempts' => 0,
            ]);
        }
        
        // La sección para 'asistente-social' ha sido eliminada por completo.

        // Mensajes en consola
        $this->command->info('Usuarios creados exitosamente:');
        $this->command->info('Admin - CI: 12345678, Password: admin123');
        $this->command->info('Responsable - CI: 87654321, Password: responsable123');
        $this->command->info('Legal - CI: 87654350, Password: legal123');
    }
}