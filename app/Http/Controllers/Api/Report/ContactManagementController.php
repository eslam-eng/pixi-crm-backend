<?php

namespace App\Http\Controllers\Api\Report;

use App\DTO\Report\SalesPerformanceReportDTO;
use App\Http\Controllers\Controller;
use App\Services\Tenant\Report\ReportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactManagementController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {
        // $this->middleware('permission:view-contact-reports');
    }

    /**
     * Get comprehensive contact management dashboard data
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);

            // Get contact management data
            $contactData = $this->reportService->executeContactManagementReport($filters);

            // Calculate key metrics
            $keyMetrics = $this->calculateKeyMetrics($contactData['data']);

            // Get contacts over time
            $contactsOverTime = $this->getContactsOverTime($contactData['data'], $filters);

            // Get contact status distribution
            $contactStatusDistribution = $this->getContactStatusDistribution($contactData['data']);

            // Get contacts by source
            $contactsBySource = $this->getContactsBySource($contactData['data']);

            // Get contact engagement levels
            $contactEngagementLevels = $this->getContactEngagementLevels($contactData['data']);

            // Get monthly growth rate
            $monthlyGrowthRate = $this->getMonthlyGrowthRate($contactData['data'], $filters);

            // Get lead conversion funnel
            $leadConversionFunnel = $this->getLeadConversionFunnel($contactData['data']);

            // Get communication methods
            $communicationMethods = $this->getCommunicationMethods($contactData['data']);

            // Get additional KPIs
            $additionalKPIs = $this->getAdditionalKPIs($contactData['data']);

            return ApiResponse([
                'key_metrics' => $keyMetrics,
                'contacts_over_time' => $contactsOverTime,
                'contact_status_distribution' => $contactStatusDistribution,
                'contacts_by_source' => $contactsBySource,
                'contact_engagement_levels' => $contactEngagementLevels,
                'monthly_growth_rate' => $monthlyGrowthRate,
                'lead_conversion_funnel' => $leadConversionFunnel,
                'communication_methods' => $communicationMethods,
                'additional_kpis' => $additionalKPIs,
                'summary' => $contactData['summary'],
                'records_count' => $contactData['records_count'],
            ], 'Contact management dashboard data retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get contact analysis
     */
    public function contactAnalysis(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeContactManagementReport($filters);

            return ApiResponse([
                'data' => $result['data'],
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Contact analysis report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get contact engagement metrics
     */
    public function contactEngagementMetrics(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeContactManagementReport($filters);

            $engagementLevels = $this->getContactEngagementLevels($result['data']);
            $communicationMethods = $this->getCommunicationMethods($result['data']);

            return ApiResponse([
                'engagement_levels' => $engagementLevels,
                'communication_methods' => $communicationMethods,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Contact engagement metrics report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get contact source analysis
     */
    public function contactSourceAnalysis(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeContactManagementReport($filters);

            $contactsBySource = $this->getContactsBySource($result['data']);

            return ApiResponse([
                'contacts_by_source' => $contactsBySource,
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Contact source analysis report generated successfully');
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

        // Calculate growth rate (simplified)
        $lastMonthContacts = $data->where('created_at', '>=', now()->subMonth()->startOfMonth())
            ->where('created_at', '<', now()->startOfMonth())->count();
        $growthRate = $lastMonthContacts > 0 ? (($newContactsThisMonth - $lastMonthContacts) / $lastMonthContacts) * 100 : 0;

        return [
            'total_contacts' => [
                'value' => $totalContacts,
                'description' => 'View more records',
            ],
            'new_contacts' => [
                'value' => $newContactsThisMonth,
                'description' => 'This month',
            ],
            'active_contacts' => [
                'value' => $activeContacts,
                'description' => 'View % of total',
            ],
            'growth_rate' => [
                'value' => number_format($growthRate, 0) . '%',
                'description' => 'Monthly average',
            ],
        ];
    }

    /**
     * Get contacts over time data
     */
    private function getContactsOverTime($data, $filters): array
    {
        // Group by month and calculate total, new, and active contacts
        $monthlyData = $data->groupBy(function ($contact) {
            return \Carbon\Carbon::parse($contact->created_at)->format('M');
        })->map(function ($monthData, $month) use ($data) {
            $totalContacts = $data->where('created_at', '<=', \Carbon\Carbon::parse($month . ' ' . now()->year)->endOfMonth())->count();
            $newContacts = $monthData->count();
            $activeContacts = $monthData->where('status', 'active')->count();

            return [
                'total_contacts' => $totalContacts,
                'new_contacts' => $newContacts,
                'active_contacts' => $activeContacts,
            ];
        });

        // Generate last 6 months
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = \Carbon\Carbon::now()->subMonths($i)->format('M');
            $monthData = $monthlyData->get($month, [
                'total_contacts' => 0,
                'new_contacts' => 0,
                'active_contacts' => 0
            ]);
            $months[] = [
                'month' => $month,
                'total_contacts' => $monthData['total_contacts'],
                'new_contacts' => $monthData['new_contacts'],
                'active_contacts' => $monthData['active_contacts'],
            ];
        }

        return $months;
    }

    /**
     * Get contact status distribution
     */
    private function getContactStatusDistribution($data): array
    {
        $statusDistribution = $data->groupBy('status')->map(function ($statusData, $status) {
            return [
                'status' => ucfirst($status ?: 'Unknown'),
                'count' => $statusData->count(),
            ];
        })->values();

        return $statusDistribution->toArray();
    }

    /**
     * Get contacts by source
     */
    private function getContactsBySource($data): array
    {
        $totalContacts = $data->count();

        $contactsBySource = $data->groupBy('source_name')->map(function ($sourceData, $sourceName) use ($totalContacts) {
            return [
                'source' => $sourceName ?: 'Unknown',
                'count' => $sourceData->count(),
                'percentage' => $totalContacts > 0 ? round(($sourceData->count() / $totalContacts) * 100, 1) : 0,
            ];
        })->sortByDesc('count')->values();

        return $contactsBySource->toArray();
    }

    /**
     * Get contact engagement levels
     */
    private function getContactEngagementLevels($data): array
    {
        // This would need to be implemented based on your engagement tracking
        // For now, returning mock data based on common engagement levels
        return [
            ['level' => 'High', 'count' => 220],
            ['level' => 'Medium', 'count' => 420],
            ['level' => 'Low', 'count' => 100],
            ['level' => 'None', 'count' => 180],
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
     * Get lead conversion funnel
     */
    private function getLeadConversionFunnel($data): array
    {
        // This would need to be implemented based on your conversion tracking
        // For now, returning mock data based on typical conversion funnel
        $totalContacts = $data->count();

        return [
            ['stage' => 'Total Contacts', 'count' => $totalContacts],
            ['stage' => 'Qualified Leads', 'count' => round($totalContacts * 0.87)],
            ['stage' => 'Opportunities', 'count' => round($totalContacts * 0.46)],
            ['stage' => 'Proposals', 'count' => round($totalContacts * 0.26)],
            ['stage' => 'Customers', 'count' => round($totalContacts * 0.10)],
        ];
    }

    /**
     * Get communication methods
     */
    private function getCommunicationMethods($data): array
    {
        // This would need to be implemented based on your communication tracking
        // For now, returning mock data based on common communication methods
        return [
            ['method' => 'Email', 'frequency' => 1250],
            ['method' => 'Phone', 'frequency' => 950],
            ['method' => 'SMS', 'frequency' => 400],
            ['method' => 'Meeting', 'frequency' => 600],
        ];
    }

    /**
     * Get additional KPIs
     */
    private function getAdditionalKPIs($data): array
    {
        // This would need to be implemented based on your KPI tracking
        // For now, returning mock data
        return [
            'avg_response_time' => [
                'value' => '4.2 hrs',
                'description' => 'This month',
            ],
            'email_open_rate' => [
                'value' => '42.5%',
                'change' => '+2.5% from last month',
            ],
            'call_connect_rate' => [
                'value' => '68%',
                'change' => '-1.5% from last month',
            ],
            'conversion_rate' => [
                'value' => '9.7%',
                'change' => '+1.2% from last month',
            ],
        ];
    }
}
