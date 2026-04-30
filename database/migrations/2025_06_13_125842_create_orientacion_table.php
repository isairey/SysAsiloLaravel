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
        Schema::create('orientacion', function (Blueprint $table) { // Nombre de la tabla cambiado a 'orientacion'
            $table->id('cod_or'); // Primary key, auto-incrementing
            $table->date('fecha_ingreso');
            $table->unsignedBigInteger('id_adulto'); // Foreign key to Adulto Mayor
            $table->enum('tipo_orientacion', ['psicologica', 'social', 'legal']);
            $table->text('motivo_orientacion');
            $table->text('resultado_obtenido')->nullable();
            $table->unsignedBigInteger('id_usuario')->nullable(); // Assuming id_usuario refers to the user who registered the orientation
            $table->timestamps(); // created_at and updated_at
           // ---ESTA LINEA ES LA ENCARGADA DEL BORRADO LOGICO----------------------------------------------------------------
            $table->softDeletes();
            // -----------------------------------------------------------------
            // Foreign key constraint
            $table->foreign('id_adulto')->references('id_adulto')->on('adulto_mayor')->onDelete('cascade');
            // Assuming 'users' table for id_usuario, uncomment if you use it
            // $table->foreign('id_usuario')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orientacion'); // Nombre de la tabla cambiado a 'orientacion'
    }
};
