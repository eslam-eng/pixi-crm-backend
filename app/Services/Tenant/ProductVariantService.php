<?php

namespace App\Services\Tenant;

use App\Models\Tenant\Item;
use App\Models\Tenant\ItemAttribute;
use App\Models\Tenant\ItemAttributeValue;
use App\Models\Tenant\ItemVariant;
use Illuminate\Support\Facades\DB;

class ProductVariantService
{
    public function createVariantsBulk(Item $product, array $variantsData)
    {
        DB::beginTransaction();

        try {
            $createdVariants = [];

            foreach ($variantsData as $variantData) {
                $variant = $this->createSingleVariant($product, $variantData);
                $createdVariants[] = $variant;
            }

            DB::commit();
            return $createdVariants;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    private function createSingleVariant(Item $product, array $variantData)
    {
        // Create variant
        $variant = ItemVariant::create([
            'item_id' => $product->id,
            'sku' => $this->generateVariantSku($product, $variantData['attributes']),
            'price' => $variantData['price'],
            'stock' => $variantData['stock'] ?? 0
        ]);

        // Attach attribute values
        foreach ($variantData['attributes'] as $attributeSlug => $valueSlug) {
            $attribute = ItemAttribute::where('slug', $attributeSlug)->firstOrFail();
            $attributeValue = ItemAttributeValue::where('attribute_id', $attribute->id)
                ->where('slug', $valueSlug)
                ->firstOrFail();

            $variant->attributeValues()->attach($attributeValue->id, [
                'attribute_id' => $attribute->id
            ]);
        }

        return $variant;
    }

    private function generateVariantSku(Item $product, array $attributes)
    {
        $suffix = collect($attributes)
            ->map(fn($value) => strtoupper(substr($value, 0, 2)))
            ->join('-');

        $sku = $product->base_sku . '-' . $suffix;

        // Ensure uniqueness
        $counter = 1;
        $originalSku = $sku;
        while (ItemVariant::where('sku', $sku)->exists()) {
            $sku = $originalSku . '-' . $counter;
            $counter++;
        }

        return $sku;
    }

    public function getAvailableAttributesForProduct(Item $product)
    {
        return $product->attributes()
            ->with(['values' => function ($query) {
                $query->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();
    }
}
