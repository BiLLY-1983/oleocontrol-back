<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnalysisResource extends JsonResource
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
            'analysis_date' => $this->analysis_date,
            'acidity' => $this->acidity,
            'humidity' => $this->humidity,
            'yield' => $this->yield,
            'entry' => [
                'entry_id' => $this->entry_id,
                'olive_quantity' => $this->entry->olive_quantity
            ],
            'member' => [
                'member_id' => $this->member_id,
                'member_number' => $this->member->member_number,
                'name' => $this->member->user->first_name . ' ' . $this->member->user->last_name,
            ],
            'employee' => $this->employee ? [
                'employee_id' => $this->employee_id,
                'name' => $this->employee->user->first_name . ' ' . $this->employee->user->last_name,
            ] : null,
            'oil' => $this->oil_id ? [
                'oil_id' => $this->oil_id,
                'name' => $this->oil->name,
            ] : null,
        ];
    }
}
