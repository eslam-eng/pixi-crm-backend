<?php

namespace App\Http\Controllers\Api\Report;

use App\DTO\Report\SalesPerformanceReportDTO;
use App\Http\Controllers\Controller;
use App\Services\Tenant\Report\ReportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConversionRateAnalysisController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {
        // $this->middleware('permission:view-conversion-rate-reports');
    }

    /**
     * Get comprehensive conversion rate analysis dashboard data
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);

            // Get deals performance data
            $dealsData = $this->reportService->executeSalesPerformanceReport($filters);

            // Get contact management data
            $contactData = $this->reportService->executeContactManagementReport($filters);

            // Calculate key metrics
            $keyMetrics = $this->calculateKeyMetrics($dealsData['data'], $contactData['data']);

            // Get conversion funnel
            $conversionFunnel = $this->getConversionFunnel($dealsData['data'], $contactData['data']);

            // Get stage conversion rates
            $stageConversionRates = $this->getStageConversionRates($dealsData['data'], $contactData['data']);

            // Get conversion trend
            $conversionTrend = $this->getConversionTrend($dealsData['data'], $filters);

            // Get conversion by source
            $conversionBySource = $this->getConversionBySource($dealsData['data'], $contactData['data']);

            // Get time to conversion
            $timeToConversion = $this->getTimeToConversion($dealsData['data']);

            // Get team performance
            $teamPerformance = $this->getTeamPerformance($dealsData['data']);

            // Get monthly conversion funnel
            $monthlyConversionFunnel = $this->getMonthlyConversionFunnel($dealsData['data'], $contactData['data'], $filters);

            // Get conversion by deal size
            $conversionByDealSize = $this->getConversionByDealSize($dealsData['data']);

            return ApiResponse([
                'key_metrics' => $keyMetrics,
                'conversion_funnel' => $conversionFunnel,
                'stage_conversion_rates' => $stageConversionRates,
                'conversion_trend' => $conversionTrend,
                'conversion_by_source' => $conversionBySource,
                'time_to_conversion' => $timeToConversion,
                'team_performance' => $teamPerformance,
                'monthly_conversion_funnel' => $monthlyConversionFunnel,
                'conversion_by_deal_size' => $conversionByDealSize,
                'summary' => $dealsData['summary'],
                'records_count' => $dealsData['records_count'],
            ], 'Conversion rate analysis dashboard data retrieved successfully');
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
            $dealsData = $this->reportService->executeSalesPerformanceReport($filters);
            $contactData = $this->reportService->executeContactManagementReport($filters);

            $conversionFunnel = $this->getConversionFunnel($dealsData['data'], $contactData['data']);

            return ApiResponse([
                'data' => $conversionFunnel,
                'summary' => $dealsData['summary'],
                'records_count' => $dealsData['records_count'],
            ], 'Conversion funnel report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get stage conversion rates
     */
    public function stageConversionRates(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $dealsData = $this->reportService->executeSalesPerformanceReport($filters);
            $contactData = $this->reportService->executeContactManagementReport($filters);

            $stageConversionRates = $this->getStageConversionRates($dealsData['data'], $contactData['data']);

            return ApiResponse([
                'data' => $stageConversionRates,
                'summary' => $dealsData['summary'],
                'records_count' => $dealsData['records_count'],
            ], 'Stage conversion rates report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get conversion trend
     */
    public function conversionTrend(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $conversionTrend = $this->getConversionTrend($result['data'], $filters);

            return ApiResponse([
                'data' => $conversionTrend,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Conversion trend report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get conversion by source
     */
    public function conversionBySource(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $dealsData = $this->reportService->executeSalesPerformanceReport($filters);
            $contactData = $this->reportService->executeContactManagementReport($filters);

            $conversionBySource = $this->getConversionBySource($dealsData['data'], $contactData['data']);

            return ApiResponse([
                'data' => $conversionBySource,
                'summary' => $dealsData['summary'],
                'records_count' => $dealsData['records_count'],
            ], 'Conversion by source report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get time to conversion
     */
    public function timeToConversion(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $timeToConversion = $this->getTimeToConversion($result['data']);

            return ApiResponse([
                'data' => $timeToConversion,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Time to conversion report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get team performance
     */
    public function teamPerformance(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $teamPerformance = $this->getTeamPerformance($result['data']);

            return ApiResponse([
                'data' => $teamPerformance,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Team performance report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get monthly conversion funnel
     */
    public function monthlyConversionFunnel(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $dealsData = $this->reportService->executeSalesPerformanceReport($filters);
            $contactData = $this->reportService->executeContactManagementReport($filters);

            $monthlyConversionFunnel = $this->getMonthlyConversionFunnel($dealsData['data'], $contactData['data'], $filters);

            return ApiResponse([
                'data' => $monthlyConversionFunnel,
                'summary' => $dealsData['summary'],
                'records_count' => $dealsData['records_count'],
            ], 'Monthly conversion funnel report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get conversion by deal size
     */
    public function conversionByDealSize(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeSalesPerformanceReport($filters);

            $conversionByDealSize = $this->getConversionByDealSize($result['data']);

            return ApiResponse([
                'data' => $conversionByDealSize,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Conversion by deal size report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Calculate key metrics for the dashboard
     */
    private function calculateKeyMetrics($dealsData, $contactData): array
    {
        $totalContacts = $contactData->count();
        $totalDeals = $dealsData->count();
        $wonDeals = $dealsData->where('status', 'won')->count();

        // Calculate overall conversion rate
        $overallConversionRate = $totalContacts > 0 ? ($wonDeals / $totalContacts) * 100 : 0;

        // Calculate lead conversion rate
        $leadConversionRate = $totalContacts > 0 ? ($totalDeals / $totalContacts) * 100 : 0;

        // Calculate opportunity conversion rate
        $opportunityConversionRate = $totalDeals > 0 ? ($wonDeals / $totalDeals) * 100 : 0;

        // Calculate close rate (same as opportunity conversion rate)
        $closeRate = $opportunityConversionRate;

        return [
            'overall_conversion_rate' => [
                'value' => number_format($overallConversionRate, 1) . '%',
                'change_percentage' => 0.8,
                'description' => 'Actual vs Target',
            ],
            'lead_conversion' => [
                'value' => number_format($leadConversionRate, 0) . '%',
                'change_percentage' => -10.0,
                'description' => 'Actual vs Target',
            ],
            'opportunity_conversion' => [
                'value' => number_format($opportunityConversionRate, 0) . '%',
                'change_percentage' => 2.0,
                'description' => 'Qualified vs Opportunity',
            ],
            'close_rate' => [
                'value' => number_format($closeRate, 0) . '%',
                'change_percentage' => -10.0,
                'description' => 'Opportunity vs Investment',
            ],
        ];
    }

    /**
     * Get conversion funnel data
     */
    private function getConversionFunnel($dealsData, $contactData): array
    {
        $totalContacts = $contactData->count();
        $leads = $totalContacts; // Assuming all contacts are leads
        $qualified = $dealsData->count();
        $opportunities = $dealsData->count();
        $customers = $dealsData->where('status', 'won')->count();

        // Mock visitors count (this would need to be tracked separately)
        $visitors = $totalContacts * 10; // Assuming 10:1 visitor to contact ratio

        return [
            ['stage' => 'Visitors', 'count' => $visitors],
            ['stage' => 'Leads', 'count' => $leads],
            ['stage' => 'Qualified', 'count' => $qualified],
            ['stage' => 'Opportunities', 'count' => $opportunities],
            ['stage' => 'Customers', 'count' => $customers],
        ];
    }

    /**
     * Get stage conversion rates data
     */
    private function getStageConversionRates($dealsData, $contactData): array
    {
        $totalContacts = $contactData->count();
        $totalDeals = $dealsData->count();
        $wonDeals = $dealsData->where('status', 'won')->count();

        // Mock visitors count
        $visitors = $totalContacts * 10;

        return [
            [
                'stage' => 'Visitor - Lead',
                'actual_rate' => $totalContacts > 0 ? round(($totalContacts / $visitors) * 100, 1) : 0,
                'target_rate' => 50.0,
            ],
            [
                'stage' => 'Lead - Qualified',
                'actual_rate' => $totalContacts > 0 ? round(($totalDeals / $totalContacts) * 100, 1) : 0,
                'target_rate' => 45.0,
            ],
            [
                'stage' => 'Qualified - Opportunity',
                'actual_rate' => 45.0, // Mock data
                'target_rate' => 40.0,
            ],
            [
                'stage' => 'Opportunity - Customer',
                'actual_rate' => $totalDeals > 0 ? round(($wonDeals / $totalDeals) * 100, 1) : 0,
                'target_rate' => 30.0,
            ],
        ];
    }

    /**
     * Get conversion trend data
     */
    private function getConversionTrend($data, $filters): array
    {
        // Group by month and calculate conversion rate
        $monthlyData = $data->groupBy(function ($deal) {
            return \Carbon\Carbon::parse($deal->created_at)->format('M');
        })->map(function ($monthData, $month) {
            $total = $monthData->count();
            $won = $monthData->where('status', 'won')->count();
            return $total > 0 ? ($won / $total) * 100 : 0;
        });

        // Generate last 6 months
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = \Carbon\Carbon::now()->subMonths($i)->format('M');
            $conversionRate = $monthlyData->get($month, 0);
            $targetRate = 2.4; // Mock target rate

            $months[] = [
                'month' => $month,
                'conversion_rate' => round($conversionRate, 1),
                'target_rate' => $targetRate,
            ];
        }

        return $months;
    }

    /**
     * Get conversion by source data
     */
    private function getConversionBySource($dealsData, $contactData): array
    {
        // This would need to be implemented based on your source tracking
        // For now, returning mock data based on common sources
        return [
            ['source' => 'Organic', 'conversions' => 100, 'rate_percentage' => 4.0],
            ['source' => 'Paid Ads', 'conversions' => 75, 'rate_percentage' => 3.0],
            ['source' => 'Referral', 'conversions' => 80, 'rate_percentage' => 4.0],
            ['source' => 'Social Media', 'conversions' => 60, 'rate_percentage' => 3.0],
            ['source' => 'Email', 'conversions' => 85, 'rate_percentage' => 4.0],
            ['source' => 'Direct', 'conversions' => 90, 'rate_percentage' => 4.5],
        ];
    }

    /**
     * Get time to conversion data
     */
    private function getTimeToConversion($data): array
    {
        // This would need to be implemented based on your conversion timing tracking
        // For now, returning mock data based on typical conversion timeframes
        return [
            ['timeframe' => '0-7 days', 'conversions' => 70],
            ['timeframe' => '8-14 days', 'conversions' => 120],
            ['timeframe' => '15-30 days', 'conversions' => 140],
            ['timeframe' => '31-60 days', 'conversions' => 100],
            ['timeframe' => '61-90 days', 'conversions' => 60],
            ['timeframe' => '90+ days', 'conversions' => 40],
        ];
    }

    /**
     * Get team performance data
     */
    private function getTeamPerformance($data): array
    {
        $teamPerformance = $data->groupBy(function ($deal) {
            return $deal->user_first_name . ' ' . $deal->user_last_name;
        })->map(function ($repData, $repName) {
            $total = $repData->count();
            $won = $repData->where('status', 'won')->count();
            $conversionRate = $total > 0 ? ($won / $total) * 100 : 0;

            return [
                'team_member' => $repName ?: 'Unassigned',
                'conversions' => $won,
                'rate_percentage' => round($conversionRate, 1),
            ];
        })->sortByDesc('conversions')->values()->take(5);

        return $teamPerformance->toArray();
    }

    /**
     * Get monthly conversion funnel data
     */
    private function getMonthlyConversionFunnel($dealsData, $contactData, $filters): array
    {
        // Group by month and calculate funnel stages
        $monthlyData = $dealsData->groupBy(function ($deal) {
            return \Carbon\Carbon::parse($deal->created_at)->format('M');
        })->map(function ($monthData, $month) {
            $opportunities = $monthData->count();
            $customers = $monthData->where('status', 'won')->count();
            $leads = $opportunities * 3; // Mock ratio

            return [
                'leads' => $leads,
                'opportunities' => $opportunities,
                'customers' => $customers,
            ];
        });

        // Generate last 6 months
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = \Carbon\Carbon::now()->subMonths($i)->format('M');
            $monthData = $monthlyData->get($month, [
                'leads' => 0,
                'opportunities' => 0,
                'customers' => 0
            ]);
            $months[] = [
                'month' => $month,
                'leads' => $monthData['leads'],
                'opportunities' => $monthData['opportunities'],
                'customers' => $monthData['customers'],
            ];
        }

        return $months;
    }

    /**
     * Get conversion by deal size data
     */
    private function getConversionByDealSize($data): array
    {
        $dealSizeRanges = [
            ['range' => '$0-5K', 'min' => 0, 'max' => 5000],
            ['range' => '$5-15K', 'min' => 5000, 'max' => 15000],
            ['range' => '$15-30K', 'min' => 15000, 'max' => 30000],
            ['range' => '$30-50K', 'min' => 30000, 'max' => 50000],
            ['range' => '$50K+', 'min' => 50000, 'max' => PHP_FLOAT_MAX],
        ];

        $distribution = [];
        foreach ($dealSizeRanges as $range) {
            $rangeData = $data->whereBetween('deal_value', [$range['min'], $range['max']]);
            $total = $rangeData->count();
            $won = $rangeData->where('status', 'won')->count();
            $conversionRate = $total > 0 ? ($won / $total) * 100 : 0;

            $distribution[] = [
                'range' => $range['range'],
                'opportunities' => $total,
                'conversion_rate' => round($conversionRate, 1),
            ];
        }

        return $distribution;
    }
}
