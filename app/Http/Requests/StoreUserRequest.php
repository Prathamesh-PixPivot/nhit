<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
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
            'name' => 'required|string|max:250',
            'emp_id' => 'required|string|max:250',
            'number' => 'required|digits:10',
            'email' => 'required|string|email:rfc,dns|max:250|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->letters()->numbers()->symbols()->uncompromised()],
            'roles' => 'required',
            'active' => 'required|in:Y,N',
            'account_holder' => 'required|string|max:250',
            'bank_name' => 'required|string|max:250',
            'bank_account' => 'required|string|max:250',
            'ifsc_code' => 'required|string|max:250',
            'designation_id' => 'required|exists:designations,id',
            'department_id' => 'required|exists:departments,id',
            'file' => 'nullable|image|mimes:png|max:2048',
        ];
    }
    public function messages(): array
    {
        return [
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Passwords do not match.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.mixedCase' => 'Password must contain both uppercase and lowercase letters.',
            'password.numbers' => 'Password must contain at least one number.',
            'password.symbols' => 'Password must contain at least one special character.',
            'password.uncompromised' => 'This password has been exposed in a data leak. Please choose another one.',
        ];
    }
}
