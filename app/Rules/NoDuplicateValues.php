<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoDuplicateValues implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail("The $attribute must be an array.");
            return;
        }

        if (count($value) !== count(array_unique($value))) {
            $fail("The $attribute field contains duplicate values.");
        }
    }
}
