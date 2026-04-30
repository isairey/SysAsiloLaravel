<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- 1. AÑADIR ESTA LÍNEA

class Intervencion extends Model
{
    use HasFactory, SoftDeletes; // <-- 2. AÑADIR 'SoftDeletes' AQUÍ
    protected $table = 'intervencion';
    protected $primaryKey = 'id_intervencion';

    protected $fillable = [
        'resuelto_descripcion',
        'no_resultado',
        'derivacion_institucion',
        'der_seguimiento_legal',
        'der_seguimiento_psi',
        'der_resuelto_externo',
        'der_noresuelto_externo',
        'abandono_victima',
        'resuelto_conciliacion_jio',
        'fecha_intervencion',
        'id_seg',
    ];

    protected $casts = [
        'fecha_intervencion' => 'date',
    ];

    public function seguimiento()
    {
        return $this->belongsTo(SeguimientoCaso::class, 'id_seg');
    }
}