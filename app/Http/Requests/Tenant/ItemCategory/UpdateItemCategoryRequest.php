<?php

namespace App\Http\Requests\Tenant\ItemCategory;

use App\Enums\ItemType;
use App\Http\Requests\BaseRequest;
use App\Rules\Tenant\CategoryParentRule;
use Illuminate\Validation\Rule;

class UpdateItemCategoryRequest extends BaseRequest
{
    public function rules(): array
    {
        // dd($this->route('item_category'));
        return [
            'name' => 'required|string|unique:item_categories,name,' . $this->route('item_category') . '|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Item category name is required.',
            'name.string' => 'Item category name must be a string.',
            'name.max' => 'Item category name may not be greater than 255 characters.',
        ];
    }
}
