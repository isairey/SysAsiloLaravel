<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- 1. AÑADIR ESTA LÍNEA

class Kinesiologia extends Model
{
    use HasFactory, SoftDeletes; // <-- 2. AÑADIR 'SoftDeletes' AQUÍ

    // Nombre de la tabla asociada al modelo (singular, según tu migración)
    protected $table = 'kinesiologia';

    // Clave primaria de la tabla
    protected $primaryKey = 'cod_kine';

    // Atributos que se pueden asignar masivamente
    protected $fillable = [
        'id_adulto',
        'id_historia',
        'id_usuario', // Cambiado a id_usuario
        'entrenamiento_funcional',
        'gimnasio_maquina',
        'aquafit',
        'hidroterapia',
        'manana',
        'tarde',
        // No hay 'observaciones' o 'motivo_consulta' en tu migración final, los he quitado del fillable.
        // Si los necesitas, deberías añadirlos a la migración primero.
    ];

    // Atributos que deben ser convertidos a tipos nativos
    protected $casts = [
        'entrenamiento_funcional' => 'boolean',
        'gimnasio_maquina' => 'boolean',
        'aquafit' => 'boolean',
        'hidroterapia' => 'boolean',
        'manana' => 'boolean',
        'tarde' => 'boolean',
    ];

    /**
     * Obtiene el adulto mayor asociado a la ficha de kinesiología.
     */
    public function adulto()
    {
        return $this->belongsTo(AdultoMayor::class, 'id_adulto', 'id_adulto');
    }

    /**
     * Obtiene la historia clínica asociada a la ficha de kinesiología.
     */
    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class, 'id_historia', 'id_historia');
    }

    /**
     * Obtiene el usuario que registró esta ficha de kinesiología.
     * Asume que el modelo User está configurado para usar 'usuario' como tabla y 'id_usuario' como PK.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }
}
