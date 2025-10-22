<?php

namespace App\Http\Requests\Central;

use App\Enum\ActivationStatusEnum;
use App\Http\Requests\BaseFormRequest;
use App\Rules\ValidCurrencyCode;
use Illuminate\Validation\Rule;

class PlanRequest extends BaseFormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('plans', 'name')->whereNull('deleted_at')->ignore($this->plan),
            ],
            'description' => 'string|nullable',
            'monthly_price' => 'nullable|numeric|min:1|required_without_all:annual_price,lifetime_price',
            'annual_price' => 'nullable|numeric|min:1|required_without_all:monthly_price,lifetime_price',
            'lifetime_price' => 'nullable|numeric|min:1|required_without_all:monthly_price,annual_price',
            'is_active' => 'required|boolean',
            'trial_days' => 'nullable|integer|min:0',
            'currency_code' => ['required', 'string', new ValidCurrencyCode],
            'refund_days' => 'nullable|integer|min:0',
            'features' => 'nullable|array|min:1',
            'limits' => 'nullable|array||min:1',
            'monthly_credit_tokens' => 'required|integer|min:0',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active', ACtivationStatusEnum::ACTIVE->value),
            'currency_code' => 'USD',
        ]);
    }
}
