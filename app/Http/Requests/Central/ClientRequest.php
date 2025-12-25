<?php

namespace App\Http\Requests\Central;

use App\Enums\Landlord\IndustryEnum;
use App\Enums\CompanySizes;
use App\Enums\Landlord\SubscriptionBillingCycleEnum;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class ClientRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'company_name' => ['required', 'string'],
            'contact_name' => ['required', 'string'],
            'contact_email' => 'required|email|unique:users,email',
            'contact_phone' => 'nullable|string',
            'job_title' => 'nullable|string',
            'website' => 'nullable|string',
            'company_size' => ['nullable', Rule::enum(CompanySizes::class)],
            'industry' => ['nullable', Rule::enum(IndustryEnum::class)],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'postal_code' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'subdomain' => ['required', 'string', 'unique:tenants,name'],
            'package_id' => ['required', 'integer', 'exists:plans,id'],
            'period_type' => ['required', Rule::enum(SubscriptionBillingCycleEnum::class)],
            'subscription_start' => ['required', 'date'],
            'send_password_email' => ['nullable', 'boolean'],
            'status' => ['nullable', 'boolean'],
            'note' => ['nullable', 'text'],
        ];
    }
}
