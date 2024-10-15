<?php

namespace App\Http\Requests\Analysis;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnalysisRequest extends FormRequest
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
        $analysisId = $this->route('analysis'); 
        
        return [
            'analysis_date' => 'nullable|date',
            'acidity'       => 'nullable|numeric|min:0|max:100.00',
            'humidity'      => 'nullable|numeric|min:0|max:100.00',
            'yield'         => 'nullable|numeric|min:0|max:100.00',
            'entry_id'      => 'nullable|exists:entries,id|unique:analyses,entry_id,' . $analysisId,
            'worker_id'     => 'nullable|exists:workers,id',
            'oil_id'        => 'nullable|exists:oils,id',
            'oil_quantity'  => 'nullable|numeric|min:0',
        ];
    }
}
