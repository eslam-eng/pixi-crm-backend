<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Item\ItemStoreRequest;
use App\Http\Requests\Item\ItemUpdateRequest;
use App\Http\Resources\ItemResource;
use App\Services\Tenant\ItemService;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ItemController extends Controller
{
    public function __construct(public ItemService $itemService)
    {
        $this->middleware('permission:view-items')->only(['index', 'show']);
        $this->middleware('permission:create-items')->only(['store']);
        $this->middleware('permission:edit-items')->only(['update']);
        $this->middleware('permission:delete-items')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $filters = array_filter(request()->query(), function ($value) {
            return $value !== null && $value !== '';
        });

        if ($request->has('ddl')) {
            $items = $this->itemService->index(filters: $filters, withRelations: []);
            $data = ItemResource::collection($items);
        } else {
            $items = $this->itemService->index(filters: $filters, withRelations: [], perPage: $filters['per_page'] ?? 10);
            $data = ItemResource::collection($items)->response()->getData(true);
        }

        return ApiResponse(message: 'Items retrieved successfully', data: $data, code: Response::HTTP_OK);
    }

    public function store(ItemStoreRequest $request)
    {
        try {
            DB::beginTransaction();
            $item = $this->itemService->store($request);
            DB::commit();
            return ApiResponse(message: 'Item created successfully', data: new ItemResource($item->load('itemable')), code: Response::HTTP_CREATED);
        } catch (Exception $e) {
            dd($e);
            DB::rollBack();
            return ApiResponse(message: 'Failed to create item', code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id)
    {
        try {
            DB::beginTransaction();
            $response = $this->itemService->show($id);
            DB::commit();
            return ApiResponse(message: 'Item show successfully', data: new ItemResource($response), code: Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(ItemUpdateRequest $request, int $id)
    {
        try {
            DB::beginTransaction();
            $response = $this->itemService->update($id, $request);
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
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
