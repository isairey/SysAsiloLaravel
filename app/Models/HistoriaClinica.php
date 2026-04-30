<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- 1. AÑADIR ESTA LÍNEA


class HistoriaClinica extends Model
{
    use HasFactory, SoftDeletes; // <-- 2. AÑADIR 'SoftDeletes' AQUÍ

    protected $table = 'historia_clinica';
    protected $primaryKey = 'id_historia';

    protected $fillable = [
        'municipio_nombre',
        'establecimiento',
        'antecedentes_personales',
        'antecedentes_familiares',
        'estado_actual',
        'tipo_consulta',
        'ocupacion',
        'grado_instruccion',
        'lugar_nacimiento_provincia',
        'lugar_nacimiento_departamento',
        'domicilio_actual',
        'id_usuario',
        'id_adulto',
    ];

    /**
     * Relación con el Adulto Mayor.
     */
    public function adulto()
    {
        return $this->belongsTo(AdultoMayor::class, 'id_adulto', 'id_adulto');
    }

    /**
     * Relación con el Usuario (quien registró la historia clínica).
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Relación uno a muchos con ExamenComplementario.
     */
    public function examenesComplementarios()
    {
        return $this->hasMany(ExamenComplementario::class, 'id_historia', 'id_historia');
    }
        public function medicamentosRecetados()
    {
        return $this->hasMany(MedicamentoRecetado::class, 'id_historia', 'id_historia');
    }
}

