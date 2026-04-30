<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('persona_juridica', function (Blueprint $table) {
            $table->id('id_juridica');
            $table->unsignedBigInteger('id_encargado');
            $table->string('nombre_institucion', 255);
            $table->string('direccion', 255);
            $table->string('telefono_juridica', 20);
            $table->string('nombre_funcionario', 255);
            // ---ESTA LINEA ES LA ENCARGADA DEL BORRADO LOGICO----------------------------------------------------------------
            $table->softDeletes();
            // -------------------------------------------------------------------------------------------
           
            $table->foreign('id_encargado')->references('id_encargado')->on('encargado')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persona_juridica');
    }
};
