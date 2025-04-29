<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="OilInventoryResource",
 *     description="Recurso que representa el inventario de aceite con su información detallada",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="settlement_date", type="string", format="date", example="2025-04-29"),
 *     @OA\Property(property="quantity", type="integer", example=500),
 *     @OA\Property(
 *         property="oil",
 *         type="object",
 *         @OA\Property(property="oil_id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Aceite de Oliva Virgen Extra")
 *     ),
 *     @OA\Property(
 *         property="member",
 *         type="object",
 *         @OA\Property(property="member_id", type="integer", example=2),
 *         @OA\Property(property="name", type="string", example="Juan Pérez")
 *     )
 * )
 */
class OilInventoryResource extends JsonResource
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
            'quantity' => $this->quantity,
            'oil' => [
                'oil_id' => $this->oil_id,
                'name' => $this->oil->name,
            ],
            'member' => [
                'member_id' => $this->member_id,
                'name' => $this->member->user->first_name . ' ' . $this->member->user->last_name,
            ],            
        ];
    }
}
