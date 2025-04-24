<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProfileRequest extends FormRequest
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
        $userId = Auth::id();

        return [
            'username' => 'nullable|string|max:255|unique:users,username,' . $userId,
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'dni' => ['nullable', 'string', 'max:20', 'unique:users,dni,' . $userId, function ($attribute, $value, $fail) {
                if ($value && !preg_match('/^\d{8}[A-Z]$/i', $value)) {
                    return $fail('El formato del DNI no es válido.');
                }

                if ($value) {
                    $dniNumber = substr($value, 0, -1);
                    $dniLetter = strtoupper(substr($value, -1));
                    $letters = "TRWAGMYFPDXBNJZSQVHLCKE";
                    $expectedLetter = $letters[$dniNumber % 23] ?? null;

                    if ($expectedLetter !== $dniLetter) {
                        return $fail('La letra del DNI no es válida.');
                    }
                }
            }],
            'email' => 'nullable|email|max:255|unique:users,email,' . $userId,
            'password' => [
                'nullable',
                'string',
                'min:10',
                'confirmed',
                function ($attribute, $value, $fail) {
                    if ($value && !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{10,}$/', $value)) {
                        return $fail('La contraseña debe tener al menos 10 caracteres, una letra mayúscula, una letra minúscula y un número.');
                    }
                }
            ],
            'phone' => 'nullable|string|max:20',
            'status' => 'nullable|boolean',
        ];
    }
}
