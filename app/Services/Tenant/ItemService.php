<?php

namespace App\Services\Tenant;

use App\Exceptions\GeneralException;
use App\Models\Tenant\Item;
use Illuminate\Database\Eloquent\Builder;
use App\DTO\Item\ItemDTO;
use App\DTO\Item\ProductDTO;
use App\DTO\Item\ServiceDTO;

use App\Models\Filters\ItemFilter;
use App\Models\Tenant\ItemAttribute;
use App\Models\Tenant\Product;
use App\Models\Tenant\Service;
use App\Services\BaseService;
use App\Services\Tenant\ProductVariantService;
use Illuminate\Support\Facades\DB;

class ItemService extends BaseService
{
    public function __construct(
        public Item $model,
        public Product $productModel,
        public Service $serviceModel,
        public ItemAttribute $itemAttribute,
        public ProductVariantService $productVariantService,
    ) {}

    public function getModel(): Item
    {
        return $this->model;
    }

    public function getAll(array $filters = [])
    {
        return $this->queryGet($filters)->get();
    }

    public function getTableName(): string
    {
        return $this->getModel()->getTable();
    }

    public function listing(array $filters = [], array $withRelations = [], $perPage = 5): \Illuminate\Contracts\Pagination\CursorPaginator
    {
        return $this->queryGet(filters: $filters, withRelations: $withRelations)->cursorPaginate($perPage);
    }

    public function queryGet(array $filters = [], array $withRelations = []): Builder
    {
        $defaultRelations = ['category', 'itemable'];
        $withRelations = array_merge($defaultRelations, $withRelations);
        $items = $this->model->with($withRelations)->ordered();
        return $items->filter(new ItemFilter($filters));
    }

    public function index(array $filters = [], array $withRelations = [], ?int $perPage = null)
    {
        $query = $this->queryGet(filters: $filters, withRelations: $withRelations);
        if ($perPage) {
            return $query->paginate($perPage);
        }
        return $query->get();
    }

    public function store($request)
    {
        $commonData = ItemDTO::fromRequest($request)->toArray();
        $type = $request->type;
        if ($type === 'product') {
            $item = $this->createProduct($commonData, ProductDTO::fromRequest($request));
        } elseif ($type === 'service') {
            $item = $this->createService($commonData, ServiceDTO::fromRequest($request));
        } else {
            throw new \Exception('Invalid item type. Must be "product" or "service"');
        }
        return $item;
    }

    public function show(int $id)
    {
        $item = $this->findById($id, withRelations: ['itemable']);

        return $item;
    }

    public function update(int $id, $request): Item
    {
        $item = $this->findById($id, withRelations: ['itemable']);
        $commonData = ItemDTO::fromRequest($request)->toArray();
        $requestedType = $request->input('type');

        // Check if we need to change the item type
        if ($this->needsTypeChange($item, $requestedType)) {
            $this->changeItemType($item, $requestedType, $request);
        } else {
            // Update without changing type
            $item->update($commonData);

            if ($item->isProduct) {
                $this->updateProduct($item, $request);
            } elseif ($item->isService) {
                $this->updateService($item, $request);
            }
        }
        return $item->fresh(['itemable']);
    }

    public function destroy(int $id): bool
    {
        $item = $this->findById($id, withRelations: ['itemable']);
        $itemType = $item->itemable_type;

        if ($item->opportunities()->exists()) {
            throw new GeneralException(__('app.cannot_delete_item_used_by_opportunities'));
        }

        if ($item->isProduct) {
            $this->deleteProductItem($item);
        } else {
            $this->deleteServiceItem($item);
        }

        return true;
    }

    /**
     * Delete product item with all its variants and relationships
     */
    private function deleteProductItem(Item $item): void
    {
        $product = $item->itemable;

        if ($product) {
            // Delete all variants and their relationships
            $this->deleteProductVariants($product);

            // Delete the product
            $product->delete();
        }

        // Finally delete the item
        $item->delete();
    }

    /**
     * Delete service item (simpler - no variants)
     */
    private function deleteServiceItem(Item $item): void
    {
        $service = $item->itemable;

        if ($service) {
            $service->delete();
        }

        $item->delete();
    }

    private function deleteProductVariants(Product $product): void
    {
        // Get all variant IDs first
        $variantIds = $product->variants()->pluck('id');

        if ($variantIds->isNotEmpty()) {
            // Delete variant attributes (pivot table)
            DB::table('item_variants_attribute_values')
                ->whereIn('variant_id', $variantIds)
                ->delete();

            // Delete the variants
            $product->variants()->delete();
        }
    }



    public function getAttributes()
    {
        return $this->itemAttribute->ordered()->get();
    }

    public function getAttribute(string $attribute)
    {
        return $this->itemAttribute->where('id', $attribute)->get();
    }

    public function storeAttributes(array $data)
    {
        return $this->itemAttribute->create($data);
    }

    public function destroyAttributes(string $attribute)
    {
        $attribute = $this->itemAttribute->where('id', $attribute)->first();
        if ($attribute->items()->exists()) {
            throw new GeneralException(__('app.cannot_delete_attribute_used_by_items_or_products'));
        }
        return $attribute->delete();
    }

    // public function bulkStoreWithVariants(array $data)
    // {
    //     $createdProducts = [];
    //     foreach ($data['products'] as $productData) {
    //         $product = $this->createProductWithVariants($productData);
    //         $createdProducts[] = $product;
    //     }
    //     return $createdProducts;
    // }

