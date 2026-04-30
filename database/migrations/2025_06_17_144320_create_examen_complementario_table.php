<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamenComplementarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('examen_complementario', function (Blueprint $table) {
            $table->id('id_examen'); // Primary Key
            $table->string('presion_arterial')->nullable();
            $table->string('temperatura')->nullable();
            $table->string('peso_corporal')->nullable();
            $table->string('resultado_prueba')->nullable();
            $table->string('diagnostico')->nullable();

            $table->unsignedBigInteger('id_historia');
            $table->foreign('id_historia')->references('id_historia')->on('historia_clinica')->onDelete('cascade'); // FK to historia_clinica
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuario')->onDelete('cascade'); // Foreign key to users table
            $table->unsignedBigInteger('id_adulto');
            $table->foreign('id_adulto')->references('id_adulto')->on('adulto_mayor')->onDelete('cascade');

            $table->timestamps(); // created_at and updated_at
            // ---ESTA LINEA ES LA ENCARGADA DEL BORRADO LOGICO----------------------------------------------------------------
            $table->softDeletes();
            // ------------------------------------------------------------------
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('examen_complementario');
    }
}
