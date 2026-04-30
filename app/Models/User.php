<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// --- 1. AÑADIR LA IMPORTACIÓN DE SOFTDELETES ---
use Illuminate\Database\Eloquent\SoftDeletes;
// --------Eliminacion con logica------------------
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes; //<-- 2. AÑADIR SOFTDELETES AQUÍ -->

    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        // NOTA: Tu migración usa 'ci' para la relación. 'id_persona' no existe en la tabla usuario.
        'ci',
        'id_rol',
        'password',
        'active',
        'login_attempts',
        'last_failed_login_at',
        'temporary_lockout_until',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'active' => 'boolean',
        'login_attempts' => 'integer',
        'last_failed_login_at' => 'datetime',
        'temporary_lockout_until' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    // TODO: Descomentar estas relaciones cuando creemos los modelos correspondientes
    
    // Relación con la tabla Persona (usando CI como clave foránea)
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'ci', 'ci');
    }

    // Relación con la tabla Rol
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

// Nuevo --------------------------------------------------
   public function hasPermission(string $permissionName): bool
    {
        if (!$this->rol) {
            return false;
        }
        $this->rol->loadMissing('permissions');
        return $this->rol->permissions->contains('name', $permissionName);
    }

    public function hasRole(string $roleName): bool
    {
        return strtolower(optional($this->rol)->nombre_rol) === strtolower($roleName);
    }

    //-----------------------------------------------------
    
    // Relaciones para seguimientos y anexos
    public function seguimientosCaso()
    {
        return $this->hasMany(SeguimientoCaso::class, 'id_usuario', 'id_usuario');
    }

    public function anexosN5()
    {
        return $this->hasMany(AnexoN5::class, 'id_usuario', 'id_usuario');
    }

    // public function usuarioAnexo5()
    // {
    //     return $this->hasMany(UsuarioAnexo5::class, 'id_usuario', 'id_usuario');
    // }

    public function anexos5()
    {
        return $this->belongsToMany(AnexoN5::class, 'usuario_anexo5', 'id_usuario', 'nro_an5')
                    ->withTimestamps();
    }


    // Métodos para manejo de intentos de login
    public function incrementLoginAttempts()
    {
        $this->login_attempts = ($this->login_attempts ?? 0) + 1;
        $this->last_failed_login_at = Carbon::now();

        if ($this->login_attempts >= 3) {
            $this->temporary_lockout_until = Carbon::now()->addMinutes(10);
        }

        $this->save();
    }

    public function resetLoginAttempts()
    {
        $this->login_attempts = 0;
        $this->last_failed_login_at = null;
        $this->temporary_lockout_until = null;
        $this->save();
    }

    public function isTemporarilyLocked()
    {
        if ($this->temporary_lockout_until && Carbon::now()->lt($this->temporary_lockout_until)) {
            return true;
        }
        return false;
    }

    public function canLogin()
    {
        // No puede loguearse si está inactivo
        if (!$this->active) {
            return false;
        }

        // No puede loguearse si está temporalmente bloqueado
        if ($this->isTemporarilyLocked()) {
            return false;
        }

        return true;
    }

    public function getTimeUntilUnlock()
    {
        if ($this->temporary_lockout_until && Carbon::now()->lt($this->temporary_lockout_until)) {
            return Carbon::now()->diffInMinutes($this->temporary_lockout_until, false);
        }
        return 0;
    }

    // Scope para usuarios activos
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    // Scope para usuarios bloqueados temporalmente
    public function scopeTemporarilyLocked($query)
    {
        return $query->where('active', false)
                    ->whereNotNull('temporary_lockout_until')
                    ->where('temporary_lockout_until', '>', Carbon::now());
    }

    // Obtener nombre completo del usuario (temporalmente usando CI)
    public function getFullNameAttribute()
    {
        // Esta función ahora funcionará correctamente.
        if ($this->persona) {
            return trim("{$this->persona->nombres} {$this->persona->primer_apellido} {$this->persona->segundo_apellido}");
        }
        return "Usuario CI: {$this->ci}";
    }

    
    // Obtener nombre del rol (temporalmente usando ID de rol)
    public function getRoleNameAttribute()
{
    if ($this->rol) {
        return strtolower($this->rol->nombre_rol);
    }
    
    // Fallback temporal
    $roles = [
        1 => 'admin',
        2 => 'responsable', 
        3 => 'legal',
    ];
    
    return $roles[$this->id_rol] ?? 'sin-rol';
}


}