<?php

namespace App\Http\Controllers\Api\Report;

use App\DTO\Report\SalesPerformanceReportDTO;
use App\Http\Controllers\Controller;
use App\Services\Tenant\Report\ReportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WinLossAnalysisController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {
        // $this->middleware('permission:view-win-loss-reports');
    }

    /**
     * Get comprehensive win/loss analysis dashboard data
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);

            // Get deals performance data
            $dealsData = $this->reportService->executeSalesPerformanceReport($filters);

            // Calculate key metrics
            $keyMetrics = $this->calculateKeyMetrics($dealsData['data']);

            // Get win/loss trends
            $winLossTrends = $this->getWinLossTrends($dealsData['data'], $filters);

            // Get win rate trend
            $winRateTrend = $this->getWinRateTrend($dealsData['data'], $filters);

            // Get top win reasons
            $topWinReasons = $this->getTopWinReasons($dealsData['data']);

            // Get top loss reasons
            $topLossReasons = $this->getTopLossReasons($dealsData['data']);

            return ApiResponse([
                'key_metrics' => $keyMetrics,
                'win_loss_trends' => $winLossTrends,
                'win_rate_trend' => $winRateTrend,
                'top_win_reasons' => $topWinReasons,
                'top_loss_reasons' => $topLossReasons,
                'summary' => $dealsData['summary'],
                'records_count' => $dealsData['records_count'],
            ], 'Win/loss analysis dashboard data retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get win/loss trends
     */
    public function winLossTrends(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $winLossTrends = $this->getWinLossTrends($result['data'], $filters);

            return ApiResponse([
                'data' => $winLossTrends,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Win/loss trends report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get win rate trend
     */
    public function winRateTrend(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $winRateTrend = $this->getWinRateTrend($result['data'], $filters);

            return ApiResponse([
                'data' => $winRateTrend,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Win rate trend report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get top win reasons
     */
    public function topWinReasons(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $topWinReasons = $this->getTopWinReasons($result['data']);

            return ApiResponse([
                'data' => $topWinReasons,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Top win reasons report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get top loss reasons
     */
    public function topLossReasons(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $topLossReasons = $this->getTopLossReasons($result['data']);

            return ApiResponse([
                'data' => $topLossReasons,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Top loss reasons report generated successfully');
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
        $lostDeals = $data->where('status', 'lost')->count();
        $winRate = $totalDeals > 0 ? ($wonDeals / $totalDeals) * 100 : 0;
        $lossRate = $totalDeals > 0 ? ($lostDeals / $totalDeals) * 100 : 0;

        // Calculate average cycle time (simplified - using created_at to expected_close_date)
        $avgCycleTime = $data->whereNotNull('expected_close_date')->map(function ($deal) {
            return \Carbon\Carbon::parse($deal->created_at)->diffInDays(\Carbon\Carbon::parse($deal->expected_close_date));
        })->avg();

        return [
            'win_rate' => [
                'value' => number_format($winRate, 1) . '%',
            ],
            'loss_rate' => [
                'value' => number_format($lossRate, 1) . '%',
            ],
            'total_analyzed' => [
                'value' => $totalDeals,
            ],
            'avg_cycle_time' => [
                'value' => round($avgCycleTime) . ' days',
            ],
        ];
    }

    /**
     * Get win/loss trends data
     */
    private function getWinLossTrends($data, $filters): array
    {
        // Group by month and calculate win/loss rates
        $monthlyData = $data->groupBy(function ($deal) {
            return \Carbon\Carbon::parse($deal->created_at)->format('M');
        })->map(function ($monthData) {
            $total = $monthData->count();
            $won = $monthData->where('status', 'won')->count();
            $lost = $monthData->where('status', 'lost')->count();

            return [
                'won_rate' => $total > 0 ? ($won / $total) * 100 : 0,
                'loss_rate' => $total > 0 ? ($lost / $total) * 100 : 0,
                'won_count' => $won,
                'lost_count' => $lost,
            ];
        });

        // Generate last 12 months
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = \Carbon\Carbon::now()->subMonths($i)->format('M');
            $monthData = $monthlyData->get($month, [
                'won_rate' => 0,
                'loss_rate' => 0,
                'won_count' => 0,
                'lost_count' => 0
            ]);
            $months[] = [
                'month' => $month,
                'won_rate' => round($monthData['won_rate'], 1),
                'loss_rate' => round($monthData['loss_rate'], 1),
                'won_count' => $monthData['won_count'],
                'lost_count' => $monthData['lost_count'],
            ];
        }

        return $months;
    }

    /**
     * Get win rate trend data
     */
    private function getWinRateTrend($data, $filters): array
    {
        // Group by month and calculate win rate
        $monthlyData = $data->groupBy(function ($deal) {
            return \Carbon\Carbon::parse($deal->created_at)->format('M');
        })->map(function ($monthData) {
            $total = $monthData->count();
            $won = $monthData->where('status', 'won')->count();
            return $total > 0 ? ($won / $total) * 100 : 0;
        });

        // Generate last 12 months
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = \Carbon\Carbon::now()->subMonths($i)->format('M');
            $winRate = $monthlyData->get($month, 0);
            $months[] = [
                'month' => $month,
                'win_rate' => round($winRate, 1),
            ];
        }

        return $months;
    }

    /**
     * Get top win reasons
     */
    private function getTopWinReasons($data): array
    {
        // This would need to be implemented based on your win reason tracking
        // For now, returning mock data based on common win reasons
        $wonDeals = $data->where('status', 'won');

        // Mock data - in real implementation, you'd query actual win reasons
        return [
            ['reason' => 'Better Pricing', 'count' => 45, 'percentage' => 28.1],
            ['reason' => 'Product Features', 'count' => 38, 'percentage' => 23.8],
            ['reason' => 'Customer Service', 'count' => 32, 'percentage' => 20.0],
            ['reason' => 'Technical Support', 'count' => 25, 'percentage' => 15.6],
            ['reason' => 'Implementation Speed', 'count' => 20, 'percentage' => 12.5],
        ];
    }

    /**
     * Get top loss reasons
     */
    private function getTopLossReasons($data): array
    {
        // This would need to be implemented based on your loss reason tracking
        // For now, returning mock data based on common loss reasons
        $lostDeals = $data->where('status', 'lost');

        // Mock data - in real implementation, you'd query actual loss reasons
        return [
            ['reason' => 'Price Too High', 'count' => 28, 'percentage' => 35.0],
            ['reason' => 'Competitor Won', 'count' => 22, 'percentage' => 27.5],
            ['reason' => 'Budget Constraints', 'count' => 15, 'percentage' => 18.8],
            ['reason' => 'Timing Issues', 'count' => 10, 'percentage' => 12.5],
            ['reason' => 'Feature Gaps', 'count' => 5, 'percentage' => 6.2],
        ];
    }
}
