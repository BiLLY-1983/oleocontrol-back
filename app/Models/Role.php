<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;

/**
 * Modelo que representa un rol dentro del sistema.
 *
 * Esta clase gestiona la relación entre los roles y los usuarios asignados a esos roles.
 * Un rol puede ser asignado a varios usuarios, y un usuario puede tener varios roles.
 *
 * @package App\Models
 */
class Role extends Model
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
     * Relación muchos a muchos con los usuarios.
     *
     * Un rol puede estar asignado a muchos usuarios, y un usuario puede tener varios roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

}
