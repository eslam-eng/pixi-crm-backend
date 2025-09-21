<?php

namespace App\Http\Requests\Tenant\ItemCategory;

use App\Enums\ItemType;
use App\Http\Requests\BaseRequest;
use App\Rules\Tenant\CategoryParentRule;
use Illuminate\Validation\Rule;

class StoreItemCategoryRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:item_categories,name|max:255',
            'type' => ['required', Rule::in(ItemType::values())],
            'parent_id' => ['nullable', new CategoryParentRule($this->type)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Item category name is required.',
            'name.string' => 'Item category name must be a string.',
            'name.max' => 'Item category name may not be greater than 255 characters.',
            'parent_id.exists' => 'Selected parent item category does not exist.',
            'type.required' => 'Item category type is required.',
            'type.in' => 'Invalid item category type selected.',
            'parent_id.not_same_type' => 'Selected parent item category is not of the same type.',
        ];
    }
}
