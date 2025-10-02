<?php

namespace App\Http\Controllers\Api;

use App\DTO\ItemCategory\ItemCategoryDTO;
use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\ItemCategory\StoreItemCategoryRequest;
use App\Http\Requests\Tenant\ItemCategory\UpdateItemCategoryRequest;
use App\Http\Resources\Tenant\ItemCategory\ItemCategoryDDLResource;
use App\Http\Resources\Tenant\ItemCategory\ItemCategoryResource;
use App\Services\Tenant\ItemCategoryService;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ItemCategoryController extends Controller
{
    public function __construct(public ItemCategoryService $itemCategoryService)
    {
        $this->middleware('permission:manage-settings');
    }

    public function index(Request $request)
    {

        $filters = array_filter(request()->query(), function ($value) {
            return $value !== null && $value !== '';
        });

        if ($request->has('ddl')) {
            $itemCategories = $this->itemCategoryService->index(filters: $filters, withRelations: ['children']);
            $data = ItemCategoryDDLResource::collection($itemCategories);
        } else {
            $itemCategories = $this->itemCategoryService->index(filters: $filters, withRelations: ['children'],  perPage: $filters['per_page'] ?? 10);
            $data = ItemCategoryResource::collection($itemCategories)->response()->getData(true);
        }

        return apiResponse(message: 'Item categories retrieved successfully', data: $data);
    }

    public function store(StoreItemCategoryRequest $request)
    {
        try {
            DB::beginTransaction();
            $itemCategoryDTO = ItemCategoryDTO::fromRequest($request);
            $this->itemCategoryService->store($itemCategoryDTO);
            DB::commit();
            return apiResponse(message: 'Item category created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return apiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateItemCategoryRequest $request, int $id)
    {
        try {
            DB::beginTransaction();
            $itemCategoryDTO = ItemCategoryDTO::fromRequest($request);
            $data = $this->itemCategoryService->update($id, $itemCategoryDTO);
            DB::commit();
            return apiResponse(message: 'Item category updated successfully', data: new ItemCategoryResource($data));
        } catch (Exception $e) {
            DB::rollBack();
            return apiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $this->itemCategoryService->destroy($id);
            DB::commit();
            return apiResponse(message: trans('app.data deleted successfully'));
        } catch (GeneralException $e) {
            DB::rollBack();
            return apiResponse(message: $e->getMessage(), code: 400);
        } catch (Exception $e) {
            DB::rollBack();
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
