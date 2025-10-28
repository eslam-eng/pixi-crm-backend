<?php

namespace App\Http\Requests\Central;

use App\Enums\Landlord\ActivationStatusEnum;
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
            //            'discount_type' => [
            //                'required',
            //                Rule::in(array_column(DiscountTypeEnum::cases(), 'value')),
            //            ],
            'discount_percentage' => [
                'required',
                'numeric',
                'min:0.01',
                'max:100',
            ],
            //            'users_limit' => [
            //                'nullable',
            //                'integer',
            //                'min:1',
            //                Rule::requiredIf(fn () => $this->discount_type == DiscountTypeEnum::MULTI_USE->value),
            //            ],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'expires_at' => ['required', 'date', 'date_format:Y-m-d', 'after:today'],
            'status' => [
                'sometimes',
                'boolean',
                Rule::in(ActivationStatusEnum::values()),
            ],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'discount_code' => strtoupper($this->discount_code),
            'status' => $this->boolean('status', true),
        ]);
    }
}
