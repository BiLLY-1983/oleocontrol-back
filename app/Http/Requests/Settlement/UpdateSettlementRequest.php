<?php

namespace App\Http\Requests\Settlement;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettlementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $settlementId = $this->route('settlement');

        return [
            'amount' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'settlement_status' => 'nullable|in:Pendiente,Aceptada,Cancelada',
            'member_id' => 'nullable|exists:members,id',
            'employee_id' => 'nullable|exists:employees,id'
        ];
    }
}
