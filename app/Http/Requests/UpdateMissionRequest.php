<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Mission;
use App\Rules\MissionValidation;
class UpdateMissionRequest extends FormRequest
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
            'date' => ['required', 'date', new MissionValidation($this->id)],
            'reason' => 'required|string',
            'direction' => 'required|string',
            'notes' => 'nullable|string',
        ];
    }
}
