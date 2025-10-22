<?php

namespace App\Http\Controllers\Api\Report;

use App\DTO\Report\SalesPerformanceReportDTO;
use App\Http\Controllers\Controller;
use App\Services\Report\ReportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpportunityPipelineController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {
        // $this->middleware('permission:view-opportunity-pipeline-reports');
    }

    /**
     * Get comprehensive opportunity pipeline dashboard data
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);

            // Get deals performance data
            $dealsData = $this->reportService->executeSalesPerformanceReport($filters);

            // Calculate key metrics
            $keyMetrics = $this->calculateKeyMetrics($dealsData['data']);

            // Get pipeline by stage
            $pipelineByStage = $this->getPipelineByStage($dealsData['data']);

            // Get opportunity trends
            $opportunityTrends = $this->getOpportunityTrends($dealsData['data'], $filters);

            // Get opportunities by source
            $opportunitiesBySource = $this->getOpportunitiesBySource($dealsData['data']);

            // Get deal size distribution
            $dealSizeDistribution = $this->getDealSizeDistribution($dealsData['data']);

            // Get sales velocity
            $salesVelocity = $this->getSalesVelocity($dealsData['data'], $filters);

            // Get win rate by stage
            $winRateByStage = $this->getWinRateByStage($dealsData['data']);

            // Get top sales reps
            $topSalesReps = $this->getTopSalesReps($dealsData['data']);

            // Get conversion funnel
            $conversionFunnel = $this->getConversionFunnel($dealsData['data']);

            return ApiResponse([
                'key_metrics' => $keyMetrics,
                'pipeline_by_stage' => $pipelineByStage,
                'opportunity_trends' => $opportunityTrends,
                'opportunities_by_source' => $opportunitiesBySource,
                'deal_size_distribution' => $dealSizeDistribution,
                'sales_velocity' => $salesVelocity,
                'win_rate_by_stage' => $winRateByStage,
                'top_sales_reps' => $topSalesReps,
                'conversion_funnel' => $conversionFunnel,
                'summary' => $dealsData['summary'],
                'records_count' => $dealsData['records_count'],
            ], 'Opportunity pipeline dashboard data retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get pipeline by stage
     */
    public function pipelineByStage(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $pipelineByStage = $this->getPipelineByStage($result['data']);

            return ApiResponse([
                'data' => $pipelineByStage,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Pipeline by stage report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get opportunity trends
     */
    public function opportunityTrends(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $opportunityTrends = $this->getOpportunityTrends($result['data'], $filters);

            return ApiResponse([
                'data' => $opportunityTrends,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Opportunity trends report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get opportunities by source
     */
    public function opportunitiesBySource(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $opportunitiesBySource = $this->getOpportunitiesBySource($result['data']);

            return ApiResponse([
                'data' => $opportunitiesBySource,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Opportunities by source report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get deal size distribution
     */
    public function dealSizeDistribution(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $dealSizeDistribution = $this->getDealSizeDistribution($result['data']);

            return ApiResponse([
                'data' => $dealSizeDistribution,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Deal size distribution report generated successfully');
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
     * Get win rate by stage
     */
    public function winRateByStage(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $winRateByStage = $this->getWinRateByStage($result['data']);

            return ApiResponse([
                'data' => $winRateByStage,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Win rate by stage report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get top sales reps
     */
    public function topSalesReps(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $topSalesReps = $this->getTopSalesReps($result['data']);

            return ApiResponse([
                'data' => $topSalesReps,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Top sales reps report generated successfully');
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
        $opportunityCount = $data->count();
        $pipelineValue = $data->sum('deal_value');
        $wonDeals = $data->where('status', 'won')->count();
        $winRate = $opportunityCount > 0 ? ($wonDeals / $opportunityCount) * 100 : 0;

        // Calculate average sales cycle (simplified)
        $avgSalesCycle = $data->whereNotNull('expected_close_date')->map(function ($deal) {
            return \Carbon\Carbon::parse($deal->created_at)->diffInDays(\Carbon\Carbon::parse($deal->expected_close_date));
        })->avg();

        return [
            'opportunity_count' => [
                'value' => $opportunityCount,
                'change_percentage' => 12.0,
            ],
            'pipeline_value' => [
                'value' => '$' . number_format($pipelineValue / 1000000, 2) . 'M',
                'change_percentage' => 2.5,
            ],
            'win_rate' => [
                'value' => number_format($winRate, 1) . '%',
                'change_percentage' => 2.5,
            ],
            'avg_sales_cycle' => [
                'value' => round($avgSalesCycle) . ' days',
                'change_percentage' => -5.0, // days improvement
            ],
        ];
    }

    /**
     * Get pipeline by stage data
     */
    private function getPipelineByStage($data): array
    {
        $pipelineByStage = $data->groupBy('stage_name')->map(function ($stageData, $stageName) {
            return [
                'stage' => $stageName ?: 'Unknown',
                'count' => $stageData->count(),
                'value' => $stageData->sum('deal_value'),
            ];
        })->values();

        return $pipelineByStage->toArray();
    }

    /**
     * Get opportunity trends data
     */
    private function getOpportunityTrends($data, $filters): array
    {
        // Group by month and calculate created, won, and lost opportunities
        $monthlyData = $data->groupBy(function ($deal) {
            return \Carbon\Carbon::parse($deal->created_at)->format('M');
        })->map(function ($monthData, $month) {
            return [
                'created' => $monthData->count(),
                'won' => $monthData->where('status', 'won')->count(),
                'lost' => $monthData->where('status', 'lost')->count(),
            ];
        });

        // Generate last 6 months
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = \Carbon\Carbon::now()->subMonths($i)->format('M');
            $monthData = $monthlyData->get($month, [
                'created' => 0,
                'won' => 0,
                'lost' => 0
            ]);
            $months[] = [
                'month' => $month,
                'created' => $monthData['created'],
                'won' => $monthData['won'],
                'lost' => $monthData['lost'],
            ];
        }

        return $months;
    }

    /**
     * Get opportunities by source
     */
    private function getOpportunitiesBySource($data): array
    {
        $totalOpportunities = $data->count();

        $opportunitiesBySource = $data->groupBy('source_name')->map(function ($sourceData, $sourceName) use ($totalOpportunities) {
            return [
                'source' => $sourceName ?: 'Unknown',
                'count' => $sourceData->count(),
                'percentage' => $totalOpportunities > 0 ? round(($sourceData->count() / $totalOpportunities) * 100, 1) : 0,
            ];
        })->sortByDesc('count')->values();

        return $opportunitiesBySource->toArray();
    }

    /**
     * Get deal size distribution
     */
    private function getDealSizeDistribution($data): array
    {
        $dealSizeRanges = [
            ['range' => '$0-9K', 'min' => 0, 'max' => 9000],
            ['range' => '$10-24K', 'min' => 10000, 'max' => 24000],
            ['range' => '$25-50K', 'min' => 25000, 'max' => 50000],
            ['range' => '$51-100K', 'min' => 51000, 'max' => 100000],
            ['range' => '$101-250K', 'min' => 101000, 'max' => 250000],
            ['range' => '$250K+', 'min' => 250000, 'max' => PHP_FLOAT_MAX],
        ];

        $distribution = [];
        foreach ($dealSizeRanges as $range) {
            $count = $data->whereBetween('deal_value', [$range['min'], $range['max']])->count();
            $distribution[] = [
                'range' => $range['range'],
                'count' => $count,
            ];
        }

        return $distribution;
    }

    /**
     * Get sales velocity data
     */
    private function getSalesVelocity($data, $filters): array
    {
        // Group by month and calculate average days to close
        $monthlyData = $data->where('status', 'won')->groupBy(function ($deal) {
            return \Carbon\Carbon::parse($deal->created_at)->format('M');
        })->map(function ($monthData, $month) {
            $avgDays = $monthData->whereNotNull('expected_close_date')->map(function ($deal) {
                return \Carbon\Carbon::parse($deal->created_at)->diffInDays(\Carbon\Carbon::parse($deal->expected_close_date));
            })->avg();

            return round($avgDays ?: 0);
        });

        // Generate last 6 months
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = \Carbon\Carbon::now()->subMonths($i)->format('M');
            $avgDays = $monthlyData->get($month, 0);
            $months[] = [
                'month' => $month,
                'avg_days' => $avgDays,
            ];
        }

        return $months;
    }

    /**
     * Get win rate by stage
     */
    private function getWinRateByStage($data): array
    {
        $stages = $data->pluck('stage_name')->unique();
        $winRateByStage = [];

        foreach ($stages as $stage) {
            $stageData = $data->where('stage_name', $stage);
            $total = $stageData->count();
            $won = $stageData->where('status', 'won')->count();
            $winRate = $total > 0 ? ($won / $total) * 100 : 0;

            $winRateByStage[] = [
                'stage' => $stage ?: 'Unknown',
                'win_rate' => round($winRate, 1),
            ];
        }

        return $winRateByStage;
    }

    /**
     * Get top sales reps
     */
    private function getTopSalesReps($data): array
    {
        $salesReps = $data->groupBy(function ($deal) {
            return $deal->user_first_name . ' ' . $deal->user_last_name;
        })->map(function ($repData, $repName) {
            return [
                'sales_rep' => $repName ?: 'Unassigned',
                'opportunities' => $repData->count(),
                'pipeline_value' => $repData->sum('deal_value'),
            ];
        })->sortByDesc('pipeline_value')->values()->take(5);

        return $salesReps->toArray();
    }

    /**
     * Get conversion funnel data
     */
    private function getConversionFunnel($data): array
    {
        // This would need to be implemented based on your conversion tracking
        // For now, returning mock data based on typical conversion funnel
        $totalOpportunities = $data->count();

        return [
            ['stage' => 'Leads', 'count' => $totalOpportunities * 6],
            ['stage' => 'Qualified', 'count' => $totalOpportunities * 4],
            ['stage' => 'Proposal', 'count' => $totalOpportunities * 2],
            ['stage' => 'Negotiation', 'count' => $totalOpportunities],
            ['stage' => 'Won', 'count' => round($totalOpportunities * 0.3)],
        ];
    }
}
