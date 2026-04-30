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
        Schema::create('adulto_mayor', function (Blueprint $table) {
            $table->id('id_adulto');
            $table->string('ci'); // Clave foránea hacia persona
            $table->text('discapacidad')->nullable();
            $table->string('vive_con', 200)->nullable();
            $table->boolean('migrante')->default(false);
            
            // =========================================================================
            // === NUEVO CAMPO AÑADIDO ===
            // =========================================================================
            $table->string('origen_migracion', 255)->nullable()->comment('Lugar de origen si el adulto mayor es migrante');
            
            $table->string('nro_caso', 50)->unique()->nullable();
            $table->date('fecha');
            $table->timestamps();
            
            // ---ESTA LINEA ES LA ENCARGADA DEL BORRADO LOGICO----------------------------------------------------------------
            $table->softDeletes();
            // -------------------------------------------------------------------------------------------
            // Clave foránea
            $table->foreign('ci')->references('ci')->on('persona')->onDelete('cascade');
            
            // Índices
            $table->index('nro_caso');
            $table->index('fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adulto_mayor');
    }
};
