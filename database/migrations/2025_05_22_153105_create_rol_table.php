<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rol', function (Blueprint $table) {
            $table->id('id_rol');
            $table->string('nombre_rol', 50)->unique();
            $table->text('descripcion')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Insertar roles predefinidos
        DB::table('rol')->insert([
            ['id_rol' => 1, 'nombre_rol' => 'admin', 'descripcion' => 'Administrador del sistema', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id_rol' => 2, 'nombre_rol' => 'responsable', 'descripcion' => 'Responsable de casos', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id_rol' => 3, 'nombre_rol' => 'legal', 'descripcion' => 'Asistente legal', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rol');
    }
};