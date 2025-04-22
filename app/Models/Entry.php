<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;

/**
 * Modelo que representa una entrada de aceituna en el sistema.
 *
 * Esta clase está asociada a la tabla 'entries' y permite gestionar las entradas de aceituna, las cuales
 * pueden estar asociadas a un miembro y tener un análisis asociado.
 *
 * @package App\Models
 */
class Entry extends Model
{
    use HasFactory, HasApiTokens;

    /**
     * Los atributos que no se pueden asignar masivamente.
     *
     * @var array
     */
    protected $guarded = [];

    /* ---------- */
    /* Relaciones */
    /* ---------- */

    /**
     * Obtiene el miembro asociado a esta entrada de aceituna.
     *
     * La relación establece que cada entrada de aceituna pertenece a un miembro específico.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Obtiene el análisis asociado a esta entrada de aceituna.
     *
     * La relación establece que cada entrada puede tener un análisis asociado.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function analysis(): HasOne
    {
        return $this->hasOne(Analysis::class);
    }
}
