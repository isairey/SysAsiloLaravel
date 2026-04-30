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
        Schema::create('usuario', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('ci'); // Clave foránea hacia persona
            $table->unsignedBigInteger('id_rol'); // Clave foránea hacia rol
            $table->string('password');
            $table->boolean('active')->default(true); // Estado del usuario
            $table->integer('login_attempts')->default(0); // Contador de intentos de login
            $table->timestamp('last_failed_login_at')->nullable(); // Último intento fallido
            $table->timestamp('temporary_lockout_until')->nullable(); // Hasta cuándo está bloqueado
            $table->rememberToken();
            $table->timestamps();
            
            // ---ESTA LINEA ES LA ENCARGADA DEL BORRADO LOGICO----------------------------------------------------------------
            $table->softDeletes();
            // -------------------------------------------------------------------------------------------
           
            // Claves foráneas
            $table->foreign('ci')->references('ci')->on('persona')->onDelete('cascade');
            $table->foreign('id_rol')->references('id_rol')->on('rol')->onDelete('cascade');
            
            // Índices
            $table->unique('ci'); // Un usuario por persona
            $table->index('active');
            $table->index('temporary_lockout_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};