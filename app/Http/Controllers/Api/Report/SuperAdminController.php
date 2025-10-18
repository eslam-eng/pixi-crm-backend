<?php

namespace App\Http\Controllers\Api\Report;

use App\DTO\Report\SuperAdminReportDTO;
use App\Http\Controllers\Controller;
use App\Services\Report\ReportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    /**
     * Get client overview report
     */
    public function clientOverview(Request $request): JsonResponse
    {
        try {
            $filters = SuperAdminReportDTO::fromRequest($request);
            $result = $this->reportService->executeSuperAdminReport($filters);

            return ApiResponse([
                'data' => $result['data'],
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Client overview report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get subscription management report
     */
    public function subscriptionManagement(Request $request): JsonResponse
    {
        try {
            $filters = SuperAdminReportDTO::fromRequest($request);
            $result = $this->reportService->executeSuperAdminReport($filters);

            return ApiResponse([
                'data' => $result['data'],
                'summary' => $result['summary'],
            ], 'Subscription management report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get activation code usage report
     */
    public function activationCodeUsage(Request $request): JsonResponse
    {
        try {
            $filters = SuperAdminReportDTO::fromRequest($request);
            $result = $this->reportService->executeSuperAdminReport($filters);

            return ApiResponse([
                'data' => $result['data'],
                'summary' => $result['summary'],
            ], 'Activation code usage report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get billing revenue report
     */
    public function billingRevenue(Request $request): JsonResponse
    {
        try {
            $filters = SuperAdminReportDTO::fromRequest($request);
            $result = $this->reportService->executeSuperAdminReport($filters);

            return ApiResponse([
                'data' => $result['data'],
                'summary' => $result['summary'],
            ], 'Billing revenue report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get usage analytics report
     */
    public function usageAnalytics(Request $request): JsonResponse
    {
        try {
            $filters = SuperAdminReportDTO::fromRequest($request);
            $result = $this->reportService->executeSuperAdminReport($filters);

            return ApiResponse([
                'data' => $result['data'],
                'summary' => $result['summary'],
            ], 'Usage analytics report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get system audit report
     */
    public function systemAudit(Request $request): JsonResponse
    {
        try {
            $filters = SuperAdminReportDTO::fromRequest($request);
            $result = $this->reportService->executeSuperAdminReport($filters);

            return ApiResponse([
                'data' => $result['data'],
                'summary' => $result['summary'],
            ], 'System audit report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get user management report
     */
    public function userManagement(Request $request): JsonResponse
    {
        try {
            $filters = SuperAdminReportDTO::fromRequest($request);
            $result = $this->reportService->executeSuperAdminReport($filters);

            return ApiResponse([
                'data' => $result['data'],
                'summary' => $result['summary'],
            ], 'User management report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
