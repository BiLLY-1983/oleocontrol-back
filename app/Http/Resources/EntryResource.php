<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
