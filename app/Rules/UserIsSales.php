<?php

namespace App\Rules;

use App\Enums\UserType;
use App\Services\Tenant\Users\UserService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UserIsSales implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = app()->make(UserService::class)->findById($value);

        if (!$user) {
            $fail('The selected user does not exist.');
        }

        if ($user->type !== UserType::SALES->value) {
            $fail('The selected user must be a sales person.');
        }
    }
}
