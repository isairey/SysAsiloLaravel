<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- 1. AÑADIR ESTA LÍNEA

class MedicamentoRecetado extends Model
{
    use HasFactory, SoftDeletes; // <-- 2. AÑADIR 'SoftDeletes' AQUÍ

    protected $table = 'medicamentos_recetados';
    protected $primaryKey = 'id_medicamento_recetado';

    protected $fillable = [
        'id_historia',
        'nombre_medicamento',
        'cantidad_recetada',
        'cantidad_dispensada',
        'valor_unitario',
        'total',
        'id_usuario', // Asegúrate de que esta columna esté en tu tabla
        'id_adulto',
    ];

    protected $casts = [
        'id_historia' => 'integer',
        'id_usuario' => 'integer',
        'id_adulto' => 'integer',
        'cantidad_recetada' => 'integer',
        'cantidad_dispensada' => 'integer',
        'valor_unitario' => 'float',
        'total' => 'float',
    ];

    /**
     * Define la relación con HistoriaClinica.
     * Un medicamento recetado pertenece a una Historia Clínica.
     */
    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class, 'id_historia', 'id_historia');
    }

    /**
     * Define la relación con User (el usuario que lo registró).
     * Un medicamento recetado puede haber sido registrado por un usuario.
     * Asumimos que la tabla de usuarios es 'usuario' y su PK es 'id_usuario'.
     */
    public function usuario()
    {
        // El tercer argumento es la clave primaria de la tabla 'usuario' a la que se hace referencia.
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario'); 
    }

    /**
     * Define la relación con AdultoMayor.
     * Un medicamento recetado pertenece a un Adulto Mayor.
     */
    public function adultoMayor()
    {
        return $this->belongsTo(AdultoMayor::class, 'id_adulto', 'id_adulto');
    }
}

