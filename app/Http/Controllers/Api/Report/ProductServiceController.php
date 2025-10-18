<?php

namespace App\Http\Controllers\Api\Report;

use App\DTO\Report\ProductServiceReportDTO;
use App\Http\Controllers\Controller;
use App\Services\Report\ReportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductServiceController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    /**
     * Get product performance report
     */
    public function productPerformance(Request $request): JsonResponse
    {
        try {
            $filters = ProductServiceReportDTO::fromRequest($request);
            $result = $this->reportService->executeProductPerformanceReport($filters);

            return ApiResponse([
                'data' => $result['data'],
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Product performance report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get service usage statistics
     */
    public function serviceUsageStatistics(Request $request): JsonResponse
    {
        try {
            $filters = ProductServiceReportDTO::fromRequest($request);
            $result = $this->reportService->executeProductPerformanceReport($filters);

            $usageData = $result['data']->groupBy('product_name')->map(function ($productData, $productName) {
                return [
                    'product_name' => $productName,
                    'total_usage' => $productData->sum('quantity'),
                    'revenue' => $productData->sum('total_revenue'),
                    'average_price' => $productData->avg('unit_price'),
                ];
            })->values();

            return ApiResponse([
                'usage_data' => $usageData,
                'summary' => $result['summary'],
            ], 'Service usage statistics generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get product revenue contribution
     */
    public function productRevenueContribution(Request $request): JsonResponse
    {
        try {
            $filters = ProductServiceReportDTO::fromRequest($request);
            $result = $this->reportService->executeProductPerformanceReport($filters);

            $contributionData = $result['data']->groupBy('product_name')->map(function ($productData, $productName) use ($result) {
                $productRevenue = $productData->sum('total_revenue');
                $totalRevenue = $result['data']->sum('total_revenue');

                return [
                    'product_name' => $productName,
                    'revenue' => $productRevenue,
                    'percentage' => $totalRevenue > 0 ? ($productRevenue / $totalRevenue) * 100 : 0,
                    'quantity_sold' => $productData->sum('quantity'),
                ];
            })->values();

            return ApiResponse([
                'contribution_data' => $contributionData,
                'summary' => $result['summary'],
            ], 'Product revenue contribution report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
