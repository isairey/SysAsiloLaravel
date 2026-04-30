<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    protected $table = 'permissions';
    public $timestamps = false; // Tu tabla 'permissions' no tiene timestamps

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Los roles que tienen este permiso.
     * CORRECCIÓN VITAL: Se añaden los dos últimos parámetros a la relación belongsToMany.
     * - 'id': Es la clave primaria de este modelo (Permission).
     * - 'id_rol': Es la clave primaria del modelo relacionado (Rol).
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Rol::class,
            'permission_role', // Tabla pivote
            'permission_id',   // Clave foránea de Permission en la tabla pivote
            'role_id',         // Clave foránea de Rol en la tabla pivote
            'id',              // Clave primaria de este modelo (Permission)
            'id_rol'           // Clave primaria del modelo relacionado (Rol)
        );
    }
}
