<?php

namespace App\Http\Controllers\Api;

use App\DTO\Item\ItemDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Item\ItemBulkStoreWithVariantsRequest;
use App\Http\Requests\Item\ItemStoreRequest;
use App\Http\Requests\Item\ItemUpdateRequest;
use App\Http\Resources\ItemResource;
use App\Http\Resources\ItemVariantResource;
use App\Services\Tenant\ItemService;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ItemController extends Controller
{
    public function __construct(public ItemService $itemService) {}

    public function index(Request $request)
    {
        $filters = array_filter(request()->query(), function ($value) {
            return $value !== null && $value !== '';
        });

        if ($request->has('ddl')) {
            $items = $this->itemService->index(filters: $filters, withRelations: ['variants.attributeValues.attribute']);
            $data = ItemResource::collection($items);
        } else {
            $items = $this->itemService->index(filters: $filters, withRelations: ['variants.attributeValues.attribute'], perPage: $filters['per_page'] ?? 10);
            $data = ItemResource::collection($items)->response()->getData(true);
        }

        return ApiResponse(message: 'Items retrieved successfully', data: $data, code: Response::HTTP_OK);
    }

    public function store(ItemStoreRequest $request)
    {
        try {
            DB::beginTransaction();
            $object = ItemDTO::fromArray($request->validated());
            $itemDTO = ItemDTO::fromRequest($object);
            $response = $this->itemService->store($itemDTO);
            DB::commit();
            return ApiResponse(message: 'Item created successfully', data: new ItemVariantResource($response), code: Response::HTTP_CREATED);
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(ItemUpdateRequest $request, int $id)
    {
        try {
            DB::beginTransaction();
            $itemDTO = ItemDTO::fromRequest($request);
            $response = $this->itemService->update($id, $itemDTO);
            DB::commit();
            return ApiResponse(message: 'Item updated successfully', data: new ItemResource($response), code: Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id)
    {
        try {
            DB::beginTransaction();
            $this->itemService->destroy($id);
            DB::commit();
            return ApiResponse(message: 'Item deleted successfully', code: Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function bulkStoreWithVariants(ItemBulkStoreWithVariantsRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->itemService->bulkStoreWithVariants($request->validated());
            DB::commit();
            return ApiResponse(message: 'Items created successfully', code: Response::HTTP_CREATED);
        } catch (Exception $e) {
            DB::rollback();
            return ApiResponse(message: 'Failed to create products: ' . $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
