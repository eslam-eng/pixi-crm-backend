<?php

namespace App\Http\Requests\Tenant\Opportunity;

use App\Enums\OpportunityStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OpportunityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // dd($this->all());
        $required = $this->isMethod('put') ? 'sometimes' : 'required';
        return [
            'contact_id' => $required . '|exists:contacts,id',
            'status' => [$required, Rule::in(OpportunityStatus::values())],
            'stage_id' => $required . '|exists:stages,id',
            'deal_value' => $this->isMethod('put') ? 'required_without:items|numeric' : 'items|numeric',
            'win_probability' => $this->isMethod('put') ? 'sometimes|numeric' : 'required|numeric',
            'expected_close_date' => $required . '|date',
            'assigned_to_id' => $required . '|exists:users,id',
            'notes' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'items' => 'nullable|array',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.variant_id' => 'nullable|exists:item_variants,id',
            'items.*.quantity' => ['nullable', 'integer', 'min:1'],
            'items.*.price' => 'nullable|numeric',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);
            foreach ($items as $index => $item) {
                if (isset($item['item_id']) && isset($item['variant_id'])) {
                    $this->validateItemVariantRelation($validator, $index, $item['item_id'], $item['variant_id']);
                }
            }
        });
    }

    /**
     * Validate that the variant belongs to the specified item
     */
    protected function validateItemVariantRelation($validator, $index, $itemId, $variantId)
    {
        $variant = \App\Models\Tenant\ItemVariant::find($variantId);

        if ($variant && $variant->product->item->id != $itemId) {
            $validator->errors()->add(
                "items.{$index}.variant_id",
                "The selected variant does not belong to the specified item."
            );
        }
    }
}
