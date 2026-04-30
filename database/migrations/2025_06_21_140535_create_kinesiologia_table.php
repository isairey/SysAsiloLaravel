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
        Schema::create('kinesiologia', function (Blueprint $table) {
            $table->id('cod_kine'); // Clave primaria
            
            $table->boolean('entrenamiento_funcional')->default(false); // Servicio: Entrenamiento Funcional
            $table->boolean('gimnasio_maquina')->default(false);        // Servicio: Gimnasio Máquinas
            $table->boolean('aquafit')->default(false);                 // Servicio: Aquafit
            $table->boolean('hidroterapia')->default(false);            // Servicio: Hidroterapia
            $table->boolean('manana')->default(false);                  // Turno: Mañana
            $table->boolean('tarde')->default(false);                   // Turno: Tarde
            $table->timestamps(); // created_at y updated_at
            // ---ESTA LINEA ES LA ENCARGADA DEL BORRADO LOGICO----------------------------------------------------------------
            $table->softDeletes();
            // -----------------------------------------------------------------
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
        Schema::dropIfExists('kinesiologia');
    }
};
