<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

/**
 * Modelo que representa un acuerdo de aceite para un miembro.
 *
 * Esta clase gestiona las relaciones entre los acuerdos de aceite y los miembros (socios)
 * así como los tipos de aceite involucrados en el acuerdo.
 *
 * @package App\Models
 */
class OilSettlement extends Model
{
    use HasFactory, HasApiTokens;

    /**
     * Los atributos que no se pueden asignar masivamente.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Relación con el miembro (socio) que tiene este acuerdo de aceite.
     *
     * Esta relación establece que un acuerdo de aceite pertenece a un miembro (socio).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Relación con el tipo de aceite involucrado en este acuerdo.
     *
     * Esta relación establece que un acuerdo de aceite pertenece a un tipo de aceite.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function oil()
    {
        return $this->belongsTo(Oil::class);
    }
}
