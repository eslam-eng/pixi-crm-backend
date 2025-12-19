<?php

namespace App\Http\Requests\Central;

use App\Enums\Landlord\ActivationCodeStatusEnum;
use App\Enums\Landlord\DiscountUsageEnum;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class DiscountCodeRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'discount_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('discount_codes', 'discount_code')
                    ->ignore($this->route('discount_code')),
            ],
            'plan_id' => ['required', 'exists:plans,id'],
            'discount_percentage' => [
                'required',
                'numeric',
                'min:0.01',
                'max:100',
            ],
            'discount_type' => [
                'required',
                Rule::in(DiscountUsageEnum::values()),
            ],
            'usage_limit' => [
                'nullable',
                'integer',
                'min:1',
                Rule::requiredIf($this->discount_type === 'multi_use'),
            ],
            'users_limit' => ['required', 'integer', 'min:1'],
            'expires_at' => ['required', 'date', 'after:today'],
            'status' => [
                'required',
                Rule::in(
                    ActivationCodeStatusEnum::AVAILABLE->value,
                    ActivationCodeStatusEnum::EXPIRED->value,
                    ActivationCodeStatusEnum::BLOCKED->value,
                ),
            ],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'discount_code' => strtoupper($this->discount_code),
        ]);
    }
}
