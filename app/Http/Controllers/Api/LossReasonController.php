<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\LossReason\LossReasonStoreRequest;
use App\Http\Requests\LossReason\LossReasonUpdateRequest;
use App\Http\Resources\Tenant\LossReasonResource;
use App\Services\LossReasonService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class LossReasonController extends Controller
{
    public function __construct(public LossReasonService $lossReasonService) {}

    public function index(Request $request, $pipelineId): JsonResponse
    {
        try {
            $perPage = $request->query('per_page');
            $filters = array_filter($request->get('filters', []), function ($value) {
                return ($value !== null && $value !== false && $value !== '');
            });
            $withRelations = ['pipeline'];
            $pipelines = $this->lossReasonService->index($filters, $withRelations, $perPage, $pipelineId);
            return ApiResponse(LossReasonResource::collection($pipelines), 'Loss Reasons retrieved successfully');
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'Pipeline not found', code: 404);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function store(LossReasonStoreRequest $request, $pipelineId): JsonResponse
    {
        try {
            $lossReasonDTO = $request->toLossReasonDTO();
            $lossReason = $this->lossReasonService->store($lossReasonDTO, $pipelineId);
            return ApiResponse(new LossReasonResource($lossReason), 'Loss Reason created successfully');
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'Pipeline not found', code: 404);
        } catch (Exception $e)   {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function show(int $id)
    {
        try {
            $pipline = $this->lossReasonService->show($id);
            return ApiResponse(new LossReasonResource($pipline), 'Loss Reason retrieved successfully');
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'Loss Reason not found', code: 404);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function update(LossReasonUpdateRequest $request, $lossReasonId)
    {
        try {
            DB::beginTransaction();

            $lossReasonDTO = $request->toLossReasonDTO();
            $lossReason = $this->lossReasonService->update($lossReasonDTO, $lossReasonId);
            DB::commit();
            return ApiResponse(new LossReasonResource($lossReason), 'Loss Reason updated successfully');
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'Loss Reason not found', code: 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->lossReasonService->delete($id);
            return ApiResponse(message: 'Loss Reason deleted successfully', code: 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'Loss Reason not found', code: 404);
        } catch (\Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
