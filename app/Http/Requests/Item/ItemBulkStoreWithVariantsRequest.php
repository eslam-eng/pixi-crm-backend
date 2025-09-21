<?php
// app/Http/Requests/BulkCreateProductsWithVariantsRequest.php

namespace App\Http\Requests\Item;

use App\Http\Requests\BaseRequest;
use App\Models\Tenant\Item;
use App\Models\Tenant\ItemAttribute;
use App\Models\Tenant\ItemAttributeValue;

class ItemBulkStoreWithVariantsRequest extends BaseRequest
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
        return [
            // Products array validation
            'products' => [
                'required',
                'array',
                'min:1',
                'max:50' // Limit bulk creation to prevent abuse
            ],

            // Individual product validation
            'products.*.name' => [
                'required',
                'string',
                'min:2',
                'max:255'
            ],
            'products.*.sku' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[A-Z0-9\-_]+$/i', // Only alphanumeric, hyphens, underscores
                function ($attribute, $value, $fail) {
                    // Check if SKU exists in database
                    if (Item::where('sku', $value)->exists()) {
                        $fail("The SKU '{$value}' already exists.");
                    }
                }
            ],
            'products.*.description' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'products.*.category_id' => [
                'required',
                'exists:item_categories,id',
            ],

            // Variants validation
            'products.*.variants' => [
                'required',
                'array',
                'min:1',
                'max:100' // Limit variants per product
            ],

            // Individual variant validation
            'products.*.variants.*.attributes' => [
                'required',
                'array',
                'min:1',
                'max:10' // Limit attributes per variant
            ],
            'products.*.variants.*.attributes.*' => [
                'required',
                'integer',
                'min:1',
                'max:100'
            ],
            'products.*.variants.*.price' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99',
                'regex:/^\d+(\.\d{1,2})?$/' // Ensure max 2 decimal places
            ],
            'products.*.variants.*.stock' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999'
            ]
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            // Products messages
            'products.required' => 'At least one product is required.',
            'products.array' => 'Products must be provided as an array.',
            'products.min' => 'At least one product is required.',
            'products.max' => 'Maximum of 50 products can be created at once.',

            // Product field messages
            'products.*.name.required' => 'Product name is required.',
            'products.*.name.min' => 'Product name must be at least 2 characters.',
            'products.*.name.max' => 'Product name cannot exceed 255 characters.',

            'products.*.sku.required' => 'SKU is required.',
            'products.*.sku.min' => 'SKU must be at least 2 characters.',
            'products.*.sku.max' => 'SKU cannot exceed 100 characters.',
            'products.*.sku.regex' => 'SKU can only contain letters, numbers, hyphens, and underscores.',

            'products.*.description.max' => 'Description cannot exceed 1000 characters.',

            // Variants messages
            'products.*.variants.required' => 'Each product must have at least one variant.',
            'products.*.variants.array' => 'Variants must be provided as an array.',
            'products.*.variants.min' => 'Each product must have at least one variant.',
            'products.*.variants.max' => 'Maximum of 100 variants allowed per product.',

            // Variant field messages
            'products.*.variants.*.attributes.required' => 'Variant attributes are required.',
            'products.*.variants.*.attributes.array' => 'Variant attributes must be provided as an array.',
            'products.*.variants.*.attributes.min' => 'Each variant must have at least one attribute.',
            'products.*.variants.*.attributes.max' => 'Maximum of 10 attributes allowed per variant.',
            'products.*.variants.*.attributes.*.required' => 'Attribute value is required.',
            'products.*.variants.*.attributes.*.min' => 'Attribute value must be at least 1 character.',
            'products.*.variants.*.attributes.*.max' => 'Attribute value cannot exceed 100 characters.',

            'products.*.variants.*.price.required' => 'Variant price is required.',
            'products.*.variants.*.price.numeric' => 'Price must be a valid number.',
            'products.*.variants.*.price.min' => 'Price must be at least 0.01.',
            'products.*.variants.*.price.max' => 'Price cannot exceed 999,999.99.',
            'products.*.variants.*.price.regex' => 'Price can have maximum 2 decimal places.',

            'products.*.variants.*.stock.integer' => 'Stock must be a valid integer.',
            'products.*.variants.*.stock.min' => 'Stock cannot be negative.',
            'products.*.variants.*.stock.max' => 'Stock cannot exceed 999,999.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {

        $validator->after(function ($validator) {
            $this->validateUniqueSkusInRequest($validator);
            $this->validateAttributesExist($validator);
            $this->validateAttributeValuesExist($validator);
            $this->validateUniqueVariantCombinations($validator);
            //     $this->validateRequiredAttributes($validator);
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean and normalize data
        $products = $this->input('products', []);

        foreach ($products as $productIndex => $product) {
            // Normalize product data
            if (isset($product['name'])) {
                $products[$productIndex]['name'] = trim($product['name']);
            }

            if (isset($product['sku'])) {
                $products[$productIndex]['sku'] = strtoupper(trim($product['sku']));
            }

            if (isset($product['description'])) {
                $products[$productIndex]['description'] = trim($product['description']);
            }

            // Normalize variant data
            if (isset($product['variants'])) {
                foreach ($product['variants'] as $variantIndex => $variant) {
                    if (isset($variant['attributes'])) {
                        foreach ($variant['attributes'] as $attrKey => $attrValue) {
                            $products[$productIndex]['variants'][$variantIndex]['attributes'][$attrKey] =
                                strtolower(trim($attrValue));
                        }
                    }

                    if (isset($variant['price'])) {
                        $products[$productIndex]['variants'][$variantIndex]['price'] =
                            round(floatval($variant['price']), 2);
                    }
                }
            }
        }

        $this->merge(['products' => $products]);
    }

    /**
     * Validate that SKUs are unique within the request.
     */
    private function validateUniqueSkusInRequest($validator): void
    {
        $products = $this->input('products', []);
        $skus = [];

        foreach ($products as $productIndex => $product) {
            $sku = $product['sku'] ?? null;

            if ($sku) {
                if (in_array($sku, $skus)) {
                    $validator->errors()->add(
                        "products.{$productIndex}.sku",
                        "Duplicate SKU '{$sku}' found in the request."
                    );
                } else {
                    $skus[] = $sku;
                }
            }
        }
    }

    /**
     * Validate that all specified attributes exist in the database.
     */
    private function validateAttributesExist($validator): void
    {
        $products = $this->input('products', []);
        $allAttributes = collect();

        // Collect all unique attribute slugs
        foreach ($products as $product) {
            foreach ($product['variants'] ?? [] as $variant) {
                foreach ($variant['attributes'] ?? [] as $attributeSlug => $value) {
                    $allAttributes->push($attributeSlug);
                }
            }
        }

        // Get existing attributes
        $existingAttributes = ItemAttribute::whereIn('id', $allAttributes->unique())
            ->pluck('id')
            ->toArray();

        // Check for missing attributes
        foreach ($products as $productIndex => $product) {
            foreach ($product['variants'] ?? [] as $variantIndex => $variant) {
                foreach ($variant['attributes'] ?? [] as $attributeSlug => $value) {
                    if (!in_array($attributeSlug, $existingAttributes)) {
                        $validator->errors()->add(
                            "products.{$productIndex}.variants.{$variantIndex}.attributes.{$attributeSlug}",
                            "Attribute '{$attributeSlug}' does not exist."
                        );
                    }
                }
            }
        }
    }

    /**
     * Validate that all specified attribute values exist in the database.
     */
    private function validateAttributeValuesExist($validator): void
    {
        $products = $this->input('products', []);

        foreach ($products as $productIndex => $product) {
            foreach ($product['variants'] ?? [] as $variantIndex => $variant) {
                foreach ($variant['attributes'] ?? [] as $attribute_id => $value_id) {
                    // Get the attribute
                    $attribute = ItemAttribute::where('id', $attribute_id)->first();
                    if ($attribute) {
                        // Check if the value exists for this attribute
                        $attributeValue = ItemAttributeValue::where('item_attribute_id', $attribute->id)
                            ->where('id', $value_id)
                            ->first();

                        if (!$attributeValue) {
                            $validator->errors()->add(
                                "products.{$productIndex}.variants.{$variantIndex}.attributes.{$attribute_id}",
                                "Value '{$value_id}' does not exist for attribute '{$attribute->id}'."
                            );
                        }
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
        $products = $this->input('products', []);

        foreach ($products as $productIndex => $product) {
            $variants = $product['variants'] ?? [];
            $seenCombinations = [];

            foreach ($variants as $variantIndex => $variant) {
                $attributes = $variant['attributes'] ?? [];

                // Sort attributes by key for consistent comparison
                ksort($attributes);
                $combinationKey = json_encode($attributes);

                if (in_array($combinationKey, $seenCombinations)) {
                    $attributesDisplay = collect($attributes)
                        ->map(fn($value, $key) => "{$key}: {$value}")
                        ->join(', ');

                    $validator->errors()->add(
                        "products.{$productIndex}.variants.{$variantIndex}.attributes",
                        "Duplicate variant combination found: {$attributesDisplay}"
                    );
                } else {
                    $seenCombinations[] = $combinationKey;
                }
            }
        }
    }

    /**
     * Validate that all required attributes are provided for each variant.
     */
    private function validateRequiredAttributes($validator): void
    {
        $products = $this->input('products', []);

        // Get all required attributes
        $requiredAttributes = ItemAttribute::where('is_required', true)
            ->pluck('id')
            ->toArray();

        if (empty($requiredAttributes)) {
            return; // No required attributes
        }

        foreach ($products as $productIndex => $product) {
            foreach ($product['variants'] ?? [] as $variantIndex => $variant) {
                $providedAttributes = array_keys($variant['attributes'] ?? []);
                $missingAttributes = array_diff($requiredAttributes, $providedAttributes);

                foreach ($missingAttributes as $missingAttribute) {
                    $attribute = ItemAttribute::where('id', $missingAttribute)->first();
                    $attributeName = $attribute ? $attribute->id : $missingAttribute;

                    $validator->errors()->add(
                        "products.{$productIndex}.variants.{$variantIndex}.attributes.{$missingAttribute}",
                        "Required attribute '{$attributeName}' is missing."
                    );
                }
            }
        }
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        $attributes = [];
        $products = $this->input('products', []);

        foreach ($products as $productIndex => $product) {
            $attributes["products.{$productIndex}.name"] = "product #" . ($productIndex + 1) . " name";
            $attributes["products.{$productIndex}.sku"] = "product #" . ($productIndex + 1) . " SKU";
            $attributes["products.{$productIndex}.description"] = "product #" . ($productIndex + 1) . " description";

            foreach ($product['variants'] ?? [] as $variantIndex => $variant) {
                $variantNum = $variantIndex + 1;
                $productNum = $productIndex + 1;

                $attributes["products.{$productIndex}.variants.{$variantIndex}.price"] =
                    "product #{$productNum}, variant #{$variantNum} price";
                $attributes["products.{$productIndex}.variants.{$variantIndex}.stock"] =
                    "product #{$productNum}, variant #{$variantNum} stock";
                $attributes["products.{$productIndex}.variants.{$variantIndex}.attributes"] =
                    "product #{$productNum}, variant #{$variantNum} attributes";

                foreach ($variant['attributes'] ?? [] as $attrKey => $attrValue) {
                    $attributes["products.{$productIndex}.variants.{$variantIndex}.attributes.{$attrKey}"] =
                        "product #{$productNum}, variant #{$variantNum} {$attrKey}";
                }
            }
        }

        return $attributes;
    }

    /**
     * Handle a failed validation attempt.
     */
    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        $errors = $validator->errors()->toArray();

        // Log validation errors for debugging
        \Log::info('Bulk product creation validation failed', [
            'errors' => $errors,
            'input' => $this->all()
        ]);

        parent::failedValidation($validator);
    }
}
