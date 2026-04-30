<?php
// app/Models/Persona.php
// Si no tienes este modelo, créalo con: php artisan make:model Persona
// Este modelo es referenciado en tu migración de usuario y adulto_mayor.

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// --- 1. AÑADIR LA IMPORTACIÓN DE SOFTDELETES ---
use Illuminate\Database\Eloquent\SoftDeletes;
// --------Eliminacion con logica------------------
use Carbon\Carbon;

class Persona extends Model
{
    use HasFactory, SoftDeletes; //<-- 2. AÑADIR SOFTDELETES AQUÍ -->

    protected $table = 'persona'; // Nombre de la tabla
    protected $primaryKey = 'ci'; // Clave primaria
    public $incrementing = false; // La clave primaria 'ci' no es auto-incremental
    protected $keyType = 'string'; // El tipo de la clave primaria es string
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ci',
        'primer_apellido',
        'segundo_apellido',
        'nombres',
        'sexo',
        'fecha_nacimiento',
        'edad',
        'estado_civil',
        'domicilio',
        'telefono',
        'zona_comunidad',
        'area_especialidad',
        'area_especialidad_legal',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_nacimiento' => 'date',
        'edad' => 'integer',
    ];

    // Accesor para calcular la edad automáticamente si no está seteada
    public function getEdadAttribute($value)
    {
        if ($value === null && $this->fecha_nacimiento) {
            return Carbon::parse($this->fecha_nacimiento)->age;
        }
        return $value;
    }

    // Mutador para calcular la edad al setear la fecha de nacimiento
    public function setFechaNacimientoAttribute($value)
    {
        $this->attributes['fecha_nacimiento'] = $value;
        if ($value) {
            $this->attributes['edad'] = Carbon::parse($value)->age;
        }
    }

    /**
     * El usuario asociado a esta persona (si existe).
     * Una persona puede ser un usuario.
     */
    public function usuario()
    {
        return $this->hasOne(User::class, 'ci', 'ci');
    }

    /**
     * El adulto mayor asociado a esta persona (si existe).
     * Una persona puede ser un adulto mayor.
     */
    public function adultoMayor()
    {
        return $this->hasOne(AdultoMayor::class, 'ci', 'ci');
    }
}
?>