<?php

namespace App\Http\Requests\Worker;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOWorkerRequest extends FormRequest
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
        $workerId = $this->route('worker');

        return [
            'user_id' => 'required|exists:users,id|unique:workers,user_id,' . $workerId,
            'department_id' => 'nullable|exists:departments,id'
        ];
    }
}
