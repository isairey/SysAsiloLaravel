<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; //<--Nuevo
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- 1. AÑADIR ESTA LÍNEA

class ActividadLaboral extends Model
{
    use HasFactory, SoftDeletes; // <-- 2. AÑADIR 'SoftDeletes' AQUÍ
    protected $table = 'actividad_laboral';
    protected $primaryKey = 'id_act_lab';

    protected $fillable = [
        'nombre_actividad',
        'direccion_trabajo',
        'horario',
        'horas_x_dia',
        'rem_men_aprox',
        'telefono_laboral',
        'id_adulto',
    ];

    public function adulto()
    {
        return $this->belongsTo(AdultoMayor::class, 'id_adulto');
    }
}
