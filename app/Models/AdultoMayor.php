<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// ===== INICIO DE LA MODIFICACIÓN =====
use Illuminate\Database\Eloquent\SoftDeletes;
// ===== FIN DE LA MODIFICACIÓN =====

class AdultoMayor extends Model
{
    use HasFactory, SoftDeletes; // Habilita Soft Deletes para este modelo

    protected $table = 'adulto_mayor'; // Nombre de la tabla
    protected $primaryKey = 'id_adulto'; // Clave primaria

    protected $fillable = [
        'ci',
        'discapacidad',
        'vive_con',
        'migrante',
        'origen_migracion', // <-- CAMPO AÑADIDO
        'nro_caso',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'date',
        'migrante' => 'boolean',
    ];

    /**
     * Obtiene la información de la persona para este adulto mayor (uno a uno con Persona).
     */
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'ci', 'ci');
    }

    // RELACIONES PARA LOS MÓDULOS / TABS (Basado en la lógica del Controlador)

    /**
     * Obtiene la actividad laboral de este adulto mayor (relación uno a uno).
     */
    public function actividadLaboral()
    {
        return $this->hasOne(ActividadLaboral::class, 'id_adulto');
    }

    /**
     * Obtiene el encargado principal de este adulto mayor (relación uno a uno).
     * El nombre 'encargados' se mantiene para compatibilidad con las llamadas $adulto->with('encargados')
     * y el acceso en la vista como un único objeto.
     */
    public function encargados()
    {
        return $this->hasOne(Encargado::class, 'id_adulto');
    }

    /**
     * Obtiene el denunciado asociado a este adulto mayor (relación uno a uno).
     */
    public function denunciado()
    {
        return $this->hasOne(Denunciado::class, 'id_adulto');
    }

    /**
     * Obtiene los miembros del grupo familiar de este adulto mayor (relación uno a muchos).
     */
    public function grupoFamiliar()
    {
        return $this->hasMany(GrupoFamiliar::class, 'id_adulto');
    }

    /**
     * Obtiene el croquis de este adulto mayor (relación uno a uno).
     */
    public function croquis()
    {
        return $this->hasOne(Croquis::class, 'id_adulto');
    }

    /**
     * Obtiene los seguimientos de caso de este adulto mayor (relación uno a muchos).
     * Asegúrate que el modelo para la tabla 'seguimiento_caso' es 'SeguimientoCaso'.
     */
    public function seguimientos()
    {
        return $this->hasMany(SeguimientoCaso::class, 'id_adulto');
    }

    /**
     * Obtiene los Anexos N3 de este adulto mayor (relación uno a muchos),
     * debido a la lógica de 'delete all and recreate' en storeAnexoN3.
     */
    public function anexoN3()
    {
        return $this->hasMany(AnexoN3::class, 'id_adulto');
    }

    /**
     * Obtiene los Anexos N5 de este adulto mayor (relación uno a muchos),
     * debido a la lógica de 'syncing' en storeAnexoN5.
     */
    public function anexoN5()
    {
        return $this->hasMany(AnexoN5::class, 'id_adulto');
    }
    /**
     * Relación para obtener la ÚLTIMA ficha de orientación de este adulto mayor.
     * Se ordena por fecha de ingreso en orden descendente y toma la primera.
     */
    public function latestOrientacion()
    {
        return $this->hasOne(Orientacion::class, 'id_adulto', 'id_adulto')->latest('fecha_ingreso');
    }

    /**
     * Relación para obtener TODAS las fichas de orientación de este adulto mayor.
     * (Opcional, si necesitas listar todas las fichas en otro lugar).
     */
    public function orientaciones()
    {
        return $this->hasMany(Orientacion::class, 'id_adulto', 'id_adulto');
    }
    /**
     * NUEVA RELACIÓN: Uno a muchos con HistoriaClinica.
     */
    public function historiasClinicas()
    {
        return $this->hasMany(HistoriaClinica::class, 'id_adulto', 'id_adulto');
    }

    /**
     * NUEVA RELACIÓN: Uno a uno con la última Historia Clínica (para facilitar el acceso en el listado).
     */
    public function latestHistoriaClinica()
    {
        return $this->hasOne(HistoriaClinica::class, 'id_adulto', 'id_adulto')->latest('created_at'); // O por un campo de fecha relevante en HistoriaClinica
    }
    
    public function enfermerias()
    {
        return $this->hasMany(Enfermeria::class, 'id_adulto', 'id_adulto');
    }
    /**
     * Obtiene la última ficha de enfermería para el adulto mayor.
     */
    public function latestEnfermeria()
    {
        // Se especifica 'cod_enf' como la clave local para latestOfMany
        return $this->hasOne(Enfermeria::class, 'id_adulto', 'id_adulto')->latestOfMany('cod_enf');
    }
    // Relación con Fichas de Fisioterapia
    public function fisioterapias()
    {
        return $this->hasMany(Fisioterapia::class, 'id_adulto', 'id_adulto');
    }

    /**
     * Obtiene la ÚLTIMA ficha de Fisioterapia asociada a este adulto mayor.
     * Se especifica 'cod_fisio' como la columna clave porque Fisioterapia no usa 'id' como PK.
     * Argumentos de latestOfMany: (columna_timestamp_para_ordenar, columna_clave_primaria_de_la_relacion)
     */
    public function latestFisioterapia()
    {
        return $this->hasOne(Fisioterapia::class, 'id_adulto', 'id_adulto')->latestOfMany('created_at', 'cod_fisio');
    }

    // Relación con Fichas de Kinesiología
    public function kinesiologias()
    {
        return $this->hasMany(Kinesiologia::class, 'id_adulto', 'id_adulto');
    }

    /**
     * Obtiene la ÚLTIMA ficha de Kinesiología asociada a este adulto mayor.
     * Se asume 'cod_kine' como la columna clave primaria para el modelo Kinesiologia.
     * POR FAVOR, VERIFICA TU MODELO Kinesiologia.php para confirmar si su PK es 'cod_kine' o 'id'.
     * Si Kinesiologia usa 'id' como PK, entonces simplemente usa: ->latestOfMany()
     */
    public function latestKinesiologia()
    {
        return $this->hasOne(Kinesiologia::class, 'id_adulto', 'id_adulto')->latestOfMany('created_at', 'cod_kine');
    }
}