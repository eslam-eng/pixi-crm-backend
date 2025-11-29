<?php

namespace App\Http\Controllers\Api\Report;

use App\DTO\Report\SalesPerformanceReportDTO;
use App\Http\Controllers\Controller;
use App\Services\Tenant\Report\ReportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RevenueAnalysisController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {
        // $this->middleware('permission:view-revenue-reports');
    }

    /**
     * Get comprehensive revenue analysis dashboard data
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);

            // Get revenue analysis data
            $revenueData = $this->reportService->executeRevenueAnalysisReport($filters);

            // Calculate key metrics
            $keyMetrics = $this->calculateKeyMetrics($revenueData['data']);

            // Get revenue over time (monthly)
            $revenueOverTime = $this->getRevenueOverTime($revenueData['data'], $filters);

            // Get MRR growth rate
            $mrrGrowthRate = $this->getMRRGrowthRate($revenueData['data'], $filters);

            // Get revenue by package type
            $revenueByPackage = $this->getRevenueByPackage($revenueData['data']);

            // Get revenue by payment method
            $revenueByPaymentMethod = $this->getRevenueByPaymentMethod($revenueData['data']);

            // Get detailed breakdowns
            $revenueBreakdown = $this->getRevenueBreakdown($revenueData['data']);
            $growthMetrics = $this->getGrowthMetrics($revenueData['data'], $filters);
            $clientMetrics = $this->getClientMetrics($revenueData['data']);

            return ApiResponse([
                'key_metrics' => $keyMetrics,
                'revenue_over_time' => $revenueOverTime,
                'mrr_growth_rate' => $mrrGrowthRate,
                'revenue_by_package' => $revenueByPackage,
                'revenue_by_payment_method' => $revenueByPaymentMethod,
                'revenue_breakdown' => $revenueBreakdown,
                'growth_metrics' => $growthMetrics,
                'client_metrics' => $clientMetrics,
                'summary' => $revenueData['summary'],
                'records_count' => $revenueData['records_count'],
            ], 'Revenue analysis dashboard data retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get revenue trends
     */
    public function revenueTrends(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeRevenueAnalysisReport($filters);

            return ApiResponse([
                'data' => $result['data'],
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Revenue trends report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get revenue by product
     */
    public function revenueByProduct(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeRevenueAnalysisReport($filters);

            return ApiResponse([
                'data' => $result['data'],
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Revenue by product report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get revenue by customer segment
     */
    public function revenueByCustomerSegment(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeRevenueAnalysisReport($filters);

            return ApiResponse([
                'data' => $result['data'],
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Revenue by customer segment report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get revenue forecast vs actual
     */
    public function revenueForecastVsActual(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeRevenueAnalysisReport($filters);

            return ApiResponse([
                'data' => $result['data'],
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Revenue forecast vs actual report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Calculate key metrics for the dashboard
     */
    private function calculateKeyMetrics($data): array
    {
        $totalRevenue = $data->sum('total_amount');
        $recurringRevenue = $data->where('payment_status', 'partial')->sum('partial_amount_paid');
        $oneTimeRevenue = $data->where('payment_status', 'paid')->sum('total_amount');
        $avgDealValue = $data->count() > 0 ? $data->avg('total_amount') : 0;
        $uniqueClients = $data->pluck('company_name')->unique()->count();
        $revenuePerClient = $uniqueClients > 0 ? $totalRevenue / $uniqueClients : 0;

        return [
            'total_revenue' => [
                'value' => '$' . number_format($totalRevenue),
            ],
            'monthly_recurring_revenue' => [
                'value' => '$' . number_format($recurringRevenue),
            ],
            'avg_deal_value' => [
                'value' => '$' . number_format($avgDealValue),
            ],
            'revenue_per_client' => [
                'value' => '$' . number_format($revenuePerClient),
            ],
        ];
    }

    /**
     * Get revenue over time data
     */
    private function getRevenueOverTime($data, $filters): array
    {
        // Group by month and calculate revenue
        $monthlyRevenue = $data->groupBy(function ($deal) {
            return \Carbon\Carbon::parse($deal->sale_date)->format('M');
        })->map(function ($monthData) {
            return [
                'total_revenue' => $monthData->sum('total_amount'),
                'recurring_revenue' => $monthData->where('payment_status', 'recurring')->sum('total_amount'),
            ];
        });

        // Generate last 12 months
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = \Carbon\Carbon::now()->subMonths($i)->format('M');
            $monthData = $monthlyRevenue->get($month, ['total_revenue' => 0, 'recurring_revenue' => 0]);
            $months[] = [
                'month' => $month,
                'total_revenue' => $monthData['total_revenue'],
                'recurring_revenue' => $monthData['recurring_revenue'],
            ];
        }

        return $months;
    }

    /**
     * Get MRR growth rate data
     */
    private function getMRRGrowthRate($data, $filters): array
    {
        // Group by month and calculate MRR
        $monthlyMRR = $data->where('payment_status', 'partial')
            ->groupBy(function ($deal) {
                return \Carbon\Carbon::parse($deal->sale_date)->format('M');
            })->map(function ($monthData) {
                return $monthData->sum('partial_amount_paid');
            });


        // Calculate growth rates
        $months = [];
        $previousMRR = 0;

        for ($i = 11; $i >= 0; $i--) {
            $month = \Carbon\Carbon::now()->subMonths($i)->format('M');
            $currentMRR = $monthlyMRR->get($month, 0);
            $growthRate = $previousMRR > 0 ? (($currentMRR - $previousMRR) / $previousMRR) * 100 : 0;

            $months[] = [
                'month' => $month,
                'mrr' => $currentMRR,
                'growth_rate' => round($growthRate, 1),
            ];

            $previousMRR = $currentMRR;
        }

        return $months;
    }

    /**
     * Get revenue by package type
     */
    private function getRevenueByPackage($data): array
    {
        // This would need to be implemented based on your package/subscription structure
        // For now, returning mock data
        return [
            ['package' => 'Enterprise', 'revenue' => 309700, 'percentage' => 38],
            ['package' => 'Professional', 'revenue' => 244500, 'percentage' => 30],
            ['package' => 'Standard', 'revenue' => 179300, 'percentage' => 22],
            ['package' => 'Basic', 'revenue' => 81500, 'percentage' => 10],
        ];
    }

    /**
     * Get revenue by payment method
     */
    private function getRevenueByPaymentMethod($data): array
    {
        // This would need to be implemented based on your payment method structure
        // For now, returning mock data
        return [
            ['payment_method' => 'Credit Card', 'revenue' => 450000],
            ['payment_method' => 'Bank Transfer', 'revenue' => 180000],
            ['payment_method' => 'PayPal', 'revenue' => 60000],
            ['payment_method' => 'Other', 'revenue' => 30000],
        ];
    }

    /**
     * Get revenue breakdown
     */
    private function getRevenueBreakdown($data): array
    {
        $totalRevenue = $data->sum('total_amount');
        $recurringRevenue = $data->where('payment_status', 'partial')->sum('partial_amount_paid');
        $oneTimeRevenue = $data->where('payment_status', 'paid')->sum('total_amount');

        return [
            'recurring_revenue' => [
                'amount' => $recurringRevenue,
                'percentage' => $totalRevenue > 0 ? round(($recurringRevenue / $totalRevenue) * 100) : 0,
            ],
            'one_time_revenue' => [
                'amount' => $oneTimeRevenue,
                'percentage' => $totalRevenue > 0 ? round(($oneTimeRevenue / $totalRevenue) * 100) : 0,
            ],
            'total' => $totalRevenue,
        ];
    }

    /**
     * Get growth metrics
     */
    private function getGrowthMetrics($data, $filters): array
    {
        // Group data by month for analysis
        $monthlyRevenue = $data->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->sale_date)->format('Y-m');
        })->map(function ($monthData) {
            return $monthData->sum('total_amount');
        });

        // Calculate current period metrics
        $currentMonth = \Carbon\Carbon::now()->format('Y-m');
        $currentMonthRevenue = $monthlyRevenue->get($currentMonth, 0);

        // Calculate YoY Growth
        $previousYearMonth = \Carbon\Carbon::parse($currentMonth)->subYear()->format('Y-m');
        $previousYearRevenue = $monthlyRevenue->get($previousYearMonth, 0);
        $yoyGrowth = 0;
        if ($previousYearRevenue > 0) {
            $yoyGrowth = (($currentMonthRevenue - $previousYearRevenue) / $previousYearRevenue) * 100;
        }

        // Calculate MoM Growth
        $previousMonth = \Carbon\Carbon::parse($currentMonth)->subMonth()->format('Y-m');
        $previousMonthRevenue = $monthlyRevenue->get($previousMonth, 0);
        $momGrowth = 0;
        if ($previousMonthRevenue > 0) {
            $momGrowth = (($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100;
        }

        // Calculate Projected Annual
        $monthsElapsed = \Carbon\Carbon::now()->month;
        $projectedAnnual = 0;
        if ($monthsElapsed > 0 && $currentMonthRevenue > 0) {
            $projectedAnnual = ($currentMonthRevenue / $monthsElapsed) * 12;
        }

        return [
            'yoy_growth' => ($yoyGrowth >= 0 ? '+' : '') . round($yoyGrowth, 1) . '%',
            'mom_growth' => ($momGrowth >= 0 ? '+' : '') . round($momGrowth, 1) . '%',
            'projected_annual' => '$' . number_format($projectedAnnual / 1000000, 1) . 'M'
        ];
    }


    /**
     * Get client metrics
     */
    private function getClientMetrics($data): array
    {
        $uniqueClients = $data->pluck('company_name')->unique()->count();
        $totalRevenue = $data->sum('total_amount');
        $avgClientValue = $uniqueClients > 0 ? $totalRevenue / $uniqueClients : 0;

        return [
            'active_clients' => $uniqueClients,
            'avg_client_value' => '$' . number_format($avgClientValue),
            'lifetime_value' => '$32,450', // This would need more complex calculation
        ];
    }
}
