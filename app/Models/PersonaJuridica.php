<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- 1. AÑADIR ESTA LÍNEA

class PersonaJuridica extends Model
{
    use HasFactory, SoftDeletes; // <-- 2. AÑADIR 'SoftDeletes' AQUÍ

    protected $table = 'persona_juridica';
    protected $primaryKey = 'id_juridica';
    public $timestamps = false;

    protected $fillable = [
        'id_encargado',
        'nombre_institucion',
        'direccion',
        'telefono_juridica',
        'nombre_funcionario',
    ];

    public function encargado()
    {
        return $this->belongsTo(Encargado::class, 'id_encargado');
    }
}
