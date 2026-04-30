<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- 1. AÑADIR ESTA LÍNEA

class GrupoFamiliar extends Model
{
    use HasFactory, SoftDeletes; // <-- 2. AÑADIR 'SoftDeletes' AQUÍ
    protected $table = 'grupo_familiar';
    protected $primaryKey = 'id_familiar';

    protected $fillable = [
        'apellido_paterno',
        'apellido_materno',
        'nombres',
        'parentesco',
        'edad',
        'ocupacion',
        'direccion',
        'telefono',
        'id_adulto',
    ];

    public function adulto()
    {
        return $this->belongsTo(AdultoMayor::class, 'id_adulto');
    }
}
