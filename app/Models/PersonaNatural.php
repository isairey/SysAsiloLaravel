<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- 1. AÑADIR ESTA LÍNEA

class PersonaNatural extends Model
{
    use HasFactory, SoftDeletes; // <-- 2. AÑADIR 'SoftDeletes' AQUÍ

    protected $table = 'persona_natural';
    protected $primaryKey = 'id_natural';
    public $timestamps = false;

    protected $fillable = [
        'id_encargado',
        'primer_apellido',
        'segundo_apellido',
        'nombres',
        'edad',
        'ci',
        'telefono',
        'direccion_domicilio',
        'relacion_parentesco',
        'direccion_de_trabajo',
        'ocupacion',
    ];

    // public function encargado()
    // {
    //     return $this->belongsTo(Encargado::class, 'id_encargado');
    // }
    // public function denunciado()
    // {
    //     return $this->hasOne(Denunciado::class, 'id_natural');
    // }
    // public function anexosN3()
    // {
    //     return $this->hasMany(AnexoN3::class, 'id_natural', 'id_natural');
    // }
    // Relaciones:

    /**
     * Relación: Una PersonaNatural puede pertenecer a un Encargado (a través de id_encargado en PersonaNatural).
     * Esto significa que si id_encargado no es nulo, esta PersonaNatural está asumiendo el rol de Encargado.
     */
    public function encargado()
    {
        return $this->belongsTo(Encargado::class, 'id_encargado');
    }

    /**
     * Relación inversa: Una PersonaNatural puede ser el Denunciado de un caso.
     * Asume que la tabla 'denunciado' tiene una FK 'id_natural' que apunta a 'persona_natural'.
     */
    public function denunciado()
    {
        return $this->hasOne(Denunciado::class, 'id_natural', 'id_natural');
    }

    /**
     * Relación inversa: Una PersonaNatural puede ser referenciada en múltiples Anexos N3.
     * Asume que la tabla 'anexo_n3' tiene una FK 'id_natural' que apunta a 'persona_natural'.
     */
    public function anexosN3()
    {
        return $this->hasMany(AnexoN3::class, 'id_natural', 'id_natural');
    }

    /**
     * Determina si esta instancia de PersonaNatural es "huérfana"
     * (es decir, no está referenciada por ninguna otra entidad importante).
     *
     * @return bool True si es huérfana y puede ser eliminada, false en caso contrario.
     */
    public function isOrphan()
    {
        // 1. Verificar si está siendo usada como Denunciado
        if ($this->denunciado()->exists()) {
            return false;
        }

        // 2. Verificar si está siendo usada en algún AnexoN3
        if ($this->anexosN3()->exists()) {
            return false;
        }

        // 3. Verificar si está actuando como un Encargado (a través del campo id_encargado en esta tabla)
        // Esto asume que id_encargado en persona_natural enlaza con la tabla 'encargado'
        // y que si este campo no es nulo, esta PersonaNatural es un Encargado.
        if (!is_null($this->id_encargado)) {
            // También deberías verificar si el Encargado asociado todavía existe
            // para evitar falsos positivos si el Encargado ya fue eliminado pero el id_encargado aquí no se limpió.
            // if ($this->encargado()->exists()) { return false; }
            return false; // Mantener por la lógica actual del campo 'id_encargado'
        }

        // El chequeo de grupoFamiliarMembers ha sido removido.

        // Si ninguna de las condiciones anteriores es verdadera, entonces es huérfana.
        return true;
    }
}
