<?php

namespace App\Http\Requests\Central\Subscribe;

use App\Enums\CompanySizes;
use App\Http\Requests\BaseRequest;
use App\Models\Setting;
use Illuminate\Validation\Rule;

class SubscribeRequest extends BaseRequest
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
     */
    public function rules(): array
    {
        $setting = Setting::first();
        $allowedIndustries = json_decode($setting?->industries ?? '[]', true);
        
        return [
            'tier_id' => 'required|exists:tiers,id',
            'payment_method' => 'required|string',

            'company_name' => 'required|string|unique:clients',
            'subdomain' => 'required|string|unique:clients',
            'contact_name' => 'nullable|string',
            'contact_email' => 'required|email|unique:clients',
            'contact_phone' => 'required|string|unique:clients',
            'job_title' => 'nullable|string',
            'website' => 'nullable|string',
            'company_size' => ['nullable', Rule::in(CompanySizes::values())],
            'industry' => ['nullable', Rule::in($allowedIndustries)],
            'city_id' => 'nullable|exists:cities,id',
            'postal_code' => 'nullable|string',
            'address' => 'nullable|string',
        ];
    }
}
