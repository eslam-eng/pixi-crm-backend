<?php

namespace App\Services\Tenant;

use App\Models\Tenant\ItemAttribute;
use App\Models\Tenant\ItemAttributeValue;
use App\Models\Tenant\ItemVariant;
use App\Models\Tenant\Product;

class ProductVariantService
{
    public function createVariantsBulk(Product $product, array $variantsData)
    {
        $createdVariants = [];
        foreach ($variantsData as $variantData) {
            $variant = $this->createSingleVariant($product, $variantData);
            $createdVariants[] = $variant;
        }

        return $createdVariants;
    }

    private function createSingleVariant(Product $product, array $variantData)
    {
        $variant = $product->variants()->create([
            'sku' => $this->generateVariantSku($product, $variantData['attributes']),
            'price' => $variantData['price'],
            'stock' => $variantData['stock'] ?? 0
        ]);

        // Attach attribute values
        foreach ($variantData['attributes'] as $attribute) {
            $attributeInstanceId = ItemAttribute::where('id', $attribute['attribute_id'])->value('id');
            $attributeValueId = ItemAttributeValue::where('item_attribute_id', $attributeInstanceId)
                ->where('id', $attribute['value_id'])
                ->value('id');

            $variant->attributeValues()->attach($attributeValueId, [
                'item_attribute_id' => $attributeInstanceId
            ]);
        }

        return true;
    }

    private function generateVariantSku(Product $product, array $attributes)
    {
        $suffix = collect($attributes)
            ->map(fn($attribute) => strtoupper(substr($attribute['value_id'], 0, 2)))
            ->join('-');

        $sku = $product->sku . '-' . $suffix;

        // Ensure uniqueness
        $counter = 1;
        $originalSku = $sku;
        while (ItemVariant::where('sku', $sku)->exists()) {
            $sku = $originalSku . '-' . $counter;
            $counter++;
        }

        return $sku;
    }
}
