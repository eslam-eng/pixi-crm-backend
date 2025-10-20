<?php

namespace App\Http\Controllers\Api\Report;

use App\DTO\Report\SalesPerformanceReportDTO;
use App\Http\Controllers\Controller;
use App\Services\Tenant\Report\ReportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DealAnalysisController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {
        // $this->middleware('permission:view-deal-reports');
    }

    /**
     * Get comprehensive deal analysis dashboard data
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);

            // Get deals performance data
            $dealsData = $this->reportService->executeDealsReport($filters);

            // Calculate key metrics
            $keyMetrics = $this->calculateKeyMetrics($dealsData['data']);

            // Get deals over time
            $dealsOverTime = $this->getDealsOverTime($dealsData['data'], $filters);

            // Get deals by stage
            $dealsByStage = $this->getDealsByStage($dealsData['data']);

            // Get deals by source
            $dealsBySource = $this->getDealsBySource($dealsData['data']);

            // Get deal value by stage
            $dealValueByStage = $this->getDealValueByStage($dealsData['data']);

            // Get conversion funnel
            $conversionFunnel = $this->getConversionFunnel($dealsData['data']);

            return ApiResponse([
                'key_metrics' => $keyMetrics,
                'deals_over_time' => $dealsOverTime,
                'deals_by_stage' => $dealsByStage,
                'deals_by_source' => $dealsBySource,
                'deal_value_by_stage' => $dealValueByStage,
                'conversion_funnel' => $conversionFunnel,
                'summary' => $dealsData['summary'],
                'records_count' => $dealsData['records_count'],
            ], 'Deal analysis dashboard data retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get deals over time
     */
    public function dealsOverTime(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $dealsOverTime = $this->getDealsOverTime($result['data'], $filters);

            return ApiResponse([
                'data' => $dealsOverTime,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Deals over time report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get deals by stage
     */
    public function dealsByStage(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $dealsByStage = $this->getDealsByStage($result['data']);

            return ApiResponse([
                'data' => $dealsByStage,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Deals by stage report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get deals by source
     */
    public function dealsBySource(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $dealsBySource = $this->getDealsBySource($result['data']);

            return ApiResponse([
                'data' => $dealsBySource,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Deals by source report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get deal value by stage
     */
    public function dealValueByStage(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $dealValueByStage = $this->getDealValueByStage($result['data']);

            return ApiResponse([
                'data' => $dealValueByStage,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Deal value by stage report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get conversion funnel
     */
    public function conversionFunnel(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $conversionFunnel = $this->getConversionFunnel($result['data']);

            return ApiResponse([
                'data' => $conversionFunnel,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Conversion funnel report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Calculate key metrics for the dashboard
     */
    private function calculateKeyMetrics($data): array
    {
        $totalDeals = $data->count();
        $wonDeals = $data->where('status', 'won')->count();
        $winRate = $totalDeals > 0 ? ($wonDeals / $totalDeals) * 100 : 0;
        $avgDealSize = $totalDeals > 0 ? $data->avg('deal_value') : 0;

        return [
            'total_deals' => [
                'value' => $totalDeals,
            ],
            'won_deals' => [
                'value' => $wonDeals,
            ],
            'win_rate' => [
                'value' => number_format($winRate, 1) . '%',
            ],
            'avg_deal_size' => [
                'value' => '$' . number_format($avgDealSize),
            ],
        ];
    }

    /**
     * Get deals over time data
     */
    private function getDealsOverTime($data, $filters): array
    {
        // Group by month and calculate created vs won deals
        $monthlyData = $data->groupBy(function ($deal) {
            return \Carbon\Carbon::parse($deal->created_at)->format('M');
        })->map(function ($monthData) {
            return [
                'created' => $monthData->count(),
                'won' => $monthData->where('status', 'won')->count(),
            ];
        });

        // Generate last 12 months
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = \Carbon\Carbon::now()->subMonths($i)->format('M');
            $monthData = $monthlyData->get($month, ['created' => 0, 'won' => 0]);
            $months[] = [
                'month' => $month,
                'created' => $monthData['created'],
                'won' => $monthData['won'],
            ];
        }

        return $months;
    }

    /**
     * Get deals by stage
     */
    private function getDealsByStage($data): array
    {
        $totalDeals = $data->count();

        $dealsByStage = $data->groupBy('stage_name')->map(function ($stageData, $stageName) use ($totalDeals) {
            return [
                'stage' => $stageName ?: 'Unknown',
                'count' => $stageData->count(),
                'percentage' => $totalDeals > 0 ? round(($stageData->count() / $totalDeals) * 100, 1) : 0,
            ];
        })->values();

        return $dealsByStage->toArray();
    }

    /**
     * Get deals by source
     */
    private function getDealsBySource($data): array
    {
        $dealsBySource = $data->groupBy('source_name')->map(function ($sourceData, $sourceName) {
            return [
                'source' => $sourceName ?: 'Unknown',
                'count' => $sourceData->count(),
            ];
        })->sortByDesc('count')->values();

        return $dealsBySource->toArray();
    }

    /**
     * Get deal value by stage
     */
    private function getDealValueByStage($data): array
    {
        $dealValueByStage = $data->groupBy('stage_name')->map(function ($stageData, $stageName) {
            return [
                'stage' => $stageName ?: 'Unknown',
                'total_value' => $stageData->sum('deal_value'),
                'avg_value' => $stageData->avg('deal_value'),
            ];
        })->sortByDesc('total_value')->values();

        return $dealValueByStage->toArray();
    }

    /**
     * Get conversion funnel data
     */
    private function getConversionFunnel($data): array
    {
        // Define funnel stages in order
        $funnelStages = [
            'Prospecting',
            'Qualification',
            'Proposal',
            'Negotiation',
            'Closed Won',
            'Closed Lost'
        ];

        $funnelData = [];
        $totalDeals = $data->count();

        foreach ($funnelStages as $stage) {
            $stageData = $data->where('stage_name', $stage);
            $count = $stageData->count();
            $conversionRate = $totalDeals > 0 ? ($count / $totalDeals) * 100 : 0;

            $funnelData[] = [
                'stage' => $stage,
                'count' => $count,
                'conversion_rate' => round($conversionRate, 1),
            ];
        }

        return $funnelData;
    }
}
