<?php

namespace App\Http\Requests\LossReason;

use App\DTO\LossReason\LossReasonDTO;
use Illuminate\Foundation\Http\FormRequest;

class LossReasonUpdateRequest extends FormRequest
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
            'label' => 'required|string|max:255',
            'value' => 'required|string',
            'description' => 'nullable|string|max:255',
        ];
    }
    public function toLossReasonDTO(): LossReasonDTO
    {
        return new LossReasonDTO(
            label: $this->input('label'),
            value: $this->input('value'),
            description: $this->input('description'),
        );
    }
}
