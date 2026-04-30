<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('persona', function (Blueprint $table) {
            $table->string('ci')->primary(); // Carnet de Identidad como clave primaria
            $table->string('primer_apellido', 100);
            $table->string('segundo_apellido', 100)->nullable();
            $table->string('nombres', 150);
            $table->enum('sexo', ['M', 'F', 'O']); // M = Masculino, F = Femenino 'O' = Otro
            $table->date('fecha_nacimiento');
            $table->integer('edad')->nullable(); // Se puede calcular automáticamente
            $table->enum('estado_civil', ['casado', 'divorciado', 'soltero', 'otro'])->default('soltero');
            $table->text('domicilio');
            $table->string('telefono', 20)->nullable();
            $table->string('zona_comunidad', 150)->nullable();

            // --- CAMBIO CLAVE: Definición Unificada de Especialidades ---
            // Especialidad para Rol Responsable (Salud)
            $table->enum('area_especialidad', ['Enfermeria', 'Fisioterapia-Kinesiologia', 'otro'])->nullable();
            
            // Especialidad para Rol Legal
            $table->enum('area_especialidad_legal', ['Asistente Social', 'Psicologia', 'Derecho'])->nullable();
            
            // ---ESTA LINEA ES LA ENCARGADA DEL BORRADO LOGICO----------------------------------------------------------------
            $table->softDeletes();
            // -------------------------------------------------------------------------------------------
           
            $table->timestamps();
            
            // Índices para mejorar rendimiento
            $table->index(['nombres', 'primer_apellido']);
            $table->index('fecha_nacimiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persona');
    }
};