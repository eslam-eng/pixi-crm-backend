<?php

namespace App\Http\Requests\Item;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Tenant\ItemAttribute;
use App\Models\Tenant\ItemAttributeValue;
use App\Models\Tenant\ItemVariant;

class UpdateProductVariantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust based on your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $variant = $this->route('variant');

        return [
            'attributes' => [
                'nullable',
                'array',
                'min:1',
                'max:10'
            ],
            'attributes.*' => [
                'required_with:attributes',
                'string',
                'min:1',
                'max:100'
            ],
            'price' => [
                'nullable',
                'numeric',
                'min:0.01',
                'max:999999.99',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            'stock' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999'
            ],
            'is_active' => [
                'nullable',
                'boolean'
            ],
            'sku' => [
                'nullable',
                'string',
                'min:2',
                'max:100',
                'regex:/^[A-Z0-9\-_]+$/i',
                function ($attribute, $value, $fail) use ($variant) {
                    // Check if SKU exists (excluding current variant)
                    if (ItemVariant::where('sku', $value)->where('id', '!=', $variant->id)->exists()) {
                        $fail("The SKU '{$value}' already exists.");
                    }
                }
            ]
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'attributes.array' => 'Attributes must be provided as an array.',
            'attributes.min' => 'At least one attribute is required when updating attributes.',
            'attributes.max' => 'Maximum of 10 attributes allowed per variant.',
            'attributes.*.required_with' => 'Attribute value is required.',
            'attributes.*.min' => 'Attribute value must be at least 1 character.',
            'attributes.*.max' => 'Attribute value cannot exceed 100 characters.',

            'price.numeric' => 'Price must be a valid number.',
            'price.min' => 'Price must be at least 0.01.',
            'price.max' => 'Price cannot exceed 999,999.99.',
            'price.regex' => 'Price can have maximum 2 decimal places.',

            'stock.integer' => 'Stock must be a valid integer.',
            'stock.min' => 'Stock cannot be negative.',
            'stock.max' => 'Stock cannot exceed 999,999.',

            'sku.min' => 'SKU must be at least 2 characters.',
            'sku.max' => 'SKU cannot exceed 100 characters.',
            'sku.regex' => 'SKU can only contain letters, numbers, hyphens, and underscores.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->has('attributes')) {
                $this->validateAttributesExist($validator);
                $this->validateAttributeValuesExist($validator);
                $this->validateUniqueVariantCombination($validator);
                $this->validateRequiredAttributes($validator);
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $data = [];

        // Normalize attributes
        if ($this->has('attributes')) {
            $attributes = [];
            foreach ($this->input('attributes', []) as $key => $value) {
                $attributes[strtolower(trim($key))] = strtolower(trim($value));
            }
            $data['attributes'] = $attributes;
        }

        // Normalize other fields
        if ($this->has('price')) {
            $data['price'] = round(floatval($this->input('price')), 2);
        }

        if ($this->has('sku')) {
            $data['sku'] = strtoupper(trim($this->input('sku')));
        }

        $this->merge($data);
    }

    /**
     * Validate that all specified attributes exist.
     */
    private function validateAttributesExist($validator): void
    {
        $attributes = $this->input('attributes', []);
        $attributeSlugs = array_keys($attributes);

        $existingAttributes = ItemAttribute::whereIn('id', $attributeSlugs)->pluck('id')->toArray();
        $missingAttributes = array_diff($attributeSlugs, $existingAttributes);

        foreach ($missingAttributes as $missingAttribute) {
            $validator->errors()->add(
                "attributes.{$missingAttribute}",
                "Attribute '{$missingAttribute}' does not exist."
            );
        }
    }

    /**
     * Validate that all specified attribute values exist.
     */
    private function validateAttributeValuesExist($validator): void
    {
        $attributes = $this->input('attributes', []);

        foreach ($attributes as $attributeSlug => $value) {
            $attribute = ItemAttribute::where('id', $attributeSlug)->first();

            if ($attribute) {
                $attributeValue = ItemAttributeValue::where('item_attribute_id', $attribute->id)
                    ->where('value', $value)
                    ->first();

                if (!$attributeValue) {
                    $validator->errors()->add(
                        "attributes.{$attributeSlug}",
                        "Value '{$value}' does not exist for attribute '{$attribute->id}'."
                    );
                }
            }
        }
    }

    /**
     * Validate that the variant combination is unique within the product.
     */
    private function validateUniqueVariantCombination($validator): void
    {
        $variant = $this->route('variant');
        $item = $variant->item;
        $attributes = $this->input('attributes', []);

        ksort($attributes);

        // Check if another variant of the same product has this combination
        $existingVariant = $item->variants()
            ->where('id', '!=', $variant->id)
            ->get()
            ->first(function ($otherVariant) use ($attributes) {
                $otherAttributes = $otherVariant->getAttributesArray();
                ksort($otherAttributes);
                return $otherAttributes === $attributes;
            });

        if ($existingVariant) {
            $attributesDisplay = collect($attributes)
                ->map(fn($value, $key) => "{$key}: {$value}")
                ->join(', ');

            $validator->errors()->add(
                'attributes',
                "This variant combination already exists: {$attributesDisplay}"
            );
        }
    }

    /**
     * Validate that all required attributes are provided.
     */
    private function validateRequiredAttributes($validator): void
    {
        $attributes = $this->input('attributes', []);
        $providedAttributes = array_keys($attributes);

        $requiredAttributes = ItemAttribute::pluck('id')->toArray();
        $missingAttributes = array_diff($requiredAttributes, $providedAttributes);

        foreach ($missingAttributes as $missingAttribute) {
            $attribute = ItemAttribute::where('id', $missingAttribute)->first();
            $attributeName = $attribute ? $attribute->id : $missingAttribute;

            $validator->errors()->add(
                "attributes.{$missingAttribute}",
                "Required attribute '{$attributeName}' is missing." . $missingAttribute
            );
        }
    }
}
