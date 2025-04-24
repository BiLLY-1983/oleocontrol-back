<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
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
            //'username' => 'required|string|max:255|unique:users',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dni' => ['required', 'string', 'max:20', 'unique:users', function ($attribute, $value, $fail) {
                if (!preg_match('/^\d{8}[A-Z]$/i', $value)) {
                    return $fail('El formato del DNI no es válido.');
                }

                $dniNumber = substr($value, 0, -1);
                $dniLetter = strtoupper(substr($value, -1));
                $letters = "TRWAGMYFPDXBNJZSQVHLCKE";
                $expectedLetter = $letters[$dniNumber % 23] ?? null;

                if ($expectedLetter !== $dniLetter) {
                    $fail('La letra del DNI no es válida.');
                }
            }],
            'email' => 'required|string|email|max:255|unique:users',
            //'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'status' => 'required|boolean',
            'user_id' => 'exists:users,id|unique:members,user_id',
            'department_id' => 'required|exists:departments,id'
        ];
    }
}
