<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users'],
            'department_id' => ['required', 'exists:departments,id'],
            'role' => ['required', 'string', 'in:admin,supervisor,employee'],
            'supervisor_id' => ['nullable', 'exists:users,id'],
            'cid' => ['required', 'string', 'max:12', 'min:12', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'file_number' => ['required', 'string', 'max:12', 'unique:users'],
        ];
    }
} 