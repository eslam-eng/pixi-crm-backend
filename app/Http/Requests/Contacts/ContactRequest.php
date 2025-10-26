<?php

namespace App\Http\Requests\Contacts;

use App\Enums\CompanySizes;
use App\Enums\ContactMethods;
use App\Enums\ContactStatus;
use App\Enums\IndustryStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => [$this->requiredRule(), 'string', 'max:255'],
            'last_name' => 'nullable|string|max:255',
            'email' => [$this->requiredRule(), 'email', Rule::unique('contacts', 'email')->ignore($this->route('contact'))],
            'contact_phones' => [$this->requiredRule(), 'array', 'min:1'],
            'contact_phones.*.phone' => 'required|string|max:20',
            'contact_phones.*.is_primary' => 'sometimes|boolean',
            'contact_phones.*.enable_whatsapp' => 'sometimes|boolean',
            'job_title' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'status' => ['nullable', Rule::enum(ContactStatus::class)],
            'source_id' => 'nullable|exists:sources,id',
            'campaign_name' => 'nullable|string|max:255',

            // communication preferences
            'contact_method' => ['nullable', Rule::enum(ContactMethods::class)],
            'email_permission' => 'nullable|boolean',
            'phone_permission' => 'nullable|boolean',
            'whatsapp_permission' => 'nullable|boolean',

            // company info
            'company_name' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'industry' => ['nullable', Rule::enum(IndustryStatus::class)],
            'company_size' => ['nullable', Rule::enum(CompanySizes::class)],

            // address info
            'address' => 'nullable|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'city_id' => 'nullable|exists:cities,id',
            'state' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:255',

            // system fields
            'user_id' => 'nullable|exists:users,id',

            // tags
            'tags' => 'nullable|array',
            'tags.*' => 'nullable|string|max:255',

            // notes
            'notes' => 'nullable|string|max:255',
        ];
    }

    protected function requiredRule(): string
    {
        return $this->isMethod('POST') ? 'required' : 'sometimes';
    }
}
