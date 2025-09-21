<?php

namespace App\Rules\Tenant;

use App\Models\Tenant\Contact;
use App\Models\Tenant\Item;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MaxContactPhonesRule implements ValidationRule
{
    public function __construct(public ?Contact $contact, public array $contact_phones = [], public bool $isMethodPost) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $countInsertenPhones = count($this->contact_phones);
        // if ($this->isMethodPost && $countInsertenPhones == 0) {
        //     $fail('The contact phones field is required. at least one phone is required.');
        // }
        if ($this->contact) {
            $existingPhones = $this->contact?->contactPhones->count();

            if ($existingPhones + $countInsertenPhones > 5) {
                $fail('The maximum number of contact phones is 5.');
            }
        }
    }
}
