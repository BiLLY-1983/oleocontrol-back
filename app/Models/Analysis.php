<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

/**
 * Modelo que representa un análisis realizado sobre una entrada de aceituna en el sistema.
 *
 * Esta clase está asociada a la tabla 'analyses' y permite interactuar con los análisis relacionados con las entradas
 * de aceituna, los miembros (agricultores), los empleados que realizaron el análisis y los aceites relacionados con
 * la entrada.
 * 
 * @package App\Models
 */
class Analysis extends Model
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
     * Obtiene la entrada (Entry) relacionada con este análisis.
     *
     * La relación establece que cada análisis pertenece a una entrada de aceituna en el sistema.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(Entry::class);
    }

    /**
     * Obtiene el miembro (Member) relacionado con este análisis.
     *
     * La relación establece que cada análisis está asociado con un miembro (agricultor) que realizó la entrada
     * de aceituna.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Obtiene el empleado (Employee) que realizó el análisis.
     *
     * La relación establece que cada análisis fue realizado por un empleado específico del sistema.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Obtiene el aceite (Oil) relacionado con este análisis.
     *
     * La relación establece que cada análisis está vinculado a un tipo de aceite específico que se obtiene de
     * la entrada de aceituna.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function oil(): BelongsTo
    {
        return $this->belongsTo(Oil::class);
    }
}
