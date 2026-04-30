<?php
// database/migrations/YYYY_MM_DD_HHMMSS_create_permission_role_table.php
// Recuerda reemplazar YYYY_MM_DD_HHMMSS con la fecha y hora actual al crear el archivo.
// Puedes generar esta migración con: php artisan make:migration create_permission_role_table

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
        Schema::create('permission_role', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id'); // Coincide con el tipo de id_rol en tu tabla 'rol'

            // Claves foráneas
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            // Asegúrate que 'id_rol' es la clave primaria en tu tabla 'rol' y que 'rol' es el nombre correcto de la tabla.
            $table->foreign('role_id')->references('id_rol')->on('rol')->onDelete('cascade'); 

            // Clave primaria compuesta
            $table->primary(['permission_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_role');
    }
};
?>