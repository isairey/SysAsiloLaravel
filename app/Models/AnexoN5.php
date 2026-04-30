<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Asegúrate de incluirlo si lo usas
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- 1. AÑADIR ESTA LÍNEA

class AnexoN5 extends Model
{
    use HasFactory, SoftDeletes; // <-- 2. AÑADIR 'SoftDeletes' AQUÍ
    protected $table = 'anexo_n5';
    protected $primaryKey = 'nro_an5'; // Clave primaria personalizada
    
    // Si la clave primaria no es un entero autoincremental, debes indicarlo:
    // public $incrementing = true; // Si nro_an5 es un ID autoincremental
    // protected $keyType = 'int'; // Si nro_an5 es un entero

    protected $fillable = [
        'numero',
        'fecha',
        'accion_realizada',
        'resultado_obtenido',
        'id_usuario',
        'id_adulto', // ¡AGREGADO!
    ];
    protected $casts = [
        'fecha' => 'date', // <-- ¡AÑADIDO ESTO! Esto convierte 'fecha' a un objeto Carbon
    ];
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
    // Si 'usuarios()' es una relación muchos a muchos, necesitarías una tabla pivot 'usuario_anexo5'
    // La dejaremos tal cual la tienes, asumiendo su propósito actual o futuro.
    public function usuarios()
    {
        return $this->belongsToMany(\App\Models\User::class, 'usuario_anexo5', 'nro_an5', 'id_usuario')
                    ->withTimestamps();
    }

    // Agrega esta relación para vincular AnexoN5 con AdultoMayor
    public function adulto()
    {
        return $this->belongsTo(AdultoMayor::class, 'id_adulto');
    }
}
