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
        Schema::create('croquis', function (Blueprint $table) {
            $table->id('id_referencia'); // <- clave primaria personalizada
            $table->string('nombre_denunciante');
            $table->string('apellidos_denunciante');
            $table->string('ci_denunciante', 20);
            // Nueva columna para la ruta de la imagen del croquis, puede ser nula.
            $table->string('ruta_imagen')->nullable();
            $table->unsignedBigInteger('id_adulto');
            $table->foreign('id_adulto')->references('id_adulto')->on('adulto_mayor')->onDelete('cascade');
            $table->timestamps();
             // ---ESTA LINEA ES LA ENCARGADA DEL BORRADO LOGICO----------------------------------------------------------------
            $table->softDeletes();
            // -------------------------------------------------------------------------------------------
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('croquis');
    }
};
