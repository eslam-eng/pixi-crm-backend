<?php

namespace App\Http\Controllers\Api\Report;

use App\DTO\Report\RevenueAnalysisReportDTO;
use App\Http\Controllers\Controller;
use App\Services\Report\ReportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RevenueAnalysisController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    /**
     * Get revenue trends report
     */
    public function revenueTrends(Request $request): JsonResponse
    {
        try {
            $filters = RevenueAnalysisReportDTO::fromRequest($request);
            $result = $this->reportService->executeRevenueAnalysisReport($filters);

            // Group by month for trend analysis
            $trendData = $result['data']->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->sale_date)->format('Y-m');
            })->map(function ($monthData, $month) {
                return [
                    'month' => $month,
                    'revenue' => $monthData->sum('total_amount'),
                    'deals_count' => $monthData->count(),
                    'average_deal_size' => $monthData->avg('total_amount'),
                ];
            })->values();

            return ApiResponse([
                'trend_data' => $trendData,
                'summary' => $result['summary'],
            ], 'Revenue trends report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get revenue by product/service report
     */
    public function revenueByProduct(Request $request): JsonResponse
    {
        try {
            $filters = RevenueAnalysisReportDTO::fromRequest($request);
            $result = $this->reportService->executeProductPerformanceReport($filters);

            $productData = $result['data']->groupBy('product_name')->map(function ($productData, $productName) {
                return [
                    'product_name' => $productName,
                    'revenue' => $productData->sum('total_revenue'),
                    'quantity_sold' => $productData->sum('quantity'),
                    'average_price' => $productData->avg('unit_price'),
                    'deals_count' => $productData->count(),
                ];
            })->values();

            return ApiResponse([
                'product_data' => $productData,
                'summary' => $result['summary'],
            ], 'Revenue by product report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get revenue by customer segment report
     */
    public function revenueByCustomerSegment(Request $request): JsonResponse
    {
        try {
            $filters = RevenueAnalysisReportDTO::fromRequest($request);
            $result = $this->reportService->executeRevenueAnalysisReport($filters);

            $segmentData = $result['data']->groupBy('company_name')->map(function ($companyData, $companyName) {
                return [
                    'company_name' => $companyName,
                    'revenue' => $companyData->sum('total_amount'),
                    'deals_count' => $companyData->count(),
                    'average_deal_size' => $companyData->avg('total_amount'),
                    'last_deal_date' => $companyData->max('sale_date'),
                ];
            })->values();

            return ApiResponse([
                'segment_data' => $segmentData,
                'summary' => $result['summary'],
            ], 'Revenue by customer segment report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get revenue forecast vs actual report
     */
    public function revenueForecastVsActual(Request $request): JsonResponse
    {
        try {
            $filters = RevenueAnalysisReportDTO::fromRequest($request);
            $result = $this->reportService->executeForecastingReport($filters);

            $forecastData = $result['data']->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->expected_close_date)->format('Y-m');
            })->map(function ($monthData, $month) {
                // For forecasting, we'll use weighted values based on win probability
                $forecastedRevenue = $monthData->sum('weighted_value');
                $potentialRevenue = $monthData->sum('deal_value');
                $averageWinRate = $monthData->avg('win_probability');

                return [
                    'month' => $month,
                    'forecasted_revenue' => round($forecastedRevenue, 2),
                    'potential_revenue' => round($potentialRevenue, 2),
                    'average_win_rate' => round($averageWinRate, 2),
                    'opportunities_count' => $monthData->count(),
                ];
            })->values();

            return ApiResponse([
                'forecast_data' => $forecastData,
                'summary' => $result['summary'],
            ], 'Revenue forecast vs actual report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
