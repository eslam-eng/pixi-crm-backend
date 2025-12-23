<?php

namespace App\Http\Requests\Central;

use App\Enums\Landlord\IndustryEnum;
use App\Enums\CompanySizes;
use App\Enums\Landlord\PackageTypePeriodEnum;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class ClientRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'company_name' => ['required', 'string'],
            'contact_name' => ['required', 'string'],
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string',
            'job_title' => 'nullable|string',
            'website' => 'nullable|string',
            'company_size' => ['nullable', Rule::enum(CompanySizes::values())],
            'industry' => ['nullable', Rule::enum(IndustryEnum::values())],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'postal_code' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'subdomain' => ['required', 'string'],
            'package_id' => ['required', 'exists:packages,id'],
            'period_type' => ['required', Rule::enum(PackageTypePeriodEnum::values())],
            'subscription_start' => ['required', 'date'],
            'send_password_email' => ['nullable', 'boolean'],
            'status' => ['nullable', 'boolean'],
            'note' => ['nullable', 'text'],
        ];
    }
}
