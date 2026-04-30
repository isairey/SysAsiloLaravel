<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'rol';
    protected $primaryKey = 'id_rol';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'nombre_rol',
        'descripcion',
        'active',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id_rol', 'id_rol');
    }

    /**
     * Los permisos asignados a este rol.
     *
     * CORRECCIÓN VITAL: Se especifican todos los parámetros de la relación belongsToMany.
     * Laravel necesita esto porque tus claves primarias no son 'id'.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'permission_role', // 1. Nombre de la tabla pivote
            'role_id',         // 2. Clave foránea de este modelo (Rol) en la tabla pivote
            'permission_id',   // 3. Clave foránea del otro modelo (Permission) en la tabla pivote
            'id_rol',          // 4. Clave primaria de este modelo (Rol)
            'id'               // 5. Clave primaria del otro modelo (Permission)
        );
    }

    /**
     * Verifica si el rol tiene un permiso específico.
     * (Esta función ya era correcta, pero ahora funcionará gracias a la relación corregida)
     */
    public function hasPermissionTo(string $permissionName): bool
    {
        // Se utiliza la relación 'permissions' que ahora está correctamente definida.
        return $this->permissions()->where('name', $permissionName)->exists();
    }
}
