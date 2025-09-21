<?php

namespace App\Http\Requests\Item;

use App\Enums\ItemType;
use App\Enums\ServiceDuration;
use App\Enums\ServiceType;
use App\Http\Requests\BaseRequest;
use App\Rules\Tenant\ItemCategoryRule;
use Illuminate\Validation\Rule;

class ItemStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:items,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'type' => ['required', Rule::in(ItemType::values())],
            'category_id' => ['required', new ItemCategoryRule($this->type)],
            'sku' => ['exclude_unless:type,product', 'required', 'string', 'unique:items,sku'],
            'quantity' => ['exclude_unless:type,product', 'required', 'integer'],
            'service_type' => ['exclude_unless:type,service', 'requiredIf:type,service', Rule::in(ServiceType::values())],
            'duration' => ['exclude_unless:service_type,recurring', 'exclude_unless:type,service', 'requiredIf:service_type,recurring', Rule::in(ServiceDuration::values())],
        ];
    }
}
