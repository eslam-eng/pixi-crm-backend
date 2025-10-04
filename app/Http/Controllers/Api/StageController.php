<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PipelineCollection;
use Illuminate\Http\Request;
use App\DTO\Pipeline\PipelineDTO;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pipeline\PipelineStoreRequest;
use App\Http\Requests\Pipeline\PipelineUpdateRequest;
use App\Http\Requests\Stage\StageStoreRequest;
use App\Http\Requests\Stage\StageUpdateRequest;
use App\Http\Resources\PipelineResource;
use App\Http\Resources\StageCollection;
use App\Http\Resources\StageResource;
use App\Models\Stage;
use App\Services\StageService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class StageController extends Controller
{
    public function __construct(public StageService $stageService)
    {
        $this->middleware('permission:manage-settings')->except(['index']);
    }

    public function index(Request $request, $pipelineId): JsonResponse
    {
        try {
            $perPage = $request->query('per_page');
            $filters = array_filter($request->get('filters', []), function ($value) {
                return ($value !== null && $value !== false && $value !== '');
            });
            $withRelations = ['pipeline'];
            $pipelines = $this->stageService->index($filters, $withRelations, $perPage, $pipelineId);
            return ApiResponse(stageResource::collection($pipelines), 'Stages retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function store(StageStoreRequest $request, $pipelineId): JsonResponse
    {
        try {
            $stageDTO = $request->toStageDTO();
            $stage = $this->stageService->store($stageDTO, $pipelineId);
            return ApiResponse(new StageResource($stage), 'Stage created successfully');
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'Pipeline not found', code: 404);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function show(int $id)
    {
        try {
            $pipline = $this->stageService->show($id);
            return ApiResponse(new StageResource($pipline), 'Stage retrieved successfully');
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'Stage not found', code: 404);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function update(StageUpdateRequest $request, $stageId)
    {
        try {
            DB::beginTransaction();

            $stageDTO = $request->toStageDTO();
            $stage = $this->stageService->update($stageDTO, $stageId);
            DB::commit();
            return ApiResponse(new StageResource($stage), 'Stage updated successfully');
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'Stage not found', code: 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->stageService->delete($id);
            return ApiResponse(message: 'Stage deleted successfully', code: 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'Stage not found', code: 404);
        } catch (\Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
