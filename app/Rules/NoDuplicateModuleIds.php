<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoDuplicateModuleIds implements ValidationRule
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

        // Extract module_id values from the array of objects
        $moduleIds = array_column($value, 'module_id');
        
        // Check for duplicates
        if (count($moduleIds) !== count(array_unique($moduleIds))) {
            $fail("The $attribute field contains duplicate module_id values.");
        }
    }
} 