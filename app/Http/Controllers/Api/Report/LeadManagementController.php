<?php

namespace App\Http\Controllers\Api\Report;

use App\DTO\Report\LeadManagementReportDTO;
use App\Http\Controllers\Controller;
use App\Services\Report\ReportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadManagementController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    /**
     * Get lead generation report
     */
    public function leadGeneration(Request $request): JsonResponse
    {
        try {
            $filters = LeadManagementReportDTO::fromRequest($request);
            $result = $this->reportService->executeLeadManagementReport($filters);

            return ApiResponse([
                'data' => $result['data'],
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Lead generation report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get lead conversion report
     */
    public function leadConversion(Request $request): JsonResponse
    {
        try {
            $filters = LeadManagementReportDTO::fromRequest($request);
            $result = $this->reportService->executeLeadManagementReport($filters);

            // Calculate conversion metrics
            $conversionData = $result['data']->groupBy('source_name')->map(function ($sourceData, $sourceName) {
                $total = $sourceData->count();
                $converted = $sourceData->where('status', 'converted')->count();

                return [
                    'source' => $sourceName,
                    'total_leads' => $total,
                    'converted_leads' => $converted,
                    'conversion_rate' => $total > 0 ? ($converted / $total) * 100 : 0,
                    'total_value' => $sourceData->sum('deal_value'),
                ];
            })->values();

            return ApiResponse([
                'conversion_data' => $conversionData,
                'summary' => $result['summary'],
            ], 'Lead conversion report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get lead sources breakdown
     */
    public function leadSources(Request $request): JsonResponse
    {
        try {
            $filters = LeadManagementReportDTO::fromRequest($request);
            $result = $this->reportService->executeLeadManagementReport($filters);

            $sourcesData = $result['data']->groupBy('source_name')->map(function ($sourceData, $sourceName) use ($result) {
                return [
                    'source' => $sourceName,
                    'count' => $sourceData->count(),
                    'percentage' => ($sourceData->count() / $result['data']->count()) * 100,
                    'qualified_count' => $sourceData->where('is_qualifying', true)->count(),
                    'total_value' => $sourceData->sum('deal_value'),
                ];
            })->values();

            return ApiResponse([
                'sources_data' => $sourcesData,
                'summary' => $result['summary'],
            ], 'Lead sources breakdown retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get lead quality score distribution
     */
    public function leadQualityDistribution(Request $request): JsonResponse
    {
        try {
            $filters = LeadManagementReportDTO::fromRequest($request);
            $result = $this->reportService->executeLeadManagementReport($filters);

            // Group leads by quality score ranges
            $qualityData = $result['data']->groupBy(function ($lead) {
                $score = $lead->win_probability ?? 0;
                if ($score >= 80) return 'High (80-100%)';
                if ($score >= 60) return 'Medium-High (60-79%)';
                if ($score >= 40) return 'Medium (40-59%)';
                if ($score >= 20) return 'Low-Medium (20-39%)';
                return 'Low (0-19%)';
            })->map(function ($scoreData, $scoreRange) use ($result) {
                return [
                    'score_range' => $scoreRange,
                    'count' => $scoreData->count(),
                    'percentage' => ($scoreData->count() / $result['data']->count()) * 100,
                    'total_value' => $scoreData->sum('deal_value'),
                ];
            })->values();

            return ApiResponse([
                'quality_data' => $qualityData,
                'summary' => $result['summary'],
            ], 'Lead quality distribution retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get lead generation trend
     */
    public function leadGenerationTrend(Request $request): JsonResponse
    {
        try {
            $filters = LeadManagementReportDTO::fromRequest($request);
            $result = $this->reportService->executeLeadManagementReport($filters);

            // Group by month
            $trendData = $result['data']->groupBy(function ($lead) {
                return \Carbon\Carbon::parse($lead->created_at)->format('Y-m');
            })->map(function ($monthData, $month) {
                return [
                    'month' => $month,
                    'count' => $monthData->count(),
                    'qualified_count' => $monthData->where('is_qualifying', true)->count(),
                    'converted_count' => $monthData->where('status', 'converted')->count(),
                    'total_value' => $monthData->sum('deal_value'),
                ];
            })->values();

            return ApiResponse([
                'trend_data' => $trendData,
                'summary' => $result['summary'],
            ], 'Lead generation trend retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
