<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

/**
 * Modelo que representa una notificación en el sistema.
 *
 * Esta clase está asociada a la tabla 'notifications' y gestiona las notificaciones enviadas de un usuario
 * a otro dentro del sistema.
 *
 * @package App\Models
 */
class Notification extends Model
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
     * Obtiene el usuario que envió la notificación.
     *
     * La relación establece que cada notificación tiene un emisor, que es un usuario en el sistema.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Obtiene el usuario que recibió la notificación.
     *
     * La relación establece que cada notificación tiene un receptor, que es un usuario en el sistema.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
