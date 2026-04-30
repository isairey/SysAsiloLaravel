<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResponsableDetails extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'profesion',
        'ci',
        'telefono',
        'direccion',
    ];

    /**
     * Obtiene el usuario asociado con estos detalles de responsable.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}