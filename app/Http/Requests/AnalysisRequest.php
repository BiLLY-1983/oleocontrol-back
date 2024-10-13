<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnalysisRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'analysis_date' => 'nullable|date',
            'acidity' => 'nullable|numeric|min:0',
            'humidity' => 'nullable|numeric|min:0',
            'yield' => 'nullable|numeric|min:0',
            'entry_id' => 'required|exists:entries,id|unique:analyses,entry_id',
            'worker_id' => 'nullable|exists:workers,id',
            'oil_id' => 'nullable|exists:oils,id',
            'oil_quantity' => 'nullable|numeric|min:0',
        ];
    }
}
