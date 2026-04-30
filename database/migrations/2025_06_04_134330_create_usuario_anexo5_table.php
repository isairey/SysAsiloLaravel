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
        Schema::create('usuario_anexo5', function (Blueprint $table) {
        $table->id('usuario_an5'); // PK personalizada
        $table->unsignedBigInteger('id_usuario');
        $table->foreign('id_usuario')->references('id_usuario')->on('usuario')->onDelete('cascade');
        $table->unsignedBigInteger('nro_an5');
        $table->foreign('nro_an5')->references('nro_an5')->on('anexo_n5')->onDelete('cascade');
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
        Schema::dropIfExists('usuario_anexo5');
    }
};
