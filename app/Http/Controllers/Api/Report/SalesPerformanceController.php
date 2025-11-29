<?php

namespace App\Http\Controllers\Api\Report;

use App\DTO\Report\SalesPerformanceReportDTO;
use App\Http\Controllers\Controller;
use App\Services\Tenant\Report\ReportService;
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
     * Get comprehensive sales performance dashboard data
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);

            // Get all the data needed for the dashboard
            $dealsPerformance = $this->reportService->executeSalesPerformanceReport($filters);
            $revenueAnalysis = $this->reportService->executeRevenueAnalysisReport($filters);
            $pipelineFunnel = $this->reportService->executeOpportunityPipelineReport($filters);

            // Calculate KPIs
            $kpis = $this->calculateKPIs($dealsPerformance['data']);

            // Transform pipeline funnel data
            $funnelData = $pipelineFunnel['data']->groupBy('stage_name')->map(function ($stageData, $stageName) {
                return [
                    'stage' => $stageName,
                    'count' => $stageData->count(),
                    'value' => $stageData->sum('deal_value'),
                    'probability' => $stageData->avg('win_probability'),
                ];
            })->values();

            // Get revenue trends (monthly)
            $revenueTrends = $this->getRevenueTrends($revenueAnalysis['data'], $filters);

            // Get deals by source
            $dealsBySource = $this->getDealsBySource($dealsPerformance['data']);

            // Get sales rep performance
            $salesRepPerformance = $this->getSalesRepPerformance($dealsPerformance['data']);

            return ApiResponse([
                'kpis' => $kpis,
                'pipeline_funnel' => $funnelData,
                'revenue_trends' => $revenueTrends,
                'deals_by_source' => $dealsBySource,
                'sales_rep_performance' => $salesRepPerformance,
                'summary' => $dealsPerformance['summary'],
                'records_count' => $dealsPerformance['records_count'],
            ], 'Sales performance dashboard data retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
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

    /**
     * Calculate KPIs for the dashboard
     */
    private function calculateKPIs($data): array
    {
        $totalDeals = $data->count();
        $wonDeals = $data->where('status', 'won')->count();
        $lostDeals = $data->where('status', 'lost')->count();
        $pipelineValue = $data->sum('deal_value');
        $avgDealSize = $totalDeals > 0 ? $data->avg('deal_value') : 0;
        $winRate = $totalDeals > 0 ? ($wonDeals / $totalDeals) * 100 : 0;

        // Calculate average sales cycle (simplified - using created_at to expected_close_date)
        $avgSalesCycle = $data->whereNotNull('expected_close_date')->map(function ($deal) {
            return \Carbon\Carbon::parse($deal->created_at)->diffInDays(\Carbon\Carbon::parse($deal->expected_close_date));
        })->avg();

        // Calculate conversion rate (simplified - deals that moved to won status)
        $conversionRate = $totalDeals > 0 ? ($wonDeals / $totalDeals) * 100 : 0;

        return [
            'total_deals' => [
                'value' => $totalDeals,
            ],
            'pipeline_value' => [
                'value' => number_format($pipelineValue / 1000, 1) . 'K',
            ],
            'win_rate' => [
                'value' => number_format($winRate, 1) . '%',
            ],
            'avg_sales_cycle' => [
                'value' => round($avgSalesCycle) . ' days',
            ],
            'won_deals' => [
                'value' => $wonDeals,
            ],
            'lost_deals' => [
                'value' => $lostDeals,
            ],
            'avg_deal_size' => [
                'value' => '$' . number_format($avgDealSize / 1000, 0) . 'K',
            ],
            'conversion_rate' => [
                'value' => number_format($conversionRate, 1) . '%',
            ],
        ];
    }

    /**
     * Get revenue trends data
     */
    private function getRevenueTrends($data, $filters): array
    {
        // Group by month and calculate revenue
        $monthlyRevenue = $data->groupBy(function ($deal) {
            return \Carbon\Carbon::parse($deal->sale_date)->format('M');
        })->map(function ($monthData) {
            return $monthData->sum('total_amount');
        });

        // Generate last 6 months
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = \Carbon\Carbon::now()->subMonths($i)->format('M');
            $months[] = [
                'month' => $month,
                'revenue' => $monthlyRevenue->get($month, 0),
            ];
        }

        return $months;
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
                'percentage' => 0, // Will be calculated below
            ];
        })->values();

        $totalDeals = $data->count();

        return $dealsBySource->map(function ($source) use ($totalDeals) {
            $source['percentage'] = $totalDeals > 0 ? round(($source['count'] / $totalDeals) * 100, 1) : 0;
            return $source;
        })->toArray();
    }

    /**
     * Get sales rep performance
     */
    private function getSalesRepPerformance($data): array
    {
        return $data->groupBy(function ($item) {
            return $item->user_first_name . ' ' . $item->user_last_name;
        })->map(function ($repData, $repName) {
            return [
                'sales_rep' => $repName ?: 'Unassigned',
                'deals_closed' => $repData->where('status', 'won')->count(),
                'total_deals' => $repData->count(),
                'total_value' => $repData->sum('deal_value'),
            ];
        })->sortByDesc('deals_closed')->values()->take(5)->toArray();
    }
}
