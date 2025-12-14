<?php

namespace App\Http\Requests\Central;

use App\Enums\Landlord\ActivationCodeStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateActivationCodeStatusRequest extends FormRequest
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
            'ids' => 'required|array',
            'ids.*' => 'exists:activation_codes,id',
            'status' => 'required|string|in:' . implode(',', [
                ActivationCodeStatusEnum::AVAILABLE->value,
                ActivationCodeStatusEnum::USED->value,
                ActivationCodeStatusEnum::BLOCKED->value,
            ]),
        ];
    }
}
