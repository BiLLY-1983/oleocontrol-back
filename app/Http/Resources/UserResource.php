<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="UserResource",
 *     type="object",
 *     title="UserResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="username", type="string", example="juan.perez.z"),
 *     @OA\Property(property="first_name", type="string", example="Juan"),
 *     @OA\Property(property="last_name", type="string", example="Pérez"),
 *     @OA\Property(property="dni", type="string", example="12345678Z"),
 *     @OA\Property(property="email", type="string", example="juan.perez@ejemplo.com"),
 *     @OA\Property(property="phone", type="string", example="123456789"),
 *     @OA\Property(property="status", type="string", example="active"),
 *     @OA\Property(
 *         property="roles",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="Socio")
 *         )
 *     ),
 *     @OA\Property(
 *         property="member",
 *         type="object",
 *         nullable=true,
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="member_number", type="integer", example=1001)
 *     ),
 *     @OA\Property(
 *         property="employee",
 *         type="object",
 *         nullable=true,
 *         @OA\Property(property="id", type="integer", example=2),
 *         @OA\Property(
 *             property="department",
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=3),
 *             @OA\Property(property="name", type="string", example="Contabilidad")
 *         )
 *     )
 * )
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'username' => $this->username,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'dni' => $this->dni,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,

            'roles' => $this->roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                ];
            }),
        ];

        // Agregar el número de socio si es un 'Socio'
        if ($this->roles->contains('name', 'Socio') && $this->member) {
            $data['member'] = [
                'id' => $this->member->id,
                'member_number' => $this->member->member_number,
            ];
        }

        // Agregar el departamento si es un 'Empleado'
        if ($this->roles->contains('name', 'Empleado') && $this->employee && $this->employee->department) {
            $data['employee'] = [
                'id' => $this->employee->id,
                'department' => [
                    'id' => $this->employee->department->id,
                    'name' => $this->employee->department->name,
                ],
            ];
        }

        return $data;
    }
}
