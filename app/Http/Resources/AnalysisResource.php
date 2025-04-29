<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="AnalysisResource",
 *     description="Recurso que representa un análisis de aceite",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="analysis_date",
 *         type="string",
 *         format="date",
 *         example="2025-04-29"
 *     ),
 *     @OA\Property(
 *         property="acidity",
 *         type="number",
 *         format="float",
 *         example=0.5
 *     ),
 *     @OA\Property(
 *         property="humidity",
 *         type="number",
 *         format="float",
 *         example=5.0
 *     ),
 *     @OA\Property(
 *         property="yield",
 *         type="number",
 *         format="float",
 *         example=0.85
 *     ),
 *     @OA\Property(
 *         property="entry",
 *         type="object",
 *         ref="#/components/schemas/EntryResource"
 *     ),
 *     @OA\Property(
 *         property="member",
 *         type="object",
 *         ref="#/components/schemas/MemberResource"
 *     ),
 *     @OA\Property(
 *         property="employee",
 *         type="object",
 *         ref="#/components/schemas/EmployeeResource"
 *     ),
 *     @OA\Property(
 *         property="oil",
 *         type="object",
 *         ref="#/components/schemas/OilResource"
 *     )
 * )
 */
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
