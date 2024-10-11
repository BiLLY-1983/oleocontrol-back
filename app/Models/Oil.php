<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class Oil extends Model
{
    use HasFactory, HasApiTokens;

    // Especificar nombre de la tabla (daba problemas a la hora de realizar los seeders)
    protected $table = 'oils';

    protected $guarded = [];


    /* ---------- */
    /* Relaciones */
    /* ---------- */

    public function analyses(): HasMany
    {
        return $this->hasMany(Analysis::class);
    }
}
