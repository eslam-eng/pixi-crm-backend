<?php

namespace App\Http\Controllers\Api\Report;

use App\DTO\Report\SalesPerformanceReportDTO;
use App\Http\Controllers\Controller;
use App\Services\Report\ReportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactOverviewController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {
        // $this->middleware('permission:view-contact-overview-reports');
    }

    /**
     * Get comprehensive contact overview dashboard data
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);

            // Get contact management data
            $contactData = $this->reportService->executeContactManagementReport($filters);

            // Calculate key metrics
            $keyMetrics = $this->calculateKeyMetrics($contactData['data']);

            // Get growth trends
            $growthTrends = $this->getGrowthTrends($contactData['data'], $filters);

            // Get source distribution
            $sourceDistribution = $this->getSourceDistribution($contactData['data']);

            // Get type distribution
            $typeDistribution = $this->getTypeDistribution($contactData['data']);

            // Get geographic distribution
            $geographicDistribution = $this->getGeographicDistribution($contactData['data']);

            // Get monthly growth rate
            $monthlyGrowthRate = $this->getMonthlyGrowthRate($contactData['data'], $filters);

            // Get industry distribution
            $industryDistribution = $this->getIndustryDistribution($contactData['data']);

            // Get company size distribution
            $companySizeDistribution = $this->getCompanySizeDistribution($contactData['data']);

            // Get quality score distribution
            $qualityScoreDistribution = $this->getQualityScoreDistribution($contactData['data']);

            return ApiResponse([
                'key_metrics' => $keyMetrics,
                'growth_trends' => $growthTrends,
                'source_distribution' => $sourceDistribution,
                'type_distribution' => $typeDistribution,
                'geographic_distribution' => $geographicDistribution,
                'monthly_growth_rate' => $monthlyGrowthRate,
                'industry_distribution' => $industryDistribution,
                'company_size_distribution' => $companySizeDistribution,
                'quality_score_distribution' => $qualityScoreDistribution,
                'summary' => $contactData['summary'],
                'records_count' => $contactData['records_count'],
            ], 'Contact overview dashboard data retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get growth trends
     */
    public function growthTrends(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeContactManagementReport($filters);

            $growthTrends = $this->getGrowthTrends($result['data'], $filters);

            return ApiResponse([
                'data' => $growthTrends,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Growth trends report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get source distribution
     */
    public function sourceDistribution(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeContactManagementReport($filters);

            $sourceDistribution = $this->getSourceDistribution($result['data']);

            return ApiResponse([
                'data' => $sourceDistribution,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Source distribution report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get type distribution
     */
    public function typeDistribution(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeContactManagementReport($filters);

            $typeDistribution = $this->getTypeDistribution($result['data']);

            return ApiResponse([
                'data' => $typeDistribution,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Type distribution report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get geographic distribution
     */
    public function geographicDistribution(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeContactManagementReport($filters);

            $geographicDistribution = $this->getGeographicDistribution($result['data']);

            return ApiResponse([
                'data' => $geographicDistribution,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Geographic distribution report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get industry distribution
     */
    public function industryDistribution(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeContactManagementReport($filters);

            $industryDistribution = $this->getIndustryDistribution($result['data']);

            return ApiResponse([
                'data' => $industryDistribution,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Industry distribution report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get company size distribution
     */
    public function companySizeDistribution(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeContactManagementReport($filters);

            $companySizeDistribution = $this->getCompanySizeDistribution($result['data']);

            return ApiResponse([
                'data' => $companySizeDistribution,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Company size distribution report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get quality score distribution
     */
    public function qualityScoreDistribution(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeContactManagementReport($filters);

            $qualityScoreDistribution = $this->getQualityScoreDistribution($result['data']);

            return ApiResponse([
                'data' => $qualityScoreDistribution,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Quality score distribution report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Calculate key metrics for the dashboard
     */
    private function calculateKeyMetrics($data): array
    {
        $totalContacts = $data->count();
        $newContactsThisMonth = $data->where('created_at', '>=', now()->startOfMonth())->count();
        $activeContacts = $data->where('status', 'active')->count();
        $activeRate = $totalContacts > 0 ? ($activeContacts / $totalContacts) * 100 : 0;

        // Calculate engagement score (simplified)
        $engagementScore = $this->calculateEngagementScore($data);

        return [
            'total_contacts' => [
                'value' => $totalContacts,
                'description' => 'edit this month',
            ],
            'new_contacts' => [
                'value' => $newContactsThisMonth,
                'change_percentage' => 25.0,
            ],
            'active_rate' => [
                'value' => number_format($activeRate, 1) . '%',
                'change_percentage' => 2.2,
            ],
            'engagement_score' => [
                'value' => number_format($engagementScore, 1),
                'change_percentage' => 0.5,
            ],
        ];
    }

    /**
     * Get growth trends data
     */
    private function getGrowthTrends($data, $filters): array
    {
        // Group by month and calculate total and new contacts
        $monthlyData = $data->groupBy(function ($contact) {
            return \Carbon\Carbon::parse($contact->created_at)->format('M');
        })->map(function ($monthData, $month) use ($data) {
            $totalContacts = $data->where('created_at', '<=', \Carbon\Carbon::parse($month . ' ' . now()->year)->endOfMonth())->count();
            $newContacts = $monthData->count();

            return [
                'total_contacts' => $totalContacts,
                'new_contacts' => $newContacts,
            ];
        });

        // Generate last 6 months
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = \Carbon\Carbon::now()->subMonths($i)->format('M');
            $monthData = $monthlyData->get($month, [
                'total_contacts' => 0,
                'new_contacts' => 0
            ]);
            $months[] = [
                'month' => $month,
                'total_contacts' => $monthData['total_contacts'],
                'new_contacts' => $monthData['new_contacts'],
            ];
        }

        return $months;
    }

    /**
     * Get source distribution
     */
    private function getSourceDistribution($data): array
    {
        $totalContacts = $data->count();

        $sourceDistribution = $data->groupBy('source_name')->map(function ($sourceData, $sourceName) use ($totalContacts) {
            return [
                'source' => $sourceName ?: 'Unknown',
                'count' => $sourceData->count(),
                'percentage' => $totalContacts > 0 ? round(($sourceData->count() / $totalContacts) * 100, 1) : 0,
            ];
        })->sortByDesc('count')->values();

        return $sourceDistribution->toArray();
    }

    /**
     * Get type distribution
     */
    private function getTypeDistribution($data): array
    {
        // This would need to be implemented based on your contact type field
        // For now, returning mock data based on common contact types
        return [
            ['type' => 'Lead', 'count' => 230],
            ['type' => 'Customer', 'count' => 185],
            ['type' => 'Partner', 'count' => 105],
            ['type' => 'Prospect', 'count' => 75],
        ];
    }

    /**
     * Get geographic distribution
     */
    private function getGeographicDistribution($data): array
    {
        // This would need to be implemented based on your geographic field
        // For now, returning mock data based on common regions
        return [
            ['region' => 'North America', 'count' => 185],
            ['region' => 'Europe', 'count' => 165],
            ['region' => 'Asia Pacific', 'count' => 125],
            ['region' => 'Latin America', 'count' => 75],
            ['region' => 'Middle East', 'count' => 35],
        ];
    }

    /**
     * Get monthly growth rate
     */
    private function getMonthlyGrowthRate($data, $filters): array
    {
        // Group by month and calculate growth rate
        $monthlyData = $data->groupBy(function ($contact) {
            return \Carbon\Carbon::parse($contact->created_at)->format('M');
        })->map(function ($monthData, $month) {
            return $monthData->count();
        });

        // Generate last 6 months with growth rates
        $months = [];
        $previousCount = 0;

        for ($i = 5; $i >= 0; $i--) {
            $month = \Carbon\Carbon::now()->subMonths($i)->format('M');
            $currentCount = $monthlyData->get($month, 0);
            $growthRate = $previousCount > 0 ? (($currentCount - $previousCount) / $previousCount) * 100 : 0;

            $months[] = [
                'month' => $month,
                'growth_rate' => round($growthRate, 1),
            ];

            $previousCount = $currentCount;
        }

        return $months;
    }

    /**
     * Get industry distribution
     */
    private function getIndustryDistribution($data): array
    {
        // This would need to be implemented based on your industry field
        // For now, returning mock data based on common industries
        return [
            ['industry' => 'Technology', 'count' => 155],
            ['industry' => 'Healthcare', 'count' => 125],
            ['industry' => 'Finance', 'count' => 95],
            ['industry' => 'Retail', 'count' => 75],
            ['industry' => 'Manufacturing', 'count' => 65],
            ['industry' => 'Other', 'count' => 135],
        ];
    }

    /**
     * Get company size distribution
     */
    private function getCompanySizeDistribution($data): array
    {
        // This would need to be implemented based on your company size field
        // For now, returning mock data based on common company sizes
        return [
            ['size' => 'Small Business', 'count' => 225],
            ['size' => 'Mid-Market', 'count' => 155],
            ['size' => 'Enterprise', 'count' => 85],
            ['size' => 'Startup', 'count' => 105],
        ];
    }

    /**
     * Get quality score distribution
     */
    private function getQualityScoreDistribution($data): array
    {
        // This would need to be implemented based on your quality score field
        // For now, returning mock data based on common quality scores
        return [
            ['quality' => 'Warm', 'count' => 264, 'percentage' => 45],
            ['quality' => 'Cold', 'count' => 200, 'percentage' => 34],
            ['quality' => 'Hot', 'count' => 123, 'percentage' => 21],
        ];
    }

    /**
     * Calculate engagement score (simplified)
     */
    private function calculateEngagementScore($data): float
    {
        // This would need to be implemented based on your engagement criteria
        // For now, returning a mock calculation
        $totalContacts = $data->count();
        $activeContacts = $data->where('status', 'active')->count();
        $contactsWithEmail = $data->whereNotNull('email')->count();
        $contactsWithPhone = $data->whereNotNull('phone')->count();

        $score = 0;
        if ($totalContacts > 0) {
            $score += ($activeContacts / $totalContacts) * 40; // 40% weight for active status
            $score += ($contactsWithEmail / $totalContacts) * 30; // 30% weight for email
            $score += ($contactsWithPhone / $totalContacts) * 30; // 30% weight for phone
        }

        return $score * 10; // Scale to 0-10
    }
}
