<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

/**
 * Modelo que representa un departamento dentro de la organización.
 *
 * Esta clase está asociada a la tabla 'departments' y permite interactuar con los departamentos que existen en el
 * sistema. Un departamento puede tener varios empleados asociados a él.
 *
 * @package App\Models
 */
class Department extends Model
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
     * Obtiene todos los empleados (Employee) asociados a este departamento.
     *
     * La relación establece que cada departamento puede tener varios empleados trabajando en él.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
