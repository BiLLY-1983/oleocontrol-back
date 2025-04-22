<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

/**
 * Modelo que representa un miembro del sistema.
 *
 * Esta clase está asociada a la tabla 'members' y gestiona la información de un miembro, incluyendo sus
 * entradas de aceituna, análisis, liquidaciones y otros registros relacionados con el aceite.
 *
 * @package App\Models
 */
class Member extends Model
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
     * Obtiene el usuario asociado a este miembro.
     *
     * La relación establece que cada miembro pertenece a un usuario específico.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene las entradas de aceituna asociadas a este miembro.
     *
     * La relación establece que cada miembro puede tener varias entradas de aceituna.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class);
    }

    /**
     * Obtiene las liquidaciones asociadas a este miembro.
     *
     * La relación establece que cada miembro puede tener varias liquidaciones.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function settlements(): HasMany
    {
        return $this->hasMany(Settlement::class);
    }

    /**
     * Obtiene los análisis asociados a este miembro.
     *
     * La relación establece que cada miembro puede tener varios análisis asociados.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function analyses(): HasMany
    {
        return $this->hasMany(Analysis::class);
    }

    /**
     * Obtiene las liquidaciones de aceite asociadas a este miembro.
     *
     * La relación establece que cada miembro puede tener varias liquidaciones de aceite.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function oilSettlements()
    {
        return $this->hasMany(OilSettlement::class);
    }

    /**
     * Obtiene los inventarios de aceite asociados a este miembro.
     *
     * La relación establece que cada miembro puede tener varios inventarios de aceite.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function oilInventories()
    {
        return $this->hasMany(OilInventory::class);
    }
}
