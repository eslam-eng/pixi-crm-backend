<?php

namespace App\Http\Requests\Central\Subscribe;

use App\Enums\Landlord\SubscriptionBillingCycleEnum;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpgradeSubscriptionRequest extends BaseRequest
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
            'new_plan_id' => 'required|string',
            'discount_code' => 'nullable|string',
            'billing_cycle' => ['required', Rule::in(SubscriptionBillingCycleEnum::values())],
        ];
    }
}
