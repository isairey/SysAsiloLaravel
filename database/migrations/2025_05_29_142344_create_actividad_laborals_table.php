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
        Schema::create('actividad_laboral', function (Blueprint $table) {
            $table->id('id_act_lab');
            // 'nombre_actividad' ahora es nullable
            $table->string('nombre_actividad', 255)->nullable();
            $table->string('direccion_trabajo', 255)->nullable();
            $table->string('horario', 50)->nullable();
            $table->string('horas_x_dia', 50)->nullable();
            $table->string('rem_men_aprox', 100)->nullable();
            $table->string('telefono_laboral', 20)->nullable();
            $table->unsignedBigInteger('id_adulto'); // Foreign key
            $table->timestamps();

           // ---ESTA LINEA ES LA ENCARGADA DEL BORRADO LOGICO----------------------------------------------------------------
            $table->softDeletes();
            // -------------------------------------------------------------------------------------------
           

            // Definición de la clave foránea
            $table->foreign('id_adulto')
                ->references('id_adulto')
                ->on('adulto_mayor')
                ->onDelete('cascade'); // Si el adulto mayor es eliminado, también se elimina su actividad laboral.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actividad_laboral');
    }
};
