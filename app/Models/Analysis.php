<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

class Analysis extends Model
{
    use HasFactory, HasApiTokens;

    protected $guarded = [];


    /* ---------- */
    /* Relaciones */
    /* ---------- */

    public function entry(): BelongsTo
    {
        return $this->belongsTo(Entry::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function oil(): BelongsTo
    {
        return $this->belongsTo(Oil::class);
    }
}
