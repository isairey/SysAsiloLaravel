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
        Schema::create('fisioterapia', function (Blueprint $table) {
            $table->id('cod_fisio'); // Clave primaria
            
            // Campos del diagrama E/R
            $table->string('num_emergencia')->nullable(); // NUMEROS DE EMERGENCIA
            $table->text('enfermedades_actuales')->nullable(); // ENFERMEDADES ACTUALES (texto libre)
            $table->text('alergias')->nullable(); // ALERGIAS (texto libre)
            $table->date('fecha_programacion')->nullable(); // FECHA DE PROGRAMACIÓN
            
            // --- NUEVOS CAMPOS ---
            $table->date('fecha_inicio')->nullable(); // Fecha de Inicio de la terapia
            $table->date('fecha_fin')->nullable();    // Fecha de Fin de la terapia
            $table->integer('numero_sesiones')->unsigned()->nullable(); // Número de sesiones (no puede ser negativo)
            // --- FIN NUEVOS CAMPOS ---

            $table->text('motivo_consulta')->nullable(); // MOTIVO DE CONSULTA
            $table->text('solicitud_atencion')->nullable(); // SOLICITUD ATENCIÓN
            $table->string('equipos')->nullable(); // EQUIPOS (Electro Estimulador, Ultrasonido, Otros)
            
            // Timestamps
            $table->timestamps();
             // ---ESTA LINEA ES LA ENCARGADA DEL BORRADO LOGICO----------------------------------------------------------------
            $table->softDeletes();
            // ----------------------------------------------------
            // Definición de claves foráneas
            $table->unsignedBigInteger('id_adulto'); // Clave foránea para AdultoMayor
            $table->foreign('id_adulto')->references('id_adulto')->on('adulto_mayor')->onDelete('cascade');
            
            $table->unsignedBigInteger('id_historia')->nullable(); // Clave foránea para HistoriaClinica (opcional para ocupacion y grado_instruccion)
            $table->foreign('id_historia')->references('id_historia')->on('historia_clinica')->onDelete('set null');
            
            $table->unsignedBigInteger('id_usuario'); // Clave foránea para el usuario que registra
            $table->foreign('id_usuario')->references('id_usuario')->on('usuario')->onDelete('cascade'); // Asumiendo 'id' como PK de 'users'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fisioterapia');
    }
};
