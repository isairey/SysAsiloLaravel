<?php
// database/migrations/YYYY_MM_DD_HHMMSS_create_permissions_table.php
// Recuerda reemplazar YYYY_MM_DD_HHMMSS con la fecha y hora actual al crear el archivo.
// Puedes generar esta migración con: php artisan make:migration create_permissions_table

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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id(); // ID del permiso
            $table->string('name')->unique(); // Nombre del permiso (ej: adulto_mayor.view)
            $table->string('description')->nullable(); // Descripción del permiso
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};

?>