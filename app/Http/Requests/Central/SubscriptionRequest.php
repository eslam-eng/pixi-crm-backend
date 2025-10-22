<?php

namespace App\Http\Requests\Central;

use App\Enum\SubscriptionBillingCycleEnum;
use App\Http\Requests\BaseFormRequest;
use App\Models\Landlord\DiscountCode;
use Illuminate\Validation\Rule;

class SubscriptionRequest extends BaseFormRequest
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
    public function rules()
    {
        return [
            'plan_id' => 'required|integer|exists:plans,id',
            'discount_code' => 'nullable|string',
            'duration_type' => ['required', Rule::in(SubscriptionBillingCycleEnum::values())],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $discount_code = $this->input('discount_code');
            $plan_id = $this->input('plan_id');
            if (! $discount_code) {
                return;
            }
            $discountCode = DiscountCode::query()
                ->where('discount_code', $discount_code)
                ->where('plan_id', $plan_id)
                ->first();

            if (! $discountCode) {
                $validator->errors()->add('discount_code', 'Invalid discount code');
            }
            // Check expiry
            if ($discountCode->expires_at && now()->greaterThan($discountCode->expires_at)) {
                $validator->errors()->add('discount_code', 'Discount code expired.');
            }

            // Check global usage for tenant
            if ($discountCode->usage_limit && $discountCode->usages()->count() >= $discountCode->usage_limit) {
                $validator->errors()->add('discount_code', 'Discount code has been fully used.');
            }
        });
    }
}
