<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="OilResource",
 *     description="Recurso que representa un aceite con su información detallada",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Aceite de Oliva Virgen Extra"),
 *     @OA\Property(property="description", type="string", example="Aceite de oliva virgen extra de primera prensada en frío."),
 *     @OA\Property(property="price", type="number", format="float", example=10.99),
 *     @OA\Property(property="photo_url", type="string", format="url", example="http://example.com/photo.jpg")
 * )
 */
class OilResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'photo_url' => $this->photo_url,
        ];
    }
}
