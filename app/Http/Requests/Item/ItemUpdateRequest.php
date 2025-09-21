<?php

namespace App\Http\Requests\Item;

use App\Enums\DealType;
use App\Enums\ItemType;
use App\Enums\ServiceDuration;
use App\Http\Requests\BaseRequest;
use App\Rules\Tenant\ItemCategoryRule;
use Illuminate\Validation\Rule;

class ItemUpdateRequest extends BaseRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|unique:items,name,' . $this->route('item'),
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'sku' => 'nullable|string|unique:items,sku,' . $this->route('item'),
            'quantity' => 'nullable|integer',
            'category_id' => ['nullable', new ItemCategoryRule($this->type)],
            'duration' => ['nullable', Rule::in(ServiceDuration::values())],
            'type' => ['nullable', Rule::in(ItemType::values())],
        ];
    }
}
