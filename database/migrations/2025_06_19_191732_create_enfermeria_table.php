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
        Schema::create('enfermeria', function (Blueprint $table) {
            $table->id('cod_enf'); // Primary key, siguiendo tu esquema de imagen
            $table->string('presion_arterial')->nullable();
            $table->string('frecuencia_cardiaca')->nullable();
            $table->string('frecuencia_respiratoria')->nullable();
            $table->string('pulso')->nullable();
            $table->string('temperatura')->nullable();
            $table->string('control_oximetria')->nullable();
            $table->string('inyectables')->nullable();
            $table->string('peso_talla')->nullable();
            $table->string('orientacion_alimentacion')->nullable();
            $table->string('lavado_oidos')->nullable();
            $table->string('orientacion_tratamiento')->nullable();
            $table->string('curacion')->nullable();
            $table->string('adm_medicamentos')->nullable();
            $table->string('derivacion')->nullable();
            
            // Campos de auditoría
            $table->unsignedBigInteger('id_adulto');
            $table->foreign('id_adulto')->references('id_adulto')->on('adulto_mayor')->onDelete('cascade');
            $table->unsignedBigInteger('id_usuario'); // Quien registra/modifica
            $table->foreign('id_usuario')->references('id_usuario')->on('usuario')->onDelete('cascade'); 
            $table->timestamps(); // created_at, updated_at
             // ---ESTA LINEA ES LA ENCARGADA DEL BORRADO LOGICO----------------------------------------------------------------
            $table->softDeletes();
            // ----------------------------------------------------
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enfermeria');
    }
};
