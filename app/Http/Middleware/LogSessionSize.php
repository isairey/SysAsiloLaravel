<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * ==================================================================================
 * Middleware de Diagnóstico de Sesión - ¡SOLO PARA DEPURACIÓN!
 * ==================================================================================
 *
 * Propósito:
 * Este middleware se ha creado para diagnosticar un problema de sobrecarga de sesión.
 * En cada petición HTTP, inspeccionará los datos de la sesión, calculará su tamaño
 * en kilobytes y registrará esta información en el archivo de logs de Laravel.
 *
 * Cómo usarlo:
 * 1.  Crea este archivo en `app/Http/Middleware/LogSessionSize.php`.
 * 2.  Regístralo en el kernel HTTP (`app/Http/Kernel.php`) dentro del grupo 'web'.
 * 3.  Navega por tu aplicación, especialmente en las secciones donde sospechas que
 * ocurre el problema.
 * 4.  Revisa el archivo `storage/logs/laravel.log` para ver cómo crece la sesión.
 * Busca entradas con el prefijo "[SESSION_SIZE_DIAGNOSIS]".
 *
 * ¡IMPORTANTE!
 * Una vez que hayas resuelto el problema, DEBES ELIMINAR este middleware del kernel HTTP.
 * Dejarlo en producción ralentizará tu aplicación innecesariamente.
 *
 */
class LogSessionSize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Permite que la petición se complete primero para que la sesión se actualice.
        $response = $next($request);

        // Solo procede si la sesión ha sido iniciada.
        if ($request->hasSession()) {
            $sessionData = $request->session()->all();

            // Serializa los datos de la sesión para obtener un tamaño realista.
            $serializedData = serialize($sessionData);
            // Calcula el tamaño en bytes y luego lo convierte a kilobytes.
            $sizeInKilobytes = strlen($serializedData) / 1024;

            // Formatea el tamaño a 2 decimales para una mejor lectura.
            $formattedSize = round($sizeInKilobytes, 2);

            // Prepara el mensaje de log.
            $logMessage = sprintf(
                "[SESSION_SIZE_DIAGNOSIS] URL: %s | Tamaño: %s KB",
                $request->fullUrl(),
                $formattedSize
            );

            // Registra el mensaje principal en los logs.
            Log::info($logMessage);

            // Si el tamaño es sospechosamente grande (ej. > 5KB), registra también el contenido.
            // Esto te ayudará a ver exactamente qué datos están causando el problema.
            // Puedes ajustar este umbral según sea necesario.
            if ($formattedSize > 5) {
                Log::warning('[SESSION_SIZE_DIAGNOSIS] ¡Tamaño de sesión excesivo! Contenido: ', [
                    // Usamos json_encode con pretty print para que sea más fácil de leer en el log.
                    'data' => json_decode(json_encode($sessionData), true)
                ]);
            }
        }

        return $response;
    }
}
