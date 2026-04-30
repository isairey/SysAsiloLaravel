<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Ejecutar el comando para desbloquear usuarios cada minuto
        // Esto asegura que los usuarios sean desbloqueados tan pronto como expire su tiempo
        $schedule->command('users:unlock-temporary')
                 ->everyMinute()
                 ->withoutOverlapping() // Evitar ejecuciones simultáneas
                 ->runInBackground(); // Ejecutar en segundo plano
                 
        // Alternativamente, si prefieres ejecutarlo cada 5 minutos:
        // $schedule->command('users:unlock-temporary')->everyFiveMinutes();
        
        // También puedes agregar otros comandos de mantenimiento
        $schedule->command('auth:clear-resets')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}