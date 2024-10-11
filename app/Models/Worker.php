<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class Worker extends Model
{
    use HasFactory, HasApiTokens;

    protected $guarded = [];


    /* ---------- */
    /* Relaciones */
    /* ---------- */
    public function user()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    public function departments(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function analyses(): HasMany
    {
        return $this->hasMany(Analysis::class);
    }

    public function settlements(): HasMany
    {
        return $this->hasMany(Settlement::class);
    }
}
