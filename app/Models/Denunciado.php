<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- 1. AÑADIR ESTA LÍNEA

class Denunciado extends Model
{
    use HasFactory, SoftDeletes; // <-- 2. AÑADIR 'SoftDeletes' AQUÍ
    protected $table = 'denunciado';
    protected $primaryKey = 'id_denunciado';

    protected $fillable = [
        'id_natural',
        'sexo',
        'descripcion_hechos',
        'id_adulto'
    ];

    public function personaNatural()
    {
        return $this->belongsTo(\App\Models\PersonaNatural::class, 'id_natural');
    }
    public function adulto()
    {
        return $this->belongsTo(AdultoMayor::class, 'id_adulto');
    }
}
