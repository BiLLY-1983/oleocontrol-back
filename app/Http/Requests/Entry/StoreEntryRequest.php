<?php

namespace App\Http\Requests\Entry;

use Illuminate\Foundation\Http\FormRequest;

class StoreEntryRequest extends FormRequest
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
            'entry_date' => 'required|date',
            'olive_quantity' => 'required|numeric|min:1',
            'oil_quantity' => 'nullable|numeric|min:1',
            'analysis_status' => 'required|in:Pendiente,Completo',
            'member_id' => 'required|exists:members,id',
        ];
    }
}
