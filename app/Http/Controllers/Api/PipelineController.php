<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PipelineCollection;
use Illuminate\Http\Request;
use App\DTO\Pipeline\PipelineDTO;
use App\Services\PipelineService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pipeline\PipelineStoreRequest;
use App\Http\Requests\Pipeline\PipelineUpdateRequest;
use App\Http\Resources\PipelineResource;
use App\Models\Tenant\Pipeline;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class PipelineController extends Controller
{
    public function __construct(public PipelineService $pipelineService)
    {
        $this->middleware('permission:manage-settings')->except(['index', 'show']);
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->query('per_page');
            $filters = array_filter($request->get('filters', []), function ($value) {
                return ($value !== null && $value !== false && $value !== '');
            });
            $withRelations = ['stages'];
            $pipelines = $this->pipelineService->index($filters, $withRelations, $perPage);
            return ApiResponse(new PipelineCollection($pipelines), 'Pipelines retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function store(PipelineStoreRequest $request): JsonResponse
    {
        try {
            // Create pipelineDTO from the request
            $pipelineDTO = PipelineDTO::fromRequest($request);
            // Store the pipline using the service
            $pipeline = $this->pipelineService->store($pipelineDTO);
            return ApiResponse(new PipelineResource($pipeline), 'Pipeline created successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function show(int $id)
    {
        try {
            $pipline = $this->pipelineService->show($id);
            return ApiResponse(new PipelineResource($pipline), 'Pipeline retrieved successfully');
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'Pipeline not found', code: 404);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function update(PipelineUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $pipline = Pipeline::findOrFail($id);
            $piplineDTO = PipelineDTO::fromRequest($request);
            $pipline = $this->pipelineService->update($pipline, $piplineDTO);

            DB::commit();
            return ApiResponse(new PipelineResource($pipline), 'Pipeline updated successfully');
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'Pipeline not found', code: 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->pipelineService->delete($id);
            return ApiResponse(message: 'Pipeline deleted successfully', code: 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'Pipeline not found', code: 404);
        } catch (\Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
