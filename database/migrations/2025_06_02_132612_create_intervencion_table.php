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
        Schema::create('intervencion', function (Blueprint $table) {
            $table->id('id_intervencion');
            $table->text('resuelto_descripcion')->nullable();
            $table->string('no_resultado')->nullable();
            $table->string('derivacion_institucion')->nullable();
            $table->string('der_seguimiento_legal')->nullable();
            $table->string('der_seguimiento_psi')->nullable();
            $table->string('der_resuelto_externo')->nullable();
            $table->string('der_noresuelto_externo')->nullable();
            $table->string('abandono_victima')->nullable();
            $table->string('resuelto_conciliacion_jio')->nullable();
            $table->date('fecha_intervencion');
            // FK:
            $table->unsignedBigInteger('id_seg');
            $table->foreign('id_seg')->references('id_seg')->on('seguimiento_caso')->onDelete('cascade');
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
        Schema::dropIfExists('intervencion');
    }
};
