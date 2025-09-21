<?php

namespace App\Http\Requests;

use App\Enums\ActivationStatus;
use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateActivationCodeRequest extends FormRequest
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
        // dd($this->route('activation_code'));
        $setting = Setting::first();
        $allowedSources = json_decode($setting?->sources ?? '[]', true);
        return [
            'code' => [
                'required_without_all:number_of_codes,code_parts,part_length',
                'string',
                'max:255',
                Rule::unique('activation_codes', 'code')->ignore($this->route('activation_code'))
            ],
            'create_by' => 'sometimes|exists:users,id',
            'tier_id' => 'sometimes|exists:tiers,id',
            'status' => ['sometimes', Rule::in(ActivationStatus::values())],
            'source' => ['sometimes', Rule::in($allowedSources)],
            'trial_days' => 'sometimes|integer|min:0',
            'expires_at' => 'sometimes|date|after:now',
            'used_at' => 'nullable|date|after:created_at',
        ];
    }
}
