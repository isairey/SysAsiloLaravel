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
        Schema::create('medicamentos_recetados', function (Blueprint $table) {
            $table->id('id_medicamento_recetado'); // Primary key para los medicamentos
            $table->string('nombre_medicamento')->nullable();
            $table->integer('cantidad_recetada')->nullable();
            $table->integer('cantidad_dispensada')->nullable();
            $table->decimal('valor_unitario', 8, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            
            // Campos de auditoría (quienes modificaron los registros)
            $table->unsignedBigInteger('id_historia');
            $table->foreign('id_historia')->references('id_historia')->on('historia_clinica')->onDelete('cascade');
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuario')->onDelete('cascade'); // <-- Importante
            $table->unsignedBigInteger('id_adulto');
            $table->foreign('id_adulto')->references('id_adulto')->on('adulto_mayor')->onDelete('cascade');

            $table->timestamps(); // created_at, updated_at
            // ---ESTA LINEA ES LA ENCARGADA DEL BORRADO LOGICO----------------------------------------------------------------
            $table->softDeletes();
            // -----------------------------------------------------------------
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicamentos_recetados');
    }
};

