<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user->id),
            ],
            'department_id' => ['required', 'exists:departments,id'],
            'role' => ['required', 'string', 'in:admin,supervisor,employee'],
            'supervisor_id' => ['nullable', 'exists:users,id'],
            'cid' => ['required', 'numeric', 'digits:12'],
            'password' => ['nullable', 'string'],
            'signature' => ['nullable', 'string','exclude_if:signature,null'],
            'file_number' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب',
            'name.max' => 'الاسم يجب أن لا يتجاوز 255 حرف',
            'email.email' => 'البريد الإلكتروني غير صالح',
            'email.max' => 'البريد الإلكتروني يجب أن لا يتجاوز 255 حرف',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'department_id.required' => 'القسم مطلوب',
            'department_id.exists' => 'القسم غير موجود',
            'role.required' => 'الدور مطلوب',
            'role.string' => 'الدور يجب أن يكون نص',
            'role.in' => 'الدور غير صالح',
            'supervisor_id.exists' => 'المشرف غير موجود',
            'cid.required' => 'الرقم المدني مطلوب',
            'cid.numeric' => 'الرقم المدني يجب أن يكون أرقام فقط',
            'cid.max' => 'الرقم المدني يجب أن يكون 12 رقم',
            'cid.min' => 'الرقم المدني يجب أن يكون 12 رقم',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ];
    }
} 