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
            'settlement_date_res' => 'required|date',
            'oil_id' => 'nullable|exists:oil,id',
            'amount' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'settlement_status' => 'required|in:Pendiente,Aceptada,Cancelada',
            'member_id' => 'nullable|exists:members,id',
            'employee_id' => 'required|exists:employees,id'
        ];
    }
}
