<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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

            'roles' => $this->roles->map(function($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                ];
            }),
        ];

        // Agregar el número de socio si es un 'Socio'
        if ($this->roles->contains('name', 'Socio')) {
            $data['member'] = [
                'id' => $this->member->id,
                'member_number' => $this->member->member_number,
            ];
        }

        // Agregar el departamento si es un 'Empleado'
        if ($this->roles->contains('name', 'Empleado')) {
            $data['employee'] = [
                'id' => $this->employee->id,
                'department' => $this->employee->department->name,
            ];
        }

        return $data;
    }
}
