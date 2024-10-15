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
            'amount' => 'required|numeric|min:0', 
            'price' => 'nullable|numeric|min:0',
            'settlement_status' => 'required|in:Pendiente,Aceptada,Cancelada', 
            'member_id' => 'required|exists:members,id',
            'worker_id' => 'nullable|exists:workers,id',
        ];
    }
}
