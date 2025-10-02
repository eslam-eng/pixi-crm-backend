<?php
// app/Http/Controllers/ProductVariantController.php

namespace App\Http\Controllers\Api;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;

use App\Http\Requests\Item\UpdateProductVariantRequest;
use App\Http\Resources\ItemProductVariantResource;
use App\Http\Resources\ItemVariantResource;
use App\Models\Tenant\Item;
use App\Models\Tenant\ItemAttribute;
use App\Models\Tenant\ItemAttributeValue;
use App\Models\Tenant\ItemVariant;
use App\Services\Tenant\ItemVariantService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ItemVariantController extends Controller
{
    public function __construct(public ItemVariantService $itemVariantService)
    {
        $this->middleware('permission:manage-settings')->except(['index', 'show']);
    }
    /**
     * Display a listing of the variants for a product.
     */
    public function index(int $itemId)
    {

        try {
            DB::beginTransaction();
            $variants = $this->itemVariantService->index(itemId: $itemId, perPage: $filters['per_page'] ?? 10);
            $data = ItemProductVariantResource::collection($variants)->response()->getData(true);
            DB::commit();
            return apiResponse($data, 'Product variants retrieved successfully', Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return apiResponse(message: 'Product variants not found', code: Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            DB::rollBack();
            return apiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // public function getAll(Request $request)
    // {
    //     $filters = array_filter(request()->query(), function ($value) {
    //         return $value !== null && $value !== '';
    //     });

    //     if ($request->has('ddl')) {
    //         $variants = $this->itemVariantService->getAllVariant(filters: $filters, withRelations: ['item.category', 'attributeValues.attribute']);
    //         $data = ItemVariantResource::collection($variants);
    //     } else {
    //         $variants = $this->itemVariantService->getAllVariant(filters: $filters, withRelations: ['item.category', 'attributeValues.attribute'], perPage: $filters['per_page'] ?? 10);
    //         $data = ItemVariantResource::collection($variants)->response()->getData(true);
    //     }
    //     return apiResponse($data, 'Item variants retrieved successfully', Response::HTTP_OK);
    // }

    /**
     * Display the specified variant.
     */
    public function show(Item $item, ItemVariant $variant)
    {
        // Ensure variant belongs to the product
        if ($variant->item_id !== $item->id) {
            return response()->json([
                'status' => false,
                'message' => 'Variant does not belong to this item'
            ], 404);
        }

        $variant->load(['attributeValues.attribute', 'item']);

        return response()->json([
            'data' => new ItemProductVariantResource($variant),
            'status' => true,
            'message' => 'Item variant retrieved successfully'
        ]);
    }

    /**
     * Update the specified variant.
     */
    public function update(UpdateProductVariantRequest $request, Item $item, ItemVariant $variant)
    {
        // Ensure variant belongs to the product
        if ($variant->item_id !== $item->id) {
            return response()->json([
                'status' => false,
                'message' => 'Variant does not belong to this item'
            ], 404);
        }

        try {
            DB::beginTransaction();

            $validated = $request->validated();
            $updated = false;

            // Update basic variant fields
            $basicFields = ['price', 'stock', 'is_active', 'sku'];
            foreach ($basicFields as $field) {
                if (array_key_exists($field, $validated)) {
                    $variant->{$field} = $validated[$field];
                    $updated = true;
                }
            }

            // Update attributes if provided
            if (array_key_exists('attributes', $validated) && !empty($validated['attributes'])) {
                $this->updateVariantAttributes($variant, $validated['attributes']);
                $updated = true;

                // Regenerate SKU if attributes changed and no custom SKU provided
                if (!array_key_exists('sku', $validated)) {
                    $newSku = $this->generateVariantSku($item->base_sku, $validated['attributes']);
                    $variant->sku = $newSku;
                }
            }

            if ($updated) {
                $variant->save();
            }

            DB::commit();

            $variant->load(['attributeValues.attribute', 'item']);

            return response()->json([
                'data' => new ItemProductVariantResource($variant),
                'status' => true,
                'message' => 'Product variant updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'status' => false,
                'message' => 'Failed to update variant: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified variant.
     */
    public function destroy(Item $item, ItemVariant $variant)
    {
        // Ensure variant belongs to the product
        if ($variant->item_id !== $item->id) {
            return response()->json([
                'status' => false,
                'message' => 'Variant does not belong to this item'
            ], 404);
        }

        // Check if this is the last variant (optional business rule)
        if ($item->variants()->count() <= 1) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot delete the last variant. A item must have at least one variant.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Store variant info for response
            $variantData = [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'attributes' => $variant->getAttributesArray()
            ];
            $variant->attributeValues()->detach();
            // Delete variant (this will cascade delete the attribute relationships)
            $variant->delete();

            DB::commit();

            return response()->json([
                'data' => $variantData,
                'status' => true,
                'message' => 'Product variant deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'status' => false,
                'message' => 'Failed to delete variant: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete variants.
     */
    public function bulkDestroy(Request $request, Item $item)
    {
        $request->validate([
            'variant_ids' => 'required|array|min:1',
            'variant_ids.*' => 'required|integer|exists:item_variants,id'
        ]);

        $variantIds = $request->input('variant_ids');

        // Get variants and ensure they belong to this product
        $variants = ItemVariant::whereIn('id', $variantIds)
            ->where('item_id', $item->id)
            ->get();

        if ($variants->count() !== count($variantIds)) {
            return response()->json([
                'status' => false,
                'message' => 'Some variants do not belong to this product'
            ], 422);
        }

        // Check if deleting would leave product without variants
        $remainingVariants = $item->variants()->count() - $variants->count();
        if ($remainingVariants < 1) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot delete all variants. A item must have at least one variant.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $deletedCount = ItemVariant::whereIn('id', $variantIds)->delete();

            DB::commit();

            return response()->json([
                'data' => [
                    'deleted_count' => $deletedCount,
                    'deleted_ids' => $variantIds
                ],
                'status' => true,
                'message' => "{$deletedCount} variants deleted successfully"
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'status' => false,
                'message' => 'Failed to delete variants: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update variant attributes.
     */
    private function updateVariantAttributes(ItemVariant $variant, array $attributes): void
    {
        // Detach all current attributes
        $variant->attributeValues()->detach();

        // Attach new attributes
        foreach ($attributes as $name => $value) {
            $attribute = ItemAttribute::where('name', $name)->firstOrFail();
            $attributeValue = ItemAttributeValue::where('attribute_id', $attribute->id)
                ->where('value', $value)
                ->firstOrFail();

            $variant->attributeValues()->attach($attributeValue->id, [
                'attribute_id' => $attribute->id
            ]);
        }
    }

    /**
     * Generate variant SKU.
     */
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

    /**
     * Toggle variant status (activate/deactivate).
     */
    public function toggleStatus(Item $item, ItemVariant $variant)
    {
        if ($variant->item_id !== $item->id) {
            return response()->json([
                'status' => false,
                'message' => 'Variant does not belong to this item'
            ], 404);
        }

        $variant->is_active = !$variant->is_active;
        $variant->save();

        $status = $variant->is_active ? 'activated' : 'deactivated';

        return response()->json([
            'data' => new ItemProductVariantResource($variant->load(['attributeValues.attribute'])),
            'status' => true,
            'message' => "Item variant {$status} successfully"
        ]);
    }

    public function destroyVariant($id)
    {
        try {
            DB::beginTransaction();
            $this->itemVariantService->destroyVariant($id);
            DB::commit();
            return ApiResponse(message: 'Item variant deleted successfully', code: Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollback();
            return ApiResponse(message: 'Failed to delete variant: ' . $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
