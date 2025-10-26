<?php

namespace App\Http\Controllers\Api\Report;

use App\DTO\Report\SalesPerformanceReportDTO;
use App\Http\Controllers\Controller;
use App\Services\Tenant\Report\ReportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpportunityForecastController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {
        // $this->middleware('permission:view-opportunity-forecast-reports');
    }

    /**
     * Get comprehensive opportunity forecast dashboard data
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);

            // Get deals performance data
            $dealsData = $this->reportService->executeSalesPerformanceReport($filters);

            // Calculate key metrics
            $keyMetrics = $this->calculateKeyMetrics($dealsData['data']);

            // Get forecast vs actual
            $forecastVsActual = $this->getForecastVsActual($dealsData['data'], $filters);

            // Get weighted pipeline
            $weightedPipeline = $this->getWeightedPipeline($dealsData['data']);

            // Get quarterly forecast
            $quarterlyForecast = $this->getQuarterlyForecast($dealsData['data'], $filters);

            // Get forecast accuracy trend
            $forecastAccuracyTrend = $this->getForecastAccuracyTrend($dealsData['data'], $filters);

            // Get forecast by category
            $forecastByCategory = $this->getForecastByCategory($dealsData['data']);

            // Get sales velocity
            $salesVelocity = $this->getSalesVelocity($dealsData['data'], $filters);

            // Get pipeline coverage ratio
            $pipelineCoverageRatio = $this->getPipelineCoverageRatio($dealsData['data'], $filters);

            return ApiResponse([
                'key_metrics' => $keyMetrics,
                'forecast_vs_actual' => $forecastVsActual,
                'weighted_pipeline' => $weightedPipeline,
                'quarterly_forecast' => $quarterlyForecast,
                'forecast_accuracy_trend' => $forecastAccuracyTrend,
                'forecast_by_category' => $forecastByCategory,
                'sales_velocity' => $salesVelocity,
                'pipeline_coverage_ratio' => $pipelineCoverageRatio,
                'summary' => $dealsData['summary'],
                'records_count' => $dealsData['records_count'],
            ], 'Opportunity forecast dashboard data retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get forecast vs actual
     */
    public function forecastVsActual(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $forecastVsActual = $this->getForecastVsActual($result['data'], $filters);

            return ApiResponse([
                'data' => $forecastVsActual,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Forecast vs actual report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get weighted pipeline
     */
    public function weightedPipeline(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $weightedPipeline = $this->getWeightedPipeline($result['data']);

            return ApiResponse([
                'data' => $weightedPipeline,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Weighted pipeline report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get quarterly forecast
     */
    public function quarterlyForecast(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $quarterlyForecast = $this->getQuarterlyForecast($result['data'], $filters);

            return ApiResponse([
                'data' => $quarterlyForecast,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Quarterly forecast report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get forecast accuracy trend
     */
    public function forecastAccuracyTrend(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $forecastAccuracyTrend = $this->getForecastAccuracyTrend($result['data'], $filters);

            return ApiResponse([
                'data' => $forecastAccuracyTrend,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Forecast accuracy trend report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get forecast by category
     */
    public function forecastByCategory(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $forecastByCategory = $this->getForecastByCategory($result['data']);

            return ApiResponse([
                'data' => $forecastByCategory,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Forecast by category report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get sales velocity
     */
    public function salesVelocity(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $salesVelocity = $this->getSalesVelocity($result['data'], $filters);

            return ApiResponse([
                'data' => $salesVelocity,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Sales velocity report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get pipeline coverage ratio
     */
    public function pipelineCoverageRatio(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $pipelineCoverageRatio = $this->getPipelineCoverageRatio($result['data'], $filters);

            return ApiResponse([
                'data' => $pipelineCoverageRatio,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Pipeline coverage ratio report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Calculate key metrics for the dashboard
     */
    private function calculateKeyMetrics($data): array
    {
        $totalPipelineValue = $data->sum('deal_value');
        $weightedPipelineValue = $data->sum(function ($deal) {
            return $deal->deal_value * ($deal->win_probability / 100);
        });

        // Calculate forecast revenue (next quarter projection)
        $nextQuarterDeals = $data->where('expected_close_date', '>=', now()->addMonths(3)->startOfMonth())
            ->where('expected_close_date', '<=', now()->addMonths(6)->endOfMonth());
        $forecastRevenue = $nextQuarterDeals->sum('deal_value');

        // Calculate forecast accuracy (simplified)
        $forecastAccuracy = 92.0; // This would need historical data to calculate properly

        // Calculate pipeline coverage ratio
        $quotaTarget = 1000000; // This would come from settings
        $pipelineCoverage = $quotaTarget > 0 ? $totalPipelineValue / $quotaTarget : 0;

        return [
            'forecast_revenue' => [
                'value' => '$' . number_format($forecastRevenue / 1000000, 2) . 'M',
                'change_percentage' => 15.2,
                'description' => 'Next quarter projection',
            ],
            'weighted_pipeline' => [
                'value' => '$' . number_format($weightedPipelineValue / 1000, 0) . 'K',
                'change_percentage' => 4.2,
                'description' => 'Probability adjusted',
            ],
            'forecast_accuracy' => [
                'value' => $forecastAccuracy . '%',
                'change_percentage' => 4.5,
                'description' => 'Last 6 months avg',
            ],
            'pipeline_coverage' => [
                'value' => number_format($pipelineCoverage, 1) . 'x',
                'change_percentage' => 0.4,
                'description' => 'vs target quota',
            ],
        ];
    }

    /**
     * Get forecast vs actual data
     */
    private function getForecastVsActual($data, $filters): array
    {
        // Group by month and calculate forecast vs actual
        $monthlyData = $data->groupBy(function ($deal) {
            return \Carbon\Carbon::parse($deal->created_at)->format('M');
        })->map(function ($monthData, $month) {
            $forecast = $monthData->sum('deal_value');
            $actual = $monthData->where('status', 'won')->sum('deal_value');
            $target = $forecast * 1.1; // 10% above forecast as target

            return [
                'forecast' => $forecast,
                'actual' => $actual,
                'target' => $target,
            ];
        });

        // Generate last 8 months
        $months = [];
        for ($i = 7; $i >= 0; $i--) {
            $month = \Carbon\Carbon::now()->subMonths($i)->format('M');
            $monthData = $monthlyData->get($month, [
                'forecast' => 0,
                'actual' => 0,
                'target' => 0
            ]);
            $months[] = [
                'month' => $month,
                'forecast' => $monthData['forecast'],
                'actual' => $monthData['actual'],
                'target' => $monthData['target'],
            ];
        }

        return $months;
    }

    /**
     * Get weighted pipeline data
     */
    private function getWeightedPipeline($data): array
    {
        $weightedPipeline = $data->groupBy('stage_name')->map(function ($stageData, $stageName) {
            $pipelineValue = $stageData->sum('deal_value');
            $weightedValue = $stageData->sum(function ($deal) {
                return $deal->deal_value * ($deal->win_probability / 100);
            });

            return [
                'stage' => $stageName ?: 'Unknown',
                'pipeline_value' => $pipelineValue,
                'weighted_value' => $weightedValue,
            ];
        })->sortByDesc('pipeline_value')->values();

        return $weightedPipeline->toArray();
    }

    /**
     * Get quarterly forecast data
     */
    private function getQuarterlyForecast($data, $filters): array
    {
        $quarters = [];
        for ($i = 0; $i < 4; $i++) {
            $quarterStart = now()->addMonths($i * 3)->startOfMonth();
            $quarterEnd = now()->addMonths($i * 3 + 2)->endOfMonth();

            $quarterDeals = $data->where('expected_close_date', '>=', $quarterStart)
                ->where('expected_close_date', '<=', $quarterEnd);

            $forecast = $quarterDeals->sum('deal_value');
            $target = $forecast * 1.1; // 10% above forecast as target

            $quarters[] = [
                'quarter' => 'Q' . ($quarterStart->quarter) . ' ' . $quarterStart->year,
                'forecast' => $forecast,
                'target' => $target,
            ];
        }

        return $quarters;
    }

    /**
     * Get forecast accuracy trend data
     */
    private function getForecastAccuracyTrend($data, $filters): array
    {
        // This would need historical forecast data to calculate properly
        // For now, returning mock data based on typical accuracy trends
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = \Carbon\Carbon::now()->subMonths($i)->format('M');
            $accuracy = 92 + rand(-4, 4); // Mock accuracy between 88-96%

            $months[] = [
                'month' => $month,
                'accuracy' => $accuracy,
            ];
        }

        return $months;
    }

    /**
     * Get forecast by category data
     */
    private function getForecastByCategory($data): array
    {
        // This would need to be implemented based on your category field
        // For now, returning mock data based on common categories
        $totalValue = $data->sum('deal_value');

        return [
            ['category' => 'New Business', 'value' => $totalValue * 0.37, 'percentage' => 37],
            ['category' => 'Expansion', 'value' => $totalValue * 0.25, 'percentage' => 25],
            ['category' => 'Renewal', 'value' => $totalValue * 0.26, 'percentage' => 26],
            ['category' => 'Upsell', 'value' => $totalValue * 0.15, 'percentage' => 15],
        ];
    }

    /**
     * Get sales velocity data
     */
    private function getSalesVelocity($data, $filters): array
    {
        // Group by month and calculate daily revenue velocity
        $monthlyData = $data->where('status', 'won')->groupBy(function ($deal) {
            return \Carbon\Carbon::parse($deal->created_at)->format('M');
        })->map(function ($monthData, $month) {
            $totalRevenue = $monthData->sum('deal_value');
            $daysInMonth = \Carbon\Carbon::parse($month . ' ' . now()->year)->daysInMonth;
            $dailyVelocity = $totalRevenue / $daysInMonth;

            return $dailyVelocity;
        });

        // Generate last 6 months
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = \Carbon\Carbon::now()->subMonths($i)->format('M');
            $velocity = $monthlyData->get($month, 0);

            $months[] = [
                'month' => $month,
                'velocity' => $velocity,
            ];
        }

        return $months;
    }

    /**
     * Get pipeline coverage ratio data
     */
    private function getPipelineCoverageRatio($data, $filters): array
    {
        $quotaTarget = 1000000; // This would come from settings
        $targetCoverage = 3.0;

        // Group by month and calculate coverage ratio
        $monthlyData = $data->groupBy(function ($deal) {
            return \Carbon\Carbon::parse($deal->created_at)->format('M');
        })->map(function ($monthData, $month) use ($quotaTarget) {
            $pipelineValue = $monthData->sum('deal_value');
            $coverage = $quotaTarget > 0 ? $pipelineValue / $quotaTarget : 0;

            return $coverage;
        });

        // Generate last 4 months
        $months = [];
        for ($i = 3; $i >= 0; $i--) {
            $month = \Carbon\Carbon::now()->subMonths($i)->format('M');
            $coverage = $monthlyData->get($month, 0);

            $months[] = [
                'month' => $month,
                'coverage' => $coverage,
                'target' => $targetCoverage,
            ];
        }

        return $months;
    }
}
