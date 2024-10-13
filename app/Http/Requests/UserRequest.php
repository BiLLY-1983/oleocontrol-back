<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'username' => 'required|string|max:255|unique:users,username,' . $this->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dni' => 'required|string|max:20|unique:users,dni,' . $this->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $this->id,
            'password' => 'nullable|string|min:8',
            'phone' => 'required|string|max:20',
            'status' => 'boolean',
            'profile_picture' => 'nullable|string|max:255',
        ];
    }
}
