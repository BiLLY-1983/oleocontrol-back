<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

/**
 * Modelo que representa el aceite en el sistema.
 *
 * Esta clase está asociada a la tabla 'oils' y gestiona las relaciones del aceite con otras entidades
 * como análisis, liquidaciones e inventarios.
 *
 * @package App\Models
 */
class Oil extends Model
{
    use HasFactory, HasApiTokens;

    /**
     * Especifica el nombre de la tabla asociada al modelo.
     *
     * Debido a problemas al realizar seeders, se define explícitamente el nombre de la tabla como 'oils'.
     *
     * @var string
     */
    protected $table = 'oils';

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
     * Obtiene todos los análisis relacionados con este aceite.
     *
     * Esta relación establece que un aceite puede tener muchos análisis asociados.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function analyses(): HasMany
    {
        return $this->hasMany(Analysis::class);
    }

    /**
     * Obtiene todas las liquidaciones relacionadas con este aceite.
     *
     * Esta relación establece que un aceite puede tener muchas liquidaciones asociadas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function settlements(): HasMany
    {
        return $this->hasMany(Settlement::class);
    }

    /**
     * Obtiene todas las liquidaciones de aceite relacionadas con este aceite.
     *
     * Esta relación establece que un aceite puede tener muchas liquidaciones de aceite asociadas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function oilSettlements()
    {
        return $this->hasMany(OilSettlement::class);
    }

    /**
     * Obtiene todos los inventarios de aceite relacionados con este aceite.
     *
     * Esta relación establece que un aceite puede tener muchos inventarios de aceite asociados.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function oilInventories()
    {
        return $this->hasMany(OilInventory::class);
    }
}
