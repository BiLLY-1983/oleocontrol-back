<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;

class Entry extends Model
{
    use HasFactory, HasApiTokens;

    protected $guarded = [];


    /* ---------- */
    /* Relaciones */
    /* ---------- */

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function analysis(): HasOne
    {
        return $this->hasOne(Analysis::class);
    }
}
