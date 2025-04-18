<?php

namespace App\Http\Requests\Settlement;

use Illuminate\Foundation\Http\FormRequest;

class StoreSettlementRequest extends FormRequest
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
        return [
            'settlement_date' => 'required|date',
            'oil_id' => 'required|exists:oils,id',
            'amount' => 'required|numeric|min:0', 
            'price' => 'required|numeric|min:0',
            'settlement_status' => 'required|in:Pendiente,Aceptada,Cancelada', 
            'member_id' => 'required|exists:members,id',
            'employee_id' => 'nullable|exists:employee,id',
        ];
    }
}
