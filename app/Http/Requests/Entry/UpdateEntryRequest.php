<?php

namespace App\Http\Requests\Entry;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEntryRequest extends FormRequest
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
        $entryId = $this->route('entry');

        return [
            'entry_date' => 'nullable|date', 
            'olive_quantity' => 'nullable|numeric|min:1',
            'oil_quantity' => 'nullable|numeric|min:1',
            'analysis_status' => 'nullable|in:Pendiente,Completo',
            'member_id' => 'nullable|exists:members,id|unique:entries,member_id,' . $entryId,
        ];
    }
}
