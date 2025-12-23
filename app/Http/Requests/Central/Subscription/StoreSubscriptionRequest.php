<?php

namespace App\Http\Requests\Central\Subscription;

use App\Enums\Landlord\ActivationCodeStatusEnum;
use App\Enums\Landlord\ActivationMethodEnum;
use App\Enums\Landlord\SubscriptionBillingCycleEnum;
use App\Enums\Landlord\SubscriptionPaymentStatusEnum;
use App\Enums\Landlord\SubscriptionStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tenant_id' => ['required', 'exists:tenants,id'],
            'activation_method' => ['required', Rule::enum(ActivationMethodEnum::class)],
            'discount_code' => ['required_if:activation_method,' . ActivationMethodEnum::DISCOUNT_CODE->value, 'nullable', 'string'],
            'activation_code' => ['required_if:activation_method,' . ActivationMethodEnum::ACTIVATION_CODE->value, 'nullable', 'string'],
            'source_id' => ['nullable', 'exists:payout_source_collections,id'], // assuming sources table
            'billing_cycle' => ['required', Rule::enum(SubscriptionBillingCycleEnum::class)],
            'plan_id' => ['required', 'exists:plans,id'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'payment_status' => ['required', Rule::enum(SubscriptionPaymentStatusEnum::class)],
            'status' => [
                'required',
                Rule::in([
                    SubscriptionStatusEnum::ACTIVE->value,
                    SubscriptionStatusEnum::CANCELED->value, // 3
                    SubscriptionStatusEnum::SUSPENDED->value, // 5
                ])
            ],
            'auto_renew' => ['boolean'], // 0|1
            'notes' => ['nullable', 'string', 'max:2000'],
            'file' => ['nullable', 'string'],
        ];
    }
}
