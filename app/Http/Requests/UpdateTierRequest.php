<?php

namespace App\Http\Requests;

use App\Enums\DurationUnits;
use App\Enums\ModuleType;
use App\Rules\NoDuplicateValues;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTierRequest extends FormRequest
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
            'package_name' => 'nullable|string|max:255|unique:tiers,package_name',
            "description" => 'nullable|string|max:500',
            "price" => 'nullable|numeric|min:0',
            "duration_unit" => [Rule::enum(DurationUnits::class)],
            "duration" => 'nullable|numeric|min:0',
            "refund_period" => 'nullable|numeric|min:0',
            "max_users" => 'nullable|integer|min:1',
            "max_contacts" => 'nullable|integer|min:1',
            "storage_limit" => 'nullable|integer|min:1',
            "modules" => ['nullable', 'array', new NoDuplicateValues()],
            "modules.*" => [Rule::enum(ModuleType::class)],
            "status" => 'nullable|in:active,inactive',
            "availability" => 'nullable|in:Public,Private',
        ];
    }
}
