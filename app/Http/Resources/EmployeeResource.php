<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="EmployeeResource",
 *     description="Recurso que representa un empleado, incluyendo su usuario y el departamento al que pertenece",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="username", type="string", example="johndoe"),
 *         @OA\Property(property="first_name", type="string", example="John"),
 *         @OA\Property(property="last_name", type="string", example="Doe"),
 *         @OA\Property(property="dni", type="string", example="12345678A"),
 *         @OA\Property(property="email", type="string", example="johndoe@example.com"),
 *         @OA\Property(property="phone", type="string", example="123456789"),
 *         @OA\Property(property="status", type="string", example="active"),
 *         @OA\Property(
 *             property="roles",
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Administrator")
 *             )
 *         )
 *     ),
 *     @OA\Property(
 *         property="department",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="HR")
 *     )
 * )
 */
class EmployeeResource extends JsonResource
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
            ],
            'department' => [
                'id' => $this->department->id,
                'name' => $this->department->name
            ],
        ];
    }
}
