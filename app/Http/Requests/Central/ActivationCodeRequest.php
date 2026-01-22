<?php

namespace App\Http\Requests\Central;

use App\Enums\Landlord\ActivationCodeStatusEnum;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class ActivationCodeRequest extends BaseRequest
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
            'generate_multiple' => 'nullable|integer|in:0,1',
            'count' => 'required_if:generate_multiple,1|integer|min:1',
            'parts' => 'required_if:generate_multiple,1|integer|in:3,4,5,6',
            'partLength' => 'required_if:generate_multiple,1|integer|in:3,4,5,6',

            'code' => 'required_if:generate_multiple,0|string|max:255|unique:activation_codes',

            'plan_id' => 'required|integer|exists:plans,id',
            'source_id' => 'required|integer|exists:sources,id',
            'validity_days' => 'required|integer',
            'status' => [
                'required',
                'string',
                Rule::in(
                    ActivationCodeStatusEnum::AVAILABLE->value,
                    ActivationCodeStatusEnum::EXPIRED->value,
                    ActivationCodeStatusEnum::BLOCKED->value,
                )
            ],
            'billing_cycle' => 'required|string|in:month,year,lifetime',
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge(['status' => $this->status ?? ActivationCodeStatusEnum::AVAILABLE->value]);
    }
}
