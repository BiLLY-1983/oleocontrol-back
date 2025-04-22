<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

/**
 * Modelo que representa un inventario de aceite en el sistema.
 *
 * Esta clase gestiona las relaciones entre el inventario de aceite, los miembros (socios)
 * y los tipos de aceite almacenados en el inventario.
 *
 * @package App\Models
 */
class OilInventory extends Model
{
    use HasFactory, HasApiTokens;

    /**
     * Los atributos que no se pueden asignar masivamente.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Relación con el miembro (socio) que posee este inventario de aceite.
     *
     * Esta relación establece que un inventario de aceite pertenece a un miembro (socio).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Relación con el tipo de aceite almacenado en este inventario.
     *
     * Esta relación establece que un inventario de aceite pertenece a un tipo de aceite.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function oil()
    {
        return $this->belongsTo(Oil::class);
    }
}
