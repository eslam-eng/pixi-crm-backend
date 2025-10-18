<?php

namespace App\Http\Controllers\Api\Report;

use App\DTO\Report\ForecastingReportDTO;
use App\Http\Controllers\Controller;
use App\Services\Report\ReportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ForecastingController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    /**
     * Get sales forecast report
     */
    public function salesForecast(Request $request): JsonResponse
    {
        try {
            $filters = ForecastingReportDTO::fromRequest($request);
            $result = $this->reportService->executeForecastingReport($filters);

            return ApiResponse([
                'data' => $result['data'],
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Sales forecast report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get pipeline projections
     */
    public function pipelineProjections(Request $request): JsonResponse
    {
        try {
            $filters = ForecastingReportDTO::fromRequest($request);
            $result = $this->reportService->executeForecastingReport($filters);

            $projections = $result['data']->groupBy('stage_name')->map(function ($stageData, $stageName) {
                return [
                    'stage' => $stageName,
                    'opportunities_count' => $stageData->count(),
                    'weighted_value' => $stageData->sum('weighted_value'),
                    'potential_value' => $stageData->sum('deal_value'),
                    'average_probability' => $stageData->avg('win_probability'),
                ];
            })->values();

            return ApiResponse([
                'projections' => $projections,
                'summary' => $result['summary'],
            ], 'Pipeline projections generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get revenue predictions
     */
    public function revenuePredictions(Request $request): JsonResponse
    {
        try {
            $filters = ForecastingReportDTO::fromRequest($request);
            $result = $this->reportService->executeForecastingReport($filters);

            $predictions = $result['data']->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->expected_close_date)->format('Y-m');
            })->map(function ($monthData, $month) {
                return [
                    'month' => $month,
                    'forecasted_revenue' => $monthData->sum('weighted_value'),
                    'potential_revenue' => $monthData->sum('deal_value'),
                    'opportunities_count' => $monthData->count(),
                ];
            })->values();

            return ApiResponse([
                'predictions' => $predictions,
                'summary' => $result['summary'],
            ], 'Revenue predictions generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
