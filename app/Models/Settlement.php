<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

/**
 * Modelo que representa un registro de liquidación dentro del sistema.
 *
 * La clase gestiona los registros de liquidación, que están asociados a un miembro, 
 * un empleado y un tipo de aceite específico.
 *
 * @package App\Models
 */
class Settlement extends Model
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
     * Relación de pertenencia a un miembro (socios).
     *
     * Cada liquidación está asociada a un miembro específico.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Relación de pertenencia a un empleado.
     *
     * Cada liquidación está asociada a un empleado específico.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Relación de pertenencia a un tipo de aceite.
     *
     * Cada liquidación está asociada a un tipo específico de aceite.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function oil(): BelongsTo
    {
        return $this->belongsTo(Oil::class);
    }
}
