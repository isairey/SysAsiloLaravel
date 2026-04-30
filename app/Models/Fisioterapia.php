<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fisioterapia extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fisioterapia';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'cod_fisio';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_adulto',
        'id_historia',
        'id_usuario',
        'num_emergencia',
        'enfermedades_actuales',
        'alergias',
        'fecha_programacion',
        'fecha_inicio',
        'fecha_fin',
        'numero_sesiones',
        'motivo_consulta',
        'solicitud_atencion',
        'equipos',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_programacion' => 'date',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the adult mayor that owns the physiotherapy record.
     */
    public function adulto()
    {
        return $this->belongsTo(AdultoMayor::class, 'id_adulto', 'id_adulto');
    }

    /**
     * Get the clinical history associated with the physiotherapy record.
     */
    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class, 'id_historia', 'id_historia');
    }

    /**
     * Get the user who created the physiotherapy record.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }
}
