<?php

namespace App\Services\Tenant;

use App\Exceptions\GeneralException;
use App\Models\Tenant\Item;
use Illuminate\Database\Eloquent\Builder;
use App\DTO\Item\ItemDTO;
use App\Enums\ItemType;
use App\Models\Filters\ItemFilter;
use App\Models\Tenant\ItemAttribute;
use App\Models\Tenant\ItemAttributeValue;
use App\Models\Tenant\ItemVariant;
use App\Notifications\Tenant\CreateNewItemNotification;
use App\Services\BaseService;
use App\Services\Tenant\Users\UserService;
use Illuminate\Support\Facades\DB;

class ItemService extends BaseService
{
    public function __construct(
        public Item $model,
        public ItemAttribute $itemAttribute,
        public UserService $userService,
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
        $defaultRelations = ['category'];
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

    public function store(ItemDTO $itemDTO): ItemVariant | array | bool
    {
        if ($itemDTO->type == ItemType::SERVICE->value) {
            $item = $this->model->create($itemDTO->toServiceArray());
        } elseif ($itemDTO->type == ItemType::PRODUCT->value) {
            $item = $this->model->create($itemDTO->toProductArray());
            $variant = $item->variants()->create($itemDTO->toArrayVariant());
        }

        $admins = $this->userService->getModel()->role('admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new CreateNewItemNotification($item));
        }
        return $variant->load('item.category');
    }

    public function update(int $id, ItemDTO $itemDTO): Item
    {
        try {
            DB::beginTransaction();
            $item = $this->findById($id);
            $item->update($itemDTO->toArray());
            DB::commit();
            return $item->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new GeneralException('Failed to update priority: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): bool
    {
        $item = $this->findById($id);
        if ($item->opportunities()->exists()) {
            throw new GeneralException(__('app.cannot_delete_item_used_by_opportunities'));
        }
        $result = $item->delete();
        return $result;
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

    public function bulkStoreWithVariants(array $data)
    {
        $createdProducts = [];
        foreach ($data['products'] as $productData) {
            $product = $this->createProductWithVariants($productData);
            $createdProducts[] = $product;
        }
        return $createdProducts;
    }

    private function createProductWithVariants(array $productData): Item
    {
        // Create the base product
        $product = $this->model->create([
            'name' => $productData['name'],
            'sku' => $productData['sku'],
            'description' => $productData['description'] ?? null,
            'category_id' => $productData['category_id'],
        ]);

        // Create variants
        foreach ($productData['variants'] as $variantData) {
            $this->createOneVariant($product, $variantData);
        }
        return $product->load('variants.attributeValues.attribute');
    }

    private function createOneVariant(Item $product, array $variantData): ItemVariant
    {
        // Generate SKU for variant
        $variantSku = $this->generateVariantSku($product->sku, $variantData['attributes']);
        // Create variant
        $variant = ItemVariant::create([
            'item_id' => $product->id,
            'sku' => $variantSku,
            'price' => $variantData['price'],
            'stock' => $variantData['stock'] ?? 0
        ]);

        // Attach attribute values
        foreach ($variantData['attributes'] as $attribute_id => $value_id) {
            $attribute = ItemAttribute::where('id', $attribute_id)->firstOrFail();
            $attributeValue = ItemAttributeValue::where('item_attribute_id', $attribute->id)
                ->where('id', $value_id)
                ->firstOrFail();

            $variant->attributeValues()->attach($attributeValue->id, [
                'item_attribute_id' => $attribute->id
            ]);
        }
        return $variant;
    }

    private function generateVariantSku(string $baseSku, array $attributes): string
    {
        $suffix = collect($attributes)
            ->map(fn($value) => strtoupper(substr($value, 0, 2)))
            ->join('-');

        $sku = $baseSku . '-' . $suffix;

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
