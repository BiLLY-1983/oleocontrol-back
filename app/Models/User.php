<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'dni',
        'email',
        'password',
        'phone',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    /**
     * Verificar si el usuario tiene un rol específico
     * 
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    /* ---------- */
    /* Relaciones */
    /* ---------- */

    /**
     * Relación de muchos a muchos con el modelo Role.
     *
     * Un usuario puede tener múltiples roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Relación uno a uno con el modelo Member.
     *
     * Un usuario puede estar asociado a un miembro (socios).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function member(): HasOne
    {
        return $this->hasOne(Member::class, 'user_id');
    }

    /**
     * Relación uno a uno con el modelo Employee.
     *
     * Un usuario puede estar asociado a un empleado.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'user_id');
    }

    /**
     * Relación de uno a muchos con el modelo Notification (notificaciones enviadas).
     *
     * Un usuario puede enviar muchas notificaciones.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sentNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'sender_id');
    }

    /**
     * Relación de uno a muchos con el modelo Notification (notificaciones recibidas).
     *
     * Un usuario puede recibir muchas notificaciones.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function receivedNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'receiver_id');
    }

    /**
     * Función para generar un nombre de usuario único.
     * Se obtiene un nombre de usuario basado en el primer nombre, apellido y DNI.
     * 
     * @param string $first
     * @param string $last
     * @param string $dni
     * @return string
     */
    public static function generateUsername(string $first, string $last, string $dni): string
    {
        $dniLetter = substr($dni, -1);
        $base = strtolower("{$first}.{$last}{$dniLetter}");
        $username = $base;

        $count = self::where('username', $username)->count();
        if ($count > 0) {
            $username .= rand(10, 99);
        }

        return $username;
    }
}
