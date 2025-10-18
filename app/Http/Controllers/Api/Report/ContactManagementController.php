<?php

namespace App\Http\Controllers\Api\Report;

use App\DTO\Report\ContactManagementReportDTO;
use App\Http\Controllers\Controller;
use App\Services\Report\ReportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactManagementController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    /**
     * Get contact database analysis
     */
    public function contactAnalysis(Request $request): JsonResponse
    {
        try {
            $filters = ContactManagementReportDTO::fromRequest($request);
            $result = $this->reportService->executeContactManagementReport($filters);

            return ApiResponse([
                'data' => $result['data'],
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Contact database analysis generated successfully');
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
            $filters = ContactManagementReportDTO::fromRequest($request);
            $result = $this->reportService->executeContactManagementReport($filters);

            // Calculate engagement metrics
            $engagementData = $result['data']->groupBy('source_name')->map(function ($sourceData, $sourceName) {
                $totalContacts = $sourceData->count();
                $engagedContacts = $sourceData->where('last_activity_date', '!=', null)->count();
                $activeContacts = $sourceData->where('last_activity_date', '>=', now()->subDays(30))->count();

                return [
                    'source' => $sourceName,
                    'total_contacts' => $totalContacts,
                    'engaged_contacts' => $engagedContacts,
                    'active_contacts' => $activeContacts,
                    'engagement_rate' => $totalContacts > 0 ? ($engagedContacts / $totalContacts) * 100 : 0,
                    'activity_rate' => $totalContacts > 0 ? ($activeContacts / $totalContacts) * 100 : 0,
                ];
            })->values();

            return ApiResponse([
                'engagement_data' => $engagementData,
                'summary' => $result['summary'],
            ], 'Contact engagement metrics generated successfully');
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
            $filters = ContactManagementReportDTO::fromRequest($request);
            $result = $this->reportService->executeContactManagementReport($filters);

            $sourceData = $result['data']->groupBy('source_name')->map(function ($sourceData, $sourceName) use ($result) {
                return [
                    'source' => $sourceName,
                    'count' => $sourceData->count(),
                    'percentage' => $result['data']->count() > 0 ? ($sourceData->count() / $result['data']->count()) * 100 : 0,
                    'conversion_rate' => $sourceData->where('has_deals', true)->count() / max($sourceData->count(), 1) * 100,
                    'average_deal_value' => $sourceData->where('has_deals', true)->avg('total_deal_value'),
                ];
            })->values();

            return ApiResponse([
                'source_data' => $sourceData,
                'summary' => $result['summary'],
            ], 'Contact source analysis retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
