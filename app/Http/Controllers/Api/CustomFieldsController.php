<?php

namespace App\Http\Controllers\Api;

use App\DTO\CustomField\CustomFieldDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomFields\StoreCustomFieldRequest;
use App\Http\Requests\CustomFields\UpdateCustomFieldRequest;
use App\Http\Resources\CustomFieldResource;
use App\Models\Tenant\CustomField;
use App\Services\Tenant\CustomFieldsService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomFieldsController extends Controller
{
    public function __construct(public CustomFieldsService $customFieldsService)
    {
        $this->middleware('permission:manage-settings');
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->query('per_page');
            $filters = array_filter($request->get('filters', []), function ($value) {
                return ($value !== null && $value !== false && $value !== '');
            });

            $withRelations = ['formSection'];
            $customFields = $this->customFieldsService->index($filters, $withRelations, $perPage);

            if ($perPage) {
                $data = CustomFieldResource::collection($customFields)->response()->getData(true);
            } else {
                $data = CustomFieldResource::collection($customFields);
            }

            return ApiResponse($data, 'Custom fields retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function store(StoreCustomFieldRequest $request): JsonResponse
    {
        try {
            $customFieldDTO = CustomFieldDTO::fromRequest($request);
            $customField = $this->customFieldsService->store($customFieldDTO);
            return ApiResponse(new CustomFieldResource($customField), 'Custom field created successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function show(int $id)
    {
        try {
            $customField = $this->customFieldsService->show($id, ['formSection']);
            return ApiResponse(new CustomFieldResource($customField), 'Custom field retrieved successfully');
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'Custom field not found', code: 404);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function update(UpdateCustomFieldRequest $request, int $id)
    {
        try {
            DB::beginTransaction();

            $customField = CustomField::findOrFail($id);
            $customFieldDTO = CustomFieldDTO::fromRequest($request);
            $updatedCustomField = $this->customFieldsService->update($customField, $customFieldDTO);

            DB::commit();
            return ApiResponse(new CustomFieldResource($updatedCustomField), 'Custom field updated successfully');
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'Custom field not found', code: 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->customFieldsService->delete($id);
            return ApiResponse(message: 'Custom field deleted successfully', code: 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'Custom field not found', code: 404);
        } catch (\Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
