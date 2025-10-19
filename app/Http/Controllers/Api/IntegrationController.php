<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Tenant\Integration\IntegrationResource;
use App\Services\Tenant\Integration\IntegrationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Http\Response;

class IntegrationController extends Controller
{
    public function __construct(
        private IntegrationService $integrationService
    ) {}

    /**
     * Display a listing of integrations
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = array_filter($request->all(), function ($value) {
                return ($value !== null && $value !== false && $value !== '');
            });
            
            $perPage = $request->query('per_page');
            $integrations = $this->integrationService->index($filters, [], $perPage);
            
            $data = IntegrationResource::collection($integrations);
            
            return ApiResponse($data, 'Integrations retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Toggle the enabled status of an integration
     */
    public function toggleStatus(int $id): JsonResponse
    {
        try {
            $integration = $this->integrationService->toggleStatus($id);

            return ApiResponse(
                new IntegrationResource($integration),
                'Integration status updated successfully'
            );
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get integration statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $statistics = $this->integrationService->getStatistics();

            return ApiResponse($statistics, 'Integration statistics retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
