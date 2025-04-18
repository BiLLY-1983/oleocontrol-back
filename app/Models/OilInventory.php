<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class OilInventory extends Model
{
    use HasFactory, HasApiTokens;

    protected $guarded = [];

    // Relación con los miembros (socios)
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    // Relación con los tipos de aceite
    public function oil()
    {
        return $this->belongsTo(Oil::class);
    }
}
