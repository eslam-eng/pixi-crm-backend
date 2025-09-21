<?php

namespace App\Http\Requests;

use App\Enums\ActivationStatus;
use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDiscountCodeRequest extends FormRequest
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
        $setting = Setting::first();
        $allowedSources = json_decode($setting?->sources ?? '[]', true);
        return [
            'code' => 'required|string|max:255|unique:discount_codes,code',
            'tier_id' => 'required|exists:tiers,id',
            'status' => ['required', Rule::in(ActivationStatus::values())],
            'source' => ['required', Rule::in($allowedSources)],
            'trial_days' => 'required|integer|min:0',
            'expires_at' => 'required|date|after:now',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'usage_type' => ['required', Rule::in(['one-time-use', 'multi-use', 'unlimited-use'])],
            'max_uses' => 'required|integer|min:0',
        ];
    }
}
