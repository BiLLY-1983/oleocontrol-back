<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'amount' => $this->amount,
            'price' => $this->price,
            'settlement_status' => $this->settlement_status,
            'member' => [
                'member_id' => $this->member_id,
                'name' => $this->member->user->first_name . ' ' . $this->member->user->last_name,
            ],
            'worker' => $this->worker ? [
                'worker_id' => $this->worker_id,
                'name' => $this->worker->user->first_name . ' ' . $this->worker->user->last_name,
            ] : null,
            
        ];
    }
}
