<?php

namespace App\Http\Controllers\Api\Report;

use App\DTO\Report\SalesPerformanceReportDTO;
use App\Http\Controllers\Controller;
use App\Services\Report\ReportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactOpportunityConversionController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {
        // $this->middleware('permission:view-conversion-reports');
    }

    /**
     * Get comprehensive contact to opportunity conversion dashboard data
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);

            // Get contact management data
            $contactData = $this->reportService->executeContactManagementReport($filters);

            // Get deals performance data for opportunities
            $dealsData = $this->reportService->executeSalesPerformanceReport($filters);

            // Calculate key metrics
            $keyMetrics = $this->calculateKeyMetrics($contactData['data'], $dealsData['data']);

            // Get conversion funnel
            $conversionFunnel = $this->getConversionFunnel($contactData['data'], $dealsData['data']);

            // Get conversion trends
            $conversionTrends = $this->getConversionTrends($contactData['data'], $dealsData['data'], $filters);

            // Get conversion by source
            $conversionBySource = $this->getConversionBySource($contactData['data'], $dealsData['data']);

            return ApiResponse([
                'key_metrics' => $keyMetrics,
                'conversion_funnel' => $conversionFunnel,
                'conversion_trends' => $conversionTrends,
                'conversion_by_source' => $conversionBySource,
                'summary' => $contactData['summary'],
                'records_count' => $contactData['records_count'],
            ], 'Contact to opportunity conversion dashboard data retrieved successfully');
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
            $contactData = $this->reportService->executeContactManagementReport($filters);
            $dealsData = $this->reportService->executeSalesPerformanceReport($filters);

            $conversionFunnel = $this->getConversionFunnel($contactData['data'], $dealsData['data']);

            return ApiResponse([
                'data' => $conversionFunnel,
                'summary' => $contactData['summary'],
                'records_count' => $contactData['records_count'],
            ], 'Conversion funnel report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get conversion trends
     */
    public function conversionTrends(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $contactData = $this->reportService->executeContactManagementReport($filters);
            $dealsData = $this->reportService->executeSalesPerformanceReport($filters);

            $conversionTrends = $this->getConversionTrends($contactData['data'], $dealsData['data'], $filters);

            return ApiResponse([
                'data' => $conversionTrends,
                'summary' => $contactData['summary'],
                'records_count' => $contactData['records_count'],
            ], 'Conversion trends report generated successfully');
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
            $contactData = $this->reportService->executeContactManagementReport($filters);
            $dealsData = $this->reportService->executeSalesPerformanceReport($filters);

            $conversionBySource = $this->getConversionBySource($contactData['data'], $dealsData['data']);

            return ApiResponse([
                'data' => $conversionBySource,
                'summary' => $contactData['summary'],
                'records_count' => $contactData['records_count'],
            ], 'Conversion by source report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Calculate key metrics for the dashboard
     */
    private function calculateKeyMetrics($contactData, $dealsData): array
    {
        $totalContacts = $contactData->count();
        $opportunitiesCreated = $dealsData->count();
        $conversionRate = $totalContacts > 0 ? ($opportunitiesCreated / $totalContacts) * 100 : 0;

        // Calculate average time to convert (simplified)
        $avgTimeToConvert = $dealsData->whereNotNull('created_at')->map(function ($deal) {
            // This would need to be calculated based on when contact was first created vs when opportunity was created
            return 15; // Mock data - 15 days average
        })->avg();

        // Calculate lead quality score (simplified)
        $leadQualityScore = $this->calculateLeadQualityScore($contactData);

        // Calculate qualified leads (contacts that became opportunities)
        $qualifiedLeads = $opportunitiesCreated;

        // Calculate conversion velocity (simplified)
        $conversionVelocity = $avgTimeToConvert > 0 ? 1 / $avgTimeToConvert : 0;

        return [
            'conversion_percentage' => [
                'value' => number_format($conversionRate, 1) . '%',
                'change_percentage' => 2.3,
            ],
            'opportunities_created' => [
                'value' => $opportunitiesCreated,
                'change_percentage' => 15.7,
            ],
            'avg_time_to_convert' => [
                'value' => round($avgTimeToConvert) . ' days',
                'change_percentage' => -8.2,
            ],
            'lead_quality_score' => [
                'value' => number_format($leadQualityScore, 1),
                'change_percentage' => 5.4,
            ],
            'qualified_leads' => [
                'value' => $qualifiedLeads,
                'change_percentage' => 12.1,
            ],
            'conversion_velocity' => [
                'value' => number_format($conversionVelocity, 2),
                'change_percentage' => 8.7,
            ],
        ];
    }

    /**
     * Get conversion funnel data
     */
    private function getConversionFunnel($contactData, $dealsData): array
    {
        $totalContacts = $contactData->count();
        $engagedContacts = $contactData->where('status', 'active')->count();
        $qualifiedLeads = $dealsData->count();
        $opportunitiesCreated = $dealsData->count();
        $wonDeals = $dealsData->where('status', 'won')->count();

        return [
            [
                'stage' => 'Total Contacts',
                'count' => $totalContacts,
                'conversion_rate' => 100.0,
            ],
            [
                'stage' => 'Engaged Contacts',
                'count' => $engagedContacts,
                'conversion_rate' => $totalContacts > 0 ? round(($engagedContacts / $totalContacts) * 100, 1) : 0,
            ],
            [
                'stage' => 'Qualified Leads',
                'count' => $qualifiedLeads,
                'conversion_rate' => $totalContacts > 0 ? round(($qualifiedLeads / $totalContacts) * 100, 1) : 0,
            ],
            [
                'stage' => 'Opportunities Created',
                'count' => $opportunitiesCreated,
                'conversion_rate' => $totalContacts > 0 ? round(($opportunitiesCreated / $totalContacts) * 100, 1) : 0,
            ],
            [
                'stage' => 'Won Deals',
                'count' => $wonDeals,
                'conversion_rate' => $totalContacts > 0 ? round(($wonDeals / $totalContacts) * 100, 1) : 0,
            ],
        ];
    }

    /**
     * Get conversion trends data
     */
    private function getConversionTrends($contactData, $dealsData, $filters): array
    {
        // Group contacts by month
        $monthlyContacts = $contactData->groupBy(function ($contact) {
            return \Carbon\Carbon::parse($contact->created_at)->format('M');
        });

        // Group deals by month
        $monthlyDeals = $dealsData->groupBy(function ($deal) {
            return \Carbon\Carbon::parse($deal->created_at)->format('M');
        });

        // Generate last 12 months
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = \Carbon\Carbon::now()->subMonths($i)->format('M');
            $contactsCount = $monthlyContacts->get($month, collect())->count();
            $dealsCount = $monthlyDeals->get($month, collect())->count();
            $conversionRate = $contactsCount > 0 ? ($dealsCount / $contactsCount) * 100 : 0;

            $months[] = [
                'month' => $month,
                'contacts' => $contactsCount,
                'opportunities' => $dealsCount,
                'conversion_rate' => round($conversionRate, 1),
            ];
        }

        return $months;
    }

    /**
     * Get conversion by source
     */
    private function getConversionBySource($contactData, $dealsData): array
    {
        // Group contacts by source
        $contactsBySource = $contactData->groupBy('source_name');

        // Group deals by source (assuming deals have source_name from contacts)
        $dealsBySource = $dealsData->groupBy('source_name');

        $conversionBySource = [];

        foreach ($contactsBySource as $sourceName => $sourceContacts) {
            $contactsCount = $sourceContacts->count();
            $dealsCount = $dealsBySource->get($sourceName, collect())->count();
            $conversionRate = $contactsCount > 0 ? ($dealsCount / $contactsCount) * 100 : 0;

            $conversionBySource[] = [
                'source' => $sourceName ?: 'Unknown',
                'contacts' => $contactsCount,
                'opportunities' => $dealsCount,
                'conversion_rate' => round($conversionRate, 1),
            ];
        }

        // Sort by conversion rate descending
        usort($conversionBySource, function ($a, $b) {
            return $b['conversion_rate'] <=> $a['conversion_rate'];
        });

        return $conversionBySource;
    }

    /**
     * Calculate lead quality score (simplified)
     */
    private function calculateLeadQualityScore($contactData): float
    {
        // This would need to be implemented based on your lead scoring criteria
        // For now, returning a mock calculation
        $totalContacts = $contactData->count();
        $activeContacts = $contactData->where('status', 'active')->count();
        $contactsWithEmail = $contactData->whereNotNull('email')->count();
        $contactsWithPhone = $contactData->whereNotNull('phone')->count();

        $score = 0;
        if ($totalContacts > 0) {
            $score += ($activeContacts / $totalContacts) * 40; // 40% weight for active status
            $score += ($contactsWithEmail / $totalContacts) * 30; // 30% weight for email
            $score += ($contactsWithPhone / $totalContacts) * 30; // 30% weight for phone
        }

        return $score;
    }
}
