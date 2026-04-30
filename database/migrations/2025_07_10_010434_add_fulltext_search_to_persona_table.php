<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('persona', function (Blueprint $table) {
            // Añadir un índice normal a la columna CI para acelerar búsquedas exactas.
            // Aunque es PK, si se busca con LIKE '123%', esto puede ayudar.
            $table->index('ci');

            // Determinar el motor de la base de datos para la sintaxis correcta.
            $driver = DB::connection()->getDriverName();

            if ($driver === 'mysql') {
                // Sintaxis para MySQL: Crear un índice FULLTEXT.
                // Se combina nombres y apellidos para buscar en todos a la vez.
                DB::statement('ALTER TABLE persona ADD FULLTEXT fulltext_persona_nombre_completo(nombres, primer_apellido, segundo_apellido)');
            }
            // Para PostgreSQL, la sintaxis es diferente y se haría con GIN o GIST indexes,
            // pero la lógica del controlador que usaremos funcionará para ambos.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('persona', function (Blueprint $table) {
            $table->dropIndex(['ci']);
            
            $driver = DB::connection()->getDriverName();
            if ($driver === 'mysql') {
                // Eliminar el índice FULLTEXT si la migración se revierte.
                DB::statement('ALTER TABLE persona DROP INDEX fulltext_persona_nombre_completo');
            }
        });
    }
};
