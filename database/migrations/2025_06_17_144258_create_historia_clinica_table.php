<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoriaClinicaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historia_clinica', function (Blueprint $table) {
            $table->id('id_historia'); // Primary Key
            $table->string('municipio_nombre')->nullable();
            $table->string('establecimiento')->nullable();
            $table->text('antecedentes_personales')->nullable();
            $table->text('antecedentes_familiares')->nullable();
            $table->text('estado_actual')->nullable();
            $table->enum('tipo_consulta', ['N', 'R']);
            $table->string('ocupacion')->nullable();
            $table->string('grado_instruccion')->nullable();
            $table->string('lugar_nacimiento_provincia')->nullable(); // Desglosado de 'lugar_nacimiento'
            $table->string('lugar_nacimiento_departamento')->nullable(); // Desglosado de 'lugar_nacimiento'
            $table->string('domicilio_actual')->nullable();

            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuario')->onDelete('cascade'); // Foreign key to users table
            $table->unsignedBigInteger('id_adulto');
            $table->foreign('id_adulto')->references('id_adulto')->on('adulto_mayor')->onDelete('cascade');

            $table->timestamps(); // created_at and updated_at
            // ---ESTA LINEA ES LA ENCARGADA DEL BORRADO LOGICO----------------------------------------------------------------
            $table->softDeletes();
            // -------------------------------------------------------------
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historia_clinica');
    }
}

