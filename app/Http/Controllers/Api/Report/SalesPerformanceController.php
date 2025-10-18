<?php

namespace App\Http\Controllers\Api\Report;

use App\DTO\Report\SalesPerformanceReportDTO;
use App\DTO\Report\LeadManagementReportDTO;
use App\DTO\Report\ReportFilterDTO;
use App\DTO\Report\TeamPerformanceReportDTO;
use App\Http\Controllers\Controller;
use App\Services\Report\ReportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SalesPerformanceController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {
        // $this->middleware('permission:view-sales-reports');
    }

    /**
     * Get deals performance report
     */
    public function dealsPerformance(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            return ApiResponse([
                'data' => $result['data'],
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Deals performance report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get revenue analysis report
     */
    public function revenueAnalysis(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeRevenueAnalysisReport($filters);

            return ApiResponse([
                'data' => $result['data'],
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Revenue analysis report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get pipeline funnel data
     */
    public function pipelineFunnel(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeOpportunityPipelineReport($filters);

            // Transform data for funnel visualization
            $funnelData = $result['data']->groupBy('stage_name')->map(function ($stageData, $stageName) {
                return [
                    'stage' => $stageName,
                    'count' => $stageData->count(),
                    'value' => $stageData->sum('deal_value'),
                    'probability' => $stageData->avg('win_probability'),
                ];
            })->values();

            return ApiResponse([
                'funnel_data' => $funnelData,
                'summary' => $result['summary'],
            ], 'Pipeline funnel data retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get win/loss analysis
     */
    public function winLossAnalysis(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $winLossData = $result['data']->groupBy('status')->map(function ($statusData, $status) use ($result) {
                $totalCount = $result['data']->count();
                return [
                    'status' => $status,
                    'count' => $statusData->count(),
                    'value' => $statusData->sum('deal_value'),
                    'percentage' => $totalCount > 0 ? ($statusData->count() / $totalCount) * 100 : 0,
                ];
            })->values();

            return ApiResponse([
                'win_loss_data' => $winLossData,
                'summary' => $result['summary'],
            ], 'Win/loss analysis retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get sales rep performance
     */
    public function salesRepPerformance(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $repPerformance = $result['data']->groupBy(function ($item) {
                return $item->user_first_name . ' ' . $item->user_last_name;
            })->map(function ($repData, $repName) {
                return [
                    'sales_rep' => $repName,
                    'total_deals' => $repData->count(),
                    'total_value' => $repData->sum('deal_value'),
                    'average_deal_size' => $repData->avg('deal_value'),
                    'win_rate' => $repData->where('status', 'won')->count() / max($repData->count(), 1) * 100,
                ];
            })->values();

            return ApiResponse([
                'rep_performance' => $repPerformance,
                'summary' => $result['summary'],
            ], 'Sales rep performance retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
