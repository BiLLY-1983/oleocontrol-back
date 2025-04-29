<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="EntryResource",
 *     description="Recurso que representa la entrada de aceitunas, incluyendo la cantidad de aceitunas, cantidad de aceite y los detalles del miembro asociado",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="entry_date", type="string", format="date", example="2025-04-29"),
 *     @OA\Property(property="olive_quantity", type="integer", example=100),
 *     @OA\Property(property="oil_quantity", type="integer", example=50),
 *     @OA\Property(property="analysis_status", type="string", example="pending"),
 *     @OA\Property(
 *         property="member",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="John Doe"),
 *         @OA\Property(property="member_number", type="string", example="12345"),
 *         @OA\Property(property="member_email", type="string", example="johndoe@example.com")
 *     )
 * )
 */
class EntryResource extends JsonResource
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
            'entry_date' => $this->entry_date,
            'olive_quantity' => $this->olive_quantity,
            'oil_quantity' => $this->oil_quantity,
            'analysis_status' => $this->analysis_status,
            'member' => [
                'id' => $this->member->id,
                'name' => $this->member->user->first_name . ' ' . $this->member->user->last_name,
                'member_number' => $this->member->member_number,
                'member_email' => $this->member->user->email,
            ]
        ];
    }
}
