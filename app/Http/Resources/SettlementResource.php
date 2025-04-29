<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="SettlementResource",
 *     description="Liquidación de aceite realizada a un socio",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="settlement_date", type="string", format="date", example="2024-04-10"),
 *     @OA\Property(property="settlement_date_res", type="string", format="date", example="2024-04-15"),
 *     @OA\Property(property="amount", type="number", format="float", example=500.75),
 *     @OA\Property(property="price", type="number", format="float", example=3.25),
 *     @OA\Property(property="settlement_status", type="string", example="Pagado"),

 *     @OA\Property(
 *         property="oil",
 *         type="object",
 *         @OA\Property(property="oil_id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Aceite Virgen Extra"),
 *         @OA\Property(property="price", type="number", format="float", example=4.50)
 *     ),

 *     @OA\Property(
 *         property="member",
 *         type="object",
 *         @OA\Property(property="member_id", type="integer", example=10),
 *         @OA\Property(property="name", type="string", example="Juan Pérez"),
 *         @OA\Property(property="member_number", type="string", example="SOC123"),
 *         @OA\Property(property="member_email", type="string", format="email", example="juan@example.com")
 *     ),

 *     @OA\Property(
 *         property="employee",
 *         type="object",
 *         nullable=true,
 *         @OA\Property(property="employee_id", type="integer", example=3),
 *         @OA\Property(property="name", type="string", example="María García")
 *     )
 * )
 */
class SettlementResource extends JsonResource
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
            'settlement_date' => $this->settlement_date,
            'settlement_date_res' => $this->settlement_date_res,
            'amount' => $this->amount,
            'price' => $this->price,
            'settlement_status' => $this->settlement_status,
            'oil' => [
                'oil_id' => $this->oil_id,
                'name' => $this->oil->name,
                'price' => $this->oil->price,
            ],
            'member' => [
                'member_id' => $this->member_id,
                'name' => $this->member->user->first_name . ' ' . $this->member->user->last_name,
                'member_number' => $this->member->member_number,
                'member_email' => $this->member->user->email,
            ],
            'employee' => $this->employee ? [
                'employee_id' => $this->employee_id,
                'name' => $this->employee->user->first_name . ' ' . $this->employee->user->last_name,
            ] : null,
            
        ];
    }
}
