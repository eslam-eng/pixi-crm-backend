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

    /**
     * Get the appropriate required rule based on HTTP method
     */
    protected function requiredRule(): string
    {
        return $this->isMethod('POST') ? 'required' : 'sometimes';
    }

    /**
     * Configure the validator instance
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateContactPhones($validator);
        });
    }

    /**
     * Validate contact phones structure and business rules
     */
    protected function validateContactPhones($validator): void
    {
        $contactPhones = $this->input('contact_phones', []);

        if (empty($contactPhones)) {
            return;
        }

        // Check for duplicate phone numbers
        $phoneNumbers = collect($contactPhones)->pluck('phone')->filter();
        if ($phoneNumbers->count() !== $phoneNumbers->unique()->count()) {
            $validator->errors()->add('contact_phones', 'Duplicate phone numbers are not allowed.');
        }

        // Check that only one phone is marked as primary
        $primaryCount = collect($contactPhones)
            ->where('is_primary', true)
            ->count();

        if ($primaryCount > 1) {
            $validator->errors()->add('contact_phones', 'Only one phone number can be marked as primary.');
        }

        // Validate phone number format
        foreach ($contactPhones as $index => $phone) {
            $phoneNumber = $phone['phone'] ?? '';
            if (!empty($phoneNumber) && !preg_match('/^[+]?[0-9\s\-\(\)]{7,20}$/', $phoneNumber)) {
                $validator->errors()->add(
                    "contact_phones.{$index}.phone",
                    'Please enter a valid phone number.'
                );
            }

            // Check if phone exists in database (for updates)
            if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
                $contactId = $this->route('contact')->id;
                // dd($contactId);
                $exists = \DB::table('contact_phones')
                    ->where('phone', $phoneNumber)
                    ->where('contact_id', '!=', $contactId)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add(
                        "contact_phones.{$index}.phone",
                        'This phone number is already in use by another contact.'
                    );
                }
            } else {
                // For store operations
                $exists = \DB::table('contact_phones')
                    ->where('phone', $phoneNumber)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add(
                        "contact_phones.{$index}.phone",
                        'This phone number is already in use.'
                    );
                }
            }
        }
    }
}
