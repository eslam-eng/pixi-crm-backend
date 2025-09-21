<?php

namespace App\Http\Requests\Clients;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:clients,email',
            'address' => 'required|string|max:255',
            'city_id' => 'nullable|integer|exists:locations,id',
            'source_id' => 'nullable|integer|exists:sources,id',
            'customFields' => 'nullable|array',
            'customFields.*.custom_field_id' => 'integer|exists:custom_fields,id',
            'customFields.*.value' => 'required|string',
        ];
    }
    /**
     * Get custom messages for validation errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'phone.required' => 'The phone field is required.',
            'email.required' => 'The email field is required.',
            'email.unique' => 'The email has already been taken.',
            'address.required' => 'The address field is required.',
            'resource_id.required' => 'The resource ID is required.',
            'resource_id.exists' => 'The selected resource is invalid.',
            'industries.*.exists' => 'One or more selected industries are invalid.',
            'services.*.service_id.exists' => 'One or more selected services are invalid.',
            'services.*.category_id.exists' => 'One or more selected categories are invalid.',
            'customFields.*.custom_field_id.exists' => 'One or more selected custom fields are invalid.',
            'customFields.*.value.required' => 'The value for each custom field is required.',
        ];
    }
}
