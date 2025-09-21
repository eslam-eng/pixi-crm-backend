<?php

namespace App\Rules\Tenant;

use App\Models\Tenant\Item;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ItemQuantityRule implements ValidationRule
{
    public function __construct(public array $items)
    {
        dd($this->items);
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        foreach ($this->items as $item) {

            $item = Item::find($item['id']);
            if ($item) {
                if ($item->quantity < $item['quantity']) {
                    $fail('The quantity must be less than the item quantity.');
                }
            }
        }
    }
}
