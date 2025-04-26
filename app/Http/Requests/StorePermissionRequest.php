<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\PermissionValidation;
class StorePermissionRequest extends FormRequest
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
            'date' => ['required', 'date', new PermissionValidation],
            'time' => 'required|date_format:H:i',
            'reason' => 'required|string',
            'duration' => 'required|string',
            'type' => 'required|string',
            'notes' => 'nullable|string',
        ];
    }
}
