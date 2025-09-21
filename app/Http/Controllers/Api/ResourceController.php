<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\GeneralException;
use App\Http\Resources\SourceCollection;
use Exception;
use Illuminate\Http\Request;
use App\DTO\Resource\ResourceDTO;
use App\Services\ResourceService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Exceptions\NotFoundException;
use App\Http\Resources\SourceResource;
use App\Http\Requests\Resource\ResourceStoreRequest;
use App\Http\Requests\Resource\ResourceUpdateRequest;
use Symfony\Component\HttpFoundation\Response;

class ResourceController extends Controller
{
    public function __construct(public ResourceService $resourceService) {}

    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $perPage = $request->query('per_page');
            $filters = array_filter($request->get('filters', []), function ($value) {
                return ($value !== null && $value !== false && $value !== '');
            });
            $withRelations = [];
            $resources = $this->resourceService->getResources($filters, $withRelations, $perPage);
            return ApiResponse(new SourceCollection($resources), 'Sources retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function store(ResourceStoreRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();
            $resourceDTO = ResourceDTO::fromRequest($request);
            $resource = $this->resourceService->store($resourceDTO);
            DB::commit();
            return ApiResponse(new SourceResource($resource), 'Source created successfully', Response::HTTP_CREATED);
        } catch (GeneralException $e) {
            return ApiResponse(message: $e->getMessage(), code: $e->getCode());
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function show($id)
    {
        try {
            $resource = $this->resourceService->findById($id);
            return ApiResponse(new SourceResource($resource), 'Source retrieved successfully');
        } catch (NotFoundException $e) {
            return ApiResponse(message: $e->getMessage(), code: 404);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function update(ResourceUpdateRequest $request, $id)
    {
        try {
            $resourceDTO = $request->toResourceDTO();
            $this->resourceService->update($resourceDTO, $id);
            return ApiResponse(message: 'Source updated successfully');
        } catch (NotFoundException $e) {
            return ApiResponse(message: $e->getMessage(), code: 404);
        } catch (\Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->resourceService->delete($id);
            return ApiResponse(message: 'Source deleted successfully');
        } catch (NotFoundException $e) {
            return ApiResponse(message: $e->getMessage(), code: 404);
        } catch (\Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
