<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCurrencyCode implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $file = database_path('currencies.json');

        $jsonContents = file_get_contents($file);

        $currenciesData = json_decode($jsonContents, true); // decode as associative array
        $currenciesData = array_column($currenciesData, 'code');
        if (! in_array($value, $currenciesData)) {
            $fail('validation.currency_code')->translate();
        }
    }
}
