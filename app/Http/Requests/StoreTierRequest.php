<?php

namespace App\Http\Requests;

use App\Enums\DurationUnits;
use App\Rules\NoDuplicateModuleIds;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTierRequest extends FormRequest
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
            'package_name' => 'required|string|max:255|unique:tiers,package_name',
            "description" => 'nullable|string|max:500',
            "price" => 'required|numeric|min:0',
            "duration_unit" => [Rule::enum(DurationUnits::class)],
            "duration" => 'required|numeric|min:0',
            "refund_period" => 'required|numeric|min:0',
            "max_sales" => 'nullable|integer|min:1',
            "max_contacts" => 'nullable|integer|min:1',
            "storage_limit" => 'nullable|integer|min:1',
            "modules" => ['nullable', 'array', new NoDuplicateModuleIds()],
            "modules.*.module_id" => ['required', Rule::exists('modules','id')],
            "modules.*.limit_value" => ['nullable', 'numeric', 'min:0'],
            "status" => 'required|in:active,inactive',
            "availability" => 'required|in:Public,Private',
        ];
    }
}
