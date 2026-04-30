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
        Schema::create('denunciado', function (Blueprint $table) {
            $table->id('id_denunciado');
            $table->unsignedBigInteger('id_natural');
            $table->enum('sexo', ['M', 'F']);
            $table->text('descripcion_hechos');
            $table->foreign('id_natural')->references('id_natural')->on('persona_natural')->onDelete('cascade');
            $table->unsignedBigInteger('id_adulto');
            $table->foreign('id_adulto')->references('id_adulto')->on('adulto_mayor')->onDelete('cascade');
            $table->timestamps();
            // ---ESTA LINEA ES LA ENCARGADA DEL BORRADO LOGICO----------------------------------------------------------------
            $table->softDeletes();
            // ---
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denunciado');
    }
};