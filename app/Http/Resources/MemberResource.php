<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="MemberResource",
 *     description="Recurso que representa la información de un miembro, incluyendo los datos de usuario asociados",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="member_number", type="string", example="12345"),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="username", type="string", example="johndoe"),
 *         @OA\Property(property="first_name", type="string", example="John"),
 *         @OA\Property(property="last_name", type="string", example="Doe"),
 *         @OA\Property(property="dni", type="string", example="12345678X"),
 *         @OA\Property(property="email", type="string", example="johndoe@example.com"),
 *         @OA\Property(property="phone", type="string", example="123456789"),
 *         @OA\Property(property="status", type="string", example="active"),
 *         @OA\Property(
 *             property="roles",
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=2),
 *                 @OA\Property(property="name", type="string", example="Socio")
 *             )
 *         )
 *     )
 * )
 */
class MemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'member_number' => $this->member_number,
            'user' => [
                'id' => $this->user->id,
                'username' => $this->user->username,
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'dni' => $this->user->dni,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
                'status' => $this->user->status,
                'roles' => $this->user->roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                    ];
                }),
            ]
        ];
    }
}
