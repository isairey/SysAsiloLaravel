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
        Schema::create('grupo_familiar', function (Blueprint $table) {
            $table->id('id_familiar');
            $table->string('apellido_paterno', 100); // Añadido longitud
            $table->string('apellido_materno', 100)->nullable(); // HECHO NULLABLE
            $table->string('nombres', 255); // Añadido longitud
            $table->string('parentesco', 100); // Añadido longitud
            $table->integer('edad');
            $table->string('ocupacion', 100)->nullable(); // HECHO NULLABLE
            $table->string('direccion', 255)->nullable(); // Cambiado a string y HECHO NULLABLE
            $table->string('telefono', 20)->nullable(); // HECHO NULLABLE
            $table->unsignedBigInteger('id_adulto');
            $table->foreign('id_adulto')->references('id_adulto')->on('adulto_mayor')->onDelete('cascade');
            $table->timestamps(); // ¡AGREGADO: columnas created_at y updated_at!
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
        Schema::dropIfExists('grupo_familiar');
    }
};
