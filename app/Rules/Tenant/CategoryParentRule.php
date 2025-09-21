<?php

namespace App\Rules\Tenant;

use App\Enums\ItemType;
use App\Models\Tenant\ItemCategory;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CategoryParentRule implements ValidationRule
{

    public function __construct(public ?string $type) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value) {
            $itemCategory = ItemCategory::find($value);
            if (!$itemCategory) {
                $fail('The selected item category does not exist.');
            } else if ($itemCategory->type !== $this->type) {
                $fail('The selected item category is not of the same type.');
            } else if ($itemCategory->parent_id) {
                $fail('The selected item category is not root categroy.');
            }
        }
    }
}
