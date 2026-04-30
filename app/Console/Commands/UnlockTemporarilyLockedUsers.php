<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UnlockTemporarilyLockedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:unlock-temporary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unlock users whose temporary lockout period (due to failed login attempts) has expired after 10 minutes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Iniciando desbloqueo de usuarios temporalmente bloqueados...');
        Log::info('UnlockTemporarilyLockedUsers: Proceso iniciado.');

        // Buscar usuarios que:
        // 1. Están inactivos (active = false)
        // 2. Tienen tiempo de bloqueo temporal definido
        // 3. Han pasado los 10 minutos de bloqueo
        // 4. NO fueron desactivados manualmente por admin
        $lockedUsers = User::where('active', false)
                            ->whereNotNull('temporary_lockout_until')
                            ->where('temporary_lockout_until', '<=', Carbon::now())
                            ->whereNotNull('login_attempts') // Solo los bloqueados por intentos fallidos
                            ->get();

        if ($lockedUsers->isEmpty()) {
            $this->info('No hay usuarios para desbloquear en este momento.');
            Log::info('UnlockTemporarilyLockedUsers: No se encontraron usuarios para desbloquear.');
            return Command::SUCCESS;
        }

        $unlockedCount = 0;
        $skippedCount = 0;

        foreach ($lockedUsers as $user) {
            try {
                // Verificar que realmente hayan pasado 10 minutos desde el último intento fallido
                $lockoutTime = Carbon::parse($user->temporary_lockout_until);
                $timeSinceLockout = Carbon::now()->diffInMinutes($lockoutTime);
                
                // Solo desbloquear si han pasado al menos 10 minutos
                if (Carbon::now()->gte($lockoutTime)) {
                    // Reactivar usuario
                    $user->active = true;
                    $user->login_attempts = 0;
                    $user->temporary_lockout_until = null;
                    $user->last_failed_login_at = null;
                    $user->save();

                    $roleName = $user->role_name;
                    $personaInfo = $user->full_name;

                    $this->info("✓ Usuario desbloqueado: {$personaInfo} (Rol: {$roleName})");
                    Log::info("UnlockTemporarilyLockedUsers: Usuario ID {$user->id_usuario} ({$personaInfo}) desbloqueado exitosamente.");
                    
                    $unlockedCount++;
                } else {
                    $this->warn("⚠ Usuario ID: {$user->id_usuario} aún no cumple los 10 minutos de bloqueo.");
                    $skippedCount++;
                }
                
            } catch (\Exception $e) {
                $this->error("✗ Error al desbloquear usuario ID: {$user->id_usuario} - {$e->getMessage()}");
                Log::error("UnlockTemporarilyLockedUsers: Error al desbloquear usuario ID {$user->id_usuario}: {$e->getMessage()}");
                $skippedCount++;
            }
        }

        // Resumen del proceso
        $this->info("Proceso completado:");
        $this->info("- Usuarios desbloqueados: {$unlockedCount}");
        if ($skippedCount > 0) {
            $this->warn("- Usuarios omitidos: {$skippedCount}");
        }
        
        Log::info("UnlockTemporarilyLockedUsers: Proceso finalizado. Desbloqueados: {$unlockedCount}, Omitidos: {$skippedCount}");
        
        return Command::SUCCESS;
    }
}