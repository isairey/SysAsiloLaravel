<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <-- Nuevo
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- 1. AÑADIR ESTA LÍNEA

class Encargado extends Model
{
    use HasFactory, SoftDeletes; // <-- 2. AÑADIR 'SoftDeletes' AQUÍ
    protected $table = 'encargado';
    protected $primaryKey = 'id_encargado';

    protected $fillable = [
        'id_adulto',
        'tipo_encargado',
    ];

    public function adulto()
    {
        return $this->belongsTo(AdultoMayor::class, 'id_adulto');
    }
    public function personaNatural()
    {
        return $this->hasOne(PersonaNatural::class, 'id_encargado');
    }

    public function personaJuridica()
    {
        return $this->hasOne(PersonaJuridica::class, 'id_encargado');
    }
}