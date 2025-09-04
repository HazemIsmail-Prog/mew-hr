<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\MissionValidation;
class StoreMissionRequest extends FormRequest
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
            'date' => ['required', 'date', 'before_or_equal:today', new MissionValidation],
            'time' => ['required_if:direction,in,out'],
            'reason' => 'required|string',
            'direction' => 'required|string',
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
