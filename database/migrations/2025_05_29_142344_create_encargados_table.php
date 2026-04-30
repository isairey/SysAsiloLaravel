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
        Schema::create('encargado', function (Blueprint $table) {
    $table->id('id_encargado');
    $table->unsignedBigInteger('id_adulto');
    $table->string('tipo_encargado', 100); // Ej: tutor, responsable médico, etc.
    $table->timestamps();
      // ---ESTA LINEA ES LA ENCARGADA DEL BORRADO LOGICO----------------------------------------------------------------
     $table->softDeletes();
        // -------------------------------------------------------------------------------------------
           
    $table->foreign('id_adulto')->references('id_adulto')->on('adulto_mayor')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encargado');
    }
};
