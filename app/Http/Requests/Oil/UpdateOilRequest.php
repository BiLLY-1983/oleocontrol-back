<?php

namespace App\Http\Requests\Oil;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOilRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'price' => 'nullable|numeric|min:0',
            'photo_url' => 'nullable|url',
        ];
    }
}
