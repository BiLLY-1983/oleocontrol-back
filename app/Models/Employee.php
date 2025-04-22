<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

/**
 * Modelo que representa a un empleado dentro de la organización.
 *
 * Esta clase está asociada a la tabla 'employees' y permite interactuar con los empleados del sistema. Un empleado
 * puede estar asociado a un usuario, pertenecer a un departamento y tener varios análisis y liquidaciones relacionadas.
 *
 * @package App\Models
 */
class Employee extends Model
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
     * Obtiene el usuario asociado a este empleado.
     *
     * La relación establece que cada empleado pertenece a un usuario específico.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene el departamento al que pertenece este empleado.
     *
     * La relación establece que cada empleado pertenece a un departamento.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Obtiene todos los análisis (Analysis) realizados por este empleado.
     *
     * La relación establece que un empleado puede tener múltiples análisis asociados.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function analyses(): HasMany
    {
        return $this->hasMany(Analysis::class);
    }

    /**
     * Obtiene todas las liquidaciones (Settlement) asociadas a este empleado.
     *
     * La relación establece que un empleado puede tener múltiples liquidaciones asociadas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function settlements(): HasMany
    {
        return $this->hasMany(Settlement::class);
    }
}
