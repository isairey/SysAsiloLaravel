<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- 1. AÑADIR ESTA LÍNEA

class Enfermeria extends Model
{
    use HasFactory, SoftDeletes; // <-- 2. AÑADIR 'SoftDeletes' AQUÍ

    protected $table = 'enfermeria'; // Nombre de la tabla
    protected $primaryKey = 'cod_enf'; // Clave primaria, según tu imagen

    protected $fillable = [
        'id_adulto',
        'presion_arterial',
        'frecuencia_cardiaca',
        'frecuencia_respiratoria',
        'pulso',
        'temperatura',
        'control_oximetria',
        'inyectables',
        'peso_talla',
        'orientacion_alimentacion',
        'lavado_oidos',
        'orientacion_tratamiento',
        'curacion',
        'adm_medicamentos',
        'derivacion', // CAMBIO: Nueva columna 'derivacion'
        'id_usuario',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Define la relación con AdultoMayor.
     * Una ficha de enfermería pertenece a un Adulto Mayor.
     */
    public function adulto()
    {
        return $this->belongsTo(AdultoMayor::class, 'id_adulto', 'id_adulto');
    }

    /**
     * Define la relación con el usuario que creó/modificó la ficha.
     * Asumimos que la tabla de usuarios es 'usuario' y su PK es 'id_usuario',
     * y que el modelo User tiene una relación con Persona.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    // CAMBIO: Se elimina la relación 'servicio()' ya que la columna id_servicio fue removida.
    // public function servicio()
    // {
    //     return $this->belongsTo(Servicio::class, 'id_servicio', 'id_servicio');
    // }
}