    // private function createProductWithVariants(array $productData): Item
    // {
    //     // Create the base product
    //     $product = $this->model->create([
    //         'name' => $productData['name'],
    //         'sku' => $productData['sku'],
    //         'description' => $productData['description'] ?? null,
    //         'category_id' => $productData['category_id'],
    //     ]);

    //     // Create variants
    //     foreach ($productData['variants'] as $variantData) {
    //         $this->createOneVariant($product, $variantData);
    //     }
    //     return $product->load('variants.attributeValues.attribute');
    // }

    // private function createOneVariant(Item $product, array $variantData): ItemVariant
    // {
    //     // Generate SKU for variant
    //     $variantSku = $this->generateVariantSku($product->sku, $variantData['attributes']);
    //     // Create variant
    //     $variant = ItemVariant::create([
    //         'item_id' => $product->id,
    //         'sku' => $variantSku,
    //         'price' => $variantData['price'],
    //         'stock' => $variantData['stock'] ?? 0
    //     ]);

    //     // Attach attribute values
    //     foreach ($variantData['attributes'] as $attribute_id => $value_id) {
    //         $attribute = ItemAttribute::where('id', $attribute_id)->firstOrFail();
    //         $attributeValue = ItemAttributeValue::where('item_attribute_id', $attribute->id)
    //             ->where('id', $value_id)
    //             ->firstOrFail();

    //         $variant->attributeValues()->attach($attributeValue->id, [
    //             'item_attribute_id' => $attribute->id
    //         ]);
    //     }
    //     return $variant;
    // }

    // private function generateVariantSku(string $baseSku, array $attributes): string
    // {
    //     $suffix = collect($attributes)
    //         ->map(fn($value) => strtoupper(substr($value, 0, 2)))
    //         ->join('-');

    //     $sku = $baseSku . '-' . $suffix;

    //     // Ensure uniqueness
    //     $counter = 1;
    //     $originalSku = $sku;
    //     while (ItemVariant::where('sku', $sku)->exists()) {
    //         $sku = $originalSku . '-' . $counter;
    //         $counter++;
    //     }

    //     return $sku;
    // }


    public function createProduct(array $commonData, ProductDTO $productData): Item
    {
        $product = $this->productModel->create($productData->toArray());
        if ($productData->variants) {
            $this->productVariantService->createVariantsBulk($product, $productData->variants);
            $product->load('variants.attributeValues.attribute');
        }

        return $product->item()->create($commonData);
    }

    public function createService(array $commonData, ServiceDTO $serviceData): Item
    {
        $service = $this->serviceModel->create($serviceData->toArray());
        return $service->item()->create($commonData);
    }

    /**
     * Update product-specific data and variants
     */
    private function updateProduct(Item $item, $request): void
    {
        $product = $item->itemable;
        $productData = ProductDTO::fromRequest($request);

        // Update product data
        $product->update([
            'stock' => $productData->stock,
            'sku' => $productData->sku,
        ]);

        // Handle variants if provided
        if ($productData->variants) {
            $this->updateProductVariants($product, $productData->variants);
        }
    }

    /**
     * Update service-specific data
     */
    private function updateService(Item $item, $request): void
    {
        $service = $item->itemable;
        $serviceData = ServiceDTO::fromRequest($request);

        $service->update($serviceData->toArray());
    }

    /**
     * Update product variants
     */
    private function updateProductVariants(Product $product, array $variants): void
    {
        // Delete existing variants and their relationships
        $this->deleteProductVariants($product);

        // Refresh the product to ensure clean state
        $product->refresh();

        // Create new variants
        if (!empty($variants)) {
            $this->productVariantService->createVariantsBulk($product, $variants);
        }
    }

    /**
     * Check if the item type needs to be changed
     */
    private function needsTypeChange(Item $item, string $requestedType): bool
    {
        $currentType = $item->itemable_type === 'product' ? 'product' : 'service';
        return $currentType !== $requestedType;
    }

    /**
     * Change the item type from product to service or vice versa
     */
    private function changeItemType(Item $item, string $newType, $request): void
    {
        $commonData = ItemDTO::fromRequest($request)->toArray();

        // Delete the old itemable
        $oldItemable = $item->itemable;
        if ($oldItemable) {
            // If it's a product, delete variants first
            if ($item->isProduct) {
                $this->deleteProductVariants($oldItemable);
            }
            $oldItemable->delete();
        }

        // Create new itemable based on the new type
        if ($newType === 'product') {
            $productData = ProductDTO::fromRequest($request);
            $product = $this->productModel->create($productData->toArray());

            // Handle variants if provided
            if ($productData->variants) {
                $this->productVariantService->createVariantsBulk($product, $productData->variants);
            }

            // Update the item with new polymorphic relationship
            $item->update(array_merge($commonData, [
                'itemable_type' => 'product',
                'itemable_id' => $product->id,
            ]));
        } else {
            $serviceData = ServiceDTO::fromRequest($request);
            $service = $this->serviceModel->create($serviceData->toArray());

            // Update the item with new polymorphic relationship
            $item->update(array_merge($commonData, [
                'itemable_type' => 'service',
                'itemable_id' => $service->id,
            ]));
        }
    }
}
