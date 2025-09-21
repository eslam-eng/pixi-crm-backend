<?php

namespace Database\Seeders\tenant;

use App\Enums\ItemType;
use App\Models\Tenant\Item;
use App\Models\Tenant\ItemCategory;
use Illuminate\Database\Seeder;

class ProductWithVariantSeeder extends Seeder
{
    public function run(): void
    {

        if (Item::with('variants')->count() <= 3) {
            $category_id_product = ItemCategory::where('type', ItemType::PRODUCT->value)->whereNotNull('parent_id')->first()->id;

            // Create the base product
            $product = \App\Models\Tenant\Item::create([
                'name' => 'Sample Product',
                'sku' => 'SAMPLEPROD',
                'description' => 'A sample product with variants',
                'category_id' => $category_id_product, // Ensure this category exists
            ]);

            // Define the variants to create
            $variants = [
                [
                    'attributes' => [
                        'color' => 'red',
                        'size' => 'small'
                    ],
                    'price' => 99.99,
                    'stock' => 100
                ],
                [
                    'attributes' => [
                        'color' => 'blue',
                        'size' => 'medium'
                    ],
                    'price' => 109.99,
                    'stock' => 50
                ]
            ];

            foreach ($variants as $variantData) {
                // Generate SKU for variant
                $suffix = collect($variantData['attributes'])
                    ->map(fn($value, $key) => strtoupper(substr($key, 0, 1)) . strtoupper(substr($value, 0, 2)))
                    ->join('-');
                $variantSku = $product->sku . '-' . $suffix;

                // Create the variant
                $variant = \App\Models\Tenant\ItemVariant::create([
                    'item_id' => $product->id,
                    'sku' => $variantSku,
                    'price' => $variantData['price'],
                    'stock' => $variantData['stock'] ?? 0
                ]);

                // Attach attribute values
                foreach ($variantData['attributes'] as $attributeName => $value) {
                    $attribute = \App\Models\Tenant\ItemAttribute::where('name', $attributeName)->first();
                    if (!$attribute) {
                        // Optionally create the attribute if it doesn't exist
                        $attribute = \App\Models\Tenant\ItemAttribute::create(['name' => $attributeName]);
                    }
                    $attributeValue = \App\Models\Tenant\ItemAttributeValue::where('item_attribute_id', $attribute->id)
                        ->where('value', $value)
                        ->first();
                    if (!$attributeValue) {
                        // Optionally create the attribute value if it doesn't exist
                        $attributeValue = \App\Models\Tenant\ItemAttributeValue::create([
                            'item_attribute_id' => $attribute->id,
                            'value' => $value
                        ]);
                    }
                    $variant->attributeValues()->attach($attributeValue->id, [
                        'item_attribute_id' => $attribute->id
                    ]);
                }
            }
        }
    }
}
