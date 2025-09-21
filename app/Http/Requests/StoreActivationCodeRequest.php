<?php

namespace App\Http\Requests;

use App\Enums\ActivationStatus;
use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreActivationCodeRequest extends FormRequest
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
            'number_of_codes' => 'required_without:code|integer|min:1',
            'code_parts'      => 'required_without:code|integer|min:3|max:6',
            'part_length'     => 'required_without:code|integer|min:3|max:6',

            'code' => 'required_without_all:number_of_codes,code_parts,part_length|string|max:255|unique:activation_codes,code',
            'tier_id' => 'required|exists:tiers,id',
            'status' => ['required', Rule::in(ActivationStatus::values())],
            'source' => ['required', Rule::in($allowedSources)],
            'trial_days' => 'required|integer|min:0',
            'expires_at' => 'required|date|after:now',
            'used_at' => 'nullable|date|after:created_at',
        ];
    }

}
