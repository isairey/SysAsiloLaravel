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
        Schema::create('anexo_n3', function (Blueprint $table) {
            $table->id('nro_an3');
            // FK:
            $table->unsignedBigInteger('id_natural');
            $table->foreign('id_natural')->references('id_natural')->on('persona_natural')->onDelete('cascade');
            $table->unsignedBigInteger('id_adulto');
            $table->foreign('id_adulto')->references('id_adulto')->on('adulto_mayor')->onDelete('cascade');
            // KK
            $table->enum('sexo', ['M', 'F']);
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
        Schema::dropIfExists('anexo_n3');
    }
};
