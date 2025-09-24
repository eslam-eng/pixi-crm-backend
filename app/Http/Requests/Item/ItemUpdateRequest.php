<?php

namespace App\Http\Requests\Item;

use App\Enums\ServiceDuration;
use App\Enums\ServiceType;
use App\Http\Requests\BaseRequest;
use App\Models\Tenant\ItemAttribute;
use App\Models\Tenant\ItemAttributeValue;
use Illuminate\Validation\Rule;

class ItemUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $itemId = $this->route('id'); // Get the item ID from the route

        $baseRules = [
            'type' => 'required|in:product,service',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:item_categories,id',
        ];

        // Get the current item to determine its type
        $currentItem = null;
        if ($itemId) {
            $currentItem = \App\Models\Tenant\Item::with('itemable')->find($itemId);
        }

        // Get the requested type from the request
        $requestedType = $this->input('type');

        // Add product-specific rules if requested type is product
        if ($requestedType === 'product') {
            $baseRules['stock'] = 'required|integer|min:0';

            // Handle SKU uniqueness - if changing from service to product, no ignore needed
            if ($currentItem && $currentItem->isProduct) {
                $baseRules['sku'] = [
                    'required',
                    'string',
                    Rule::unique('products', 'sku')->ignore($currentItem->itemable_id)
                ];
            } else {
                $baseRules['sku'] = 'required|string|unique:products,sku';
            }

            $baseRules['variants'] = 'nullable|array';
            $baseRules['variants.*.attributes'] = 'required|array';
            $baseRules['variants.*.attributes.*.attribute_id'] = 'required|integer';
            $baseRules['variants.*.attributes.*.value_id'] = 'required|integer';
            $baseRules['variants.*.price'] = 'required|numeric|min:0';
            $baseRules['variants.*.stock'] = 'nullable|integer|min:0';
        }

        // Add service-specific rules if requested type is service
        if ($requestedType === 'service') {
            $baseRules['duration'] = [Rule::in(ServiceDuration::values()), 'required'];
            $baseRules['service_type'] = [Rule::in(ServiceType::values()), 'required'];
            $baseRules['is_recurring'] = 'nullable|boolean';
        }

        return $baseRules;
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Item type is required',
            'type.in' => 'Item type must be either product or service',
            'name.required' => 'Item name is required',
            'price.required' => 'Price is required',
            'stock.required' => 'Stock is required for products',
            'sku.required' => 'SKU is required for products',
            'sku.unique' => 'This SKU already exists',
            'duration.required' => 'Duration is required for services',
            'service_type.required' => 'Service type is required',
            'variants.*.attributes.required' => 'Variant attributes are required',
            'variants.*.attributes.*.attribute_id.required' => 'Attribute ID is required',
            'variants.*.attributes.*.value_id.required' => 'Attribute value ID is required',
            'variants.*.price.required' => 'Variant price is required',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {

        $validator->after(function ($validator) {
            $this->validateAttributesExist($validator);
            $this->validateAttributeValuesExist($validator);
            $this->validateUniqueVariantCombinations($validator);
            // $this->validateExistingVariantAttributes($validator);
            $this->validateDuplicateAttributesInVariant($validator);
            // $this->validateRequiredAttributes($validator);
        });
    }

    /**
     * Validate that all specified attributes exist in the database.
     */
    private function validateAttributesExist($validator): void
    {
        $variants = $this->input('variants', []);
        $allAttributes = collect();

        // Collect all unique attribute slugs
        foreach ($variants as $variant) {
            foreach ($variant['attributes'] ?? [] as  $attribute) {
                $allAttributes->push($attribute['attribute_id']);
            }
        }

        // Get existing attributes
        $existingAttributes = ItemAttribute::whereIn('id', $allAttributes->unique())
            ->pluck('id')
            ->toArray();

        // Check for missing attributes
        foreach ($variants as $variantIndex => $variant) {
            foreach ($variant['attributes'] ?? [] as $index => $attribute) {
                if (!in_array($attribute['attribute_id'], $existingAttributes)) {
                    $validator->errors()->add(
                        "variants.{$variantIndex}.attributes.{$index}",
                        "Attribute '{$attribute['attribute_id']}' does not exist."
                    );
                }
            }
        }
    }

    /**
     * Validate that all specified attribute values exist in the database.
     */
    private function validateAttributeValuesExist($validator): void
    {
        $variants = $this->input('variants', []);

        foreach ($variants as $variantIndex => $variant) {
            foreach ($variant['attributes'] ?? [] as $index => $attribute) {
                // Get the attribute
                $attributeInstance = ItemAttribute::where('id', $attribute['attribute_id'])->first();
                if ($attributeInstance) {
                    // Check if the value exists for this attribute
                    $attributeValue = ItemAttributeValue::where('item_attribute_id', $attributeInstance->id)
                        ->where('id', $attribute['value_id'])
                        ->first();

                    if (!$attributeValue) {
                        $validator->errors()->add(
                            "variants.{$variantIndex}.attributes.{$index}",
                            "Value '{$attribute['value_id']}' does not exist for attribute '{$attributeInstance->id}'."
                        );
                    }
                }
            }
        }
    }

    /**
     * Validate that variant combinations are unique within each product.
     */
    private function validateUniqueVariantCombinations($validator): void
    {
        $variants = $this->input('variants', []);
        $seenCombinations = [];

        foreach ($variants as $variantIndex => $variant) {
            $attributes = $variant['attributes'] ?? [];

            // Create a normalized combination key
            $attributePairs = [];
            foreach ($attributes as $attribute) {
                $attributePairs[] = $attribute['attribute_id'] . ':' . $attribute['value_id'];
            }
            sort($attributePairs); // Sort for consistent comparison
            $combinationKey = implode('|', $attributePairs);
            if (in_array($combinationKey, $seenCombinations)) {
                $attributesDisplay = collect($attributes)
                    ->map(fn($attr) => "Attribute {$attr['attribute_id']}: Value {$attr['value_id']}")
                    ->join(', ');

                $validator->errors()->add(
                    "variants.{$variantIndex}.attributes",
                    "Duplicate variant combination found: {$attributesDisplay}"
                );
            } else {
                $seenCombinations[] = $combinationKey;
            }
        }
    }

    /**
     * Validate that the same attribute is not added twice within the same variant.
     */
    private function validateDuplicateAttributesInVariant($validator): void
    {
        $variants = $this->input('variants', []);

        foreach ($variants as $variantIndex => $variant) {
            $attributes = $variant['attributes'] ?? [];
            $seenAttributeIds = [];

            foreach ($attributes as $attrIndex => $attribute) {
                $attributeId = $attribute['attribute_id'];

                if (in_array($attributeId, $seenAttributeIds)) {
                    $validator->errors()->add(
                        "variants.{$variantIndex}.attributes.{$attrIndex}.attribute_id",
                        "Attribute ID {$attributeId} is already used in this variant. Each attribute can only be used once per variant."
                    );
                } else {
                    $seenAttributeIds[] = $attributeId;
                }
            }
        }
    }

    /**
     * Validate that variant attributes don't already exist in the database for the same product.
     */
    private function validateExistingVariantAttributes($validator): void
    {
        $itemId = $this->route('item');
        $variants = $this->input('variants', []);

        if (empty($variants) || !$itemId) {
            return;
        }
        // Get the current item and its product
        $currentItem = \App\Models\Tenant\Item::with('itemable')->find($itemId);
        if (!$currentItem || !$currentItem->isProduct) {
            return;
        }

        $product = $currentItem->itemable;

        // Get all existing variants for this product
        $existingVariants = $product->variants()->with('attributeValues')->get();

        foreach ($variants as $variantIndex => $variant) {
            $newAttributes = $variant['attributes'] ?? [];

            foreach ($newAttributes as $attrIndex => $newAttribute) {
                $attributeId = $newAttribute['attribute_id'];
                $valueId = $newAttribute['value_id'];
                // Check if this attribute-value combination already exists in any variant
                foreach ($existingVariants as $existingVariant) {

                    $existingAttributeValues = $existingVariant->attributeValues()
                        ->where('item_variants_attribute_values.item_attribute_id', $attributeId)
                        ->where('item_attribute_values.id', $valueId)
                        ->exists();
                    if ($existingAttributeValues) {
                        $validator->errors()->add(
                            "variants.{$variantIndex}.attributes.{$attrIndex}",
                            "This attribute-value combination already exists in variant '{$existingVariant->sku}'"
                        );
                    }
                }
            }
        }
    }
}
