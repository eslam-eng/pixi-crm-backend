<?php

namespace App\Http\Controllers\Api\Report;

use App\DTO\Report\OpportunityManagementReportDTO;
use App\Http\Controllers\Controller;
use App\Services\Report\ReportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OpportunityManagementController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    /**
     * Get pipeline report
     */
    public function pipelineReport(Request $request): JsonResponse
    {
        try {
            $filters = OpportunityManagementReportDTO::fromRequest($request);
            $result = $this->reportService->executeOpportunityPipelineReport($filters);

            return ApiResponse([
                'data' => $result['data'],
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Pipeline report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get activity report
     */
    public function activityReport(Request $request): JsonResponse
    {
        try {
            $filters = OpportunityManagementReportDTO::fromRequest($request);
            $result = $this->reportService->executeOpportunityPipelineReport($filters);

            // Group by activity type and date
            $activityData = $result['data']->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->created_at)->format('Y-m-d');
            })->map(function ($dayData, $date) {
                return [
                    'date' => $date,
                    'new_opportunities' => $dayData->count(),
                    'total_value' => $dayData->sum('deal_value'),
                    'average_value' => $dayData->avg('deal_value'),
                ];
            })->values();

            return ApiResponse([
                'activity_data' => $activityData,
                'summary' => $result['summary'],
            ], 'Activity report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get stage progression analysis
     */
    public function stageProgressionAnalysis(Request $request): JsonResponse
    {
        try {
            $filters = OpportunityManagementReportDTO::fromRequest($request);
            $result = $this->reportService->executeOpportunityPipelineReport($filters);

            $progressionData = $result['data']->groupBy('stage_name')->map(function ($stageData, $stageName) {
                // Calculate days in stage based on created_at and current date
                $avgDaysInStage = $stageData->avg(function ($item) {
                    return \Carbon\Carbon::parse($item->created_at)->diffInDays(now());
                });
                $conversionRate = $stageData->where('status', 'won')->count() / max($stageData->count(), 1) * 100;

                return [
                    'stage_name' => $stageName,
                    'opportunities_count' => $stageData->count(),
                    'total_value' => $stageData->sum('deal_value'),
                    'average_value' => $stageData->avg('deal_value'),
                    'average_days_in_stage' => round($avgDaysInStage, 2),
                    'conversion_rate' => round($conversionRate, 2),
                ];
            })->values();

            return ApiResponse([
                'progression_data' => $progressionData,
                'summary' => $result['summary'],
            ], 'Stage progression analysis retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
