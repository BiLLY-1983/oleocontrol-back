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
            'employee' => $this->employee ? [
                'employee_id' => $this->employee_id,
                'name' => $this->employee->user->first_name . ' ' . $this->employee->user->last_name,
            ] : null,
            
        ];
    }
}
