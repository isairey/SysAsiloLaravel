<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // <-- Nuevo
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- 1. AÑADIR ESTA LÍNEA

class Croquis extends Model
{
    use HasFactory, SoftDeletes; // <-- 2. AÑADIR 'SoftDeletes' AQUÍ
    protected $table = 'croquis';
    protected $primaryKey = 'id_referencia';

    protected $fillable = [
        'nombre_denunciante',
        'apellidos_denunciante',
        'ci_denunciante',
        'id_adulto',
    ];

    public function adulto()
    {
        return $this->belongsTo(AdultoMayor::class, 'id_adulto');
    }
}
