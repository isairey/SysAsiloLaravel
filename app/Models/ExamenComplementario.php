<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- 1. AÑADIR ESTA LÍNEA

class ExamenComplementario extends Model
{
    use HasFactory, SoftDeletes; // <-- 2. AÑADIR 'SoftDeletes' AQUÍ

    protected $table = 'examen_complementario';
    protected $primaryKey = 'id_examen';

    protected $fillable = [
        'id_historia',
        'presion_arterial',
        'temperatura',
        'peso_corporal',
        'resultado_prueba',
        'diagnostico',
        'id_usuario',
        'id_adulto',
        // Las columnas de medicamentos han sido movidas a MedicamentoRecetado
    ];

    /**
     * Define la relación con HistoriaClinica.
     * Un examen complementario pertenece a una Historia Clínica.
     */
    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class, 'id_historia', 'id_historia');
    }

    /**
     * Define la relación con User (el usuario que lo registró).
     * Un examen complementario puede haber sido registrado por un usuario.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }

    /**
     * Define la relación con AdultoMayor.
     * Un examen complementario pertenece a un Adulto Mayor.
     */
    public function adultoMayor()
    {
        return $this->belongsTo(AdultoMayor::class, 'id_adulto', 'id_adulto');
    }

    // Ya no es necesario definir relación belongsTo MedicamentoRecetado aquí si no tiene sentido lógico
    // Y la relación hasMany con medicamentos_recetados irá en HistoriaClinica
}

