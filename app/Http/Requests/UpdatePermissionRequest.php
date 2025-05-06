<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\PermissionValidation;
class UpdatePermissionRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'date' => ['required', 'date', 'before_or_equal:today', new PermissionValidation($this->id)],
            'time' => 'required|date_format:H:i',
            'reason' => 'required|string',
            'duration' => 'required|string',
            'type' => 'required|string',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'date.before_or_equal' => __('The date must be today or less.'),
        ];
    }
}
