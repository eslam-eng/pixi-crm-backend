<?php

namespace App\Http\Controllers\Api\Report;

use App\DTO\Report\CallActivityReportDTO;
use App\Http\Controllers\Controller;
use App\Services\Report\ReportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CallActivityController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    /**
     * Get call log analysis
     */
    public function callLogAnalysis(Request $request): JsonResponse
    {
        try {
            $filters = CallActivityReportDTO::fromRequest($request);
            $result = $this->reportService->executeCallActivityReport($filters);

            return ApiResponse([
                'data' => $result['data'],
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Call log analysis generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get call recording analysis
     */
    public function callRecordingAnalysis(Request $request): JsonResponse
    {
        try {
            $filters = CallActivityReportDTO::fromRequest($request);
            $result = $this->reportService->executeCallActivityReport($filters);

            // Analyze call recordings
            $recordingData = $result['data']->groupBy('call_type')->map(function ($typeData, $callType) {
                return [
                    'call_type' => $callType,
                    'total_calls' => $typeData->count(),
                    'average_duration' => $typeData->avg('duration'),
                    'total_duration' => $typeData->sum('duration'),
                    'success_rate' => $typeData->where('status', 'successful')->count() / max($typeData->count(), 1) * 100,
                ];
            })->values();

            return ApiResponse([
                'recording_data' => $recordingData,
                'summary' => $result['summary'],
            ], 'Call recording analysis generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get call volume trends
     */
    public function callVolumeTrends(Request $request): JsonResponse
    {
        try {
            $filters = CallActivityReportDTO::fromRequest($request);
            $result = $this->reportService->executeCallActivityReport($filters);

            // Group by day for trend analysis
            $trendData = $result['data']->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->created_at)->format('Y-m-d');
            })->map(function ($dayData, $date) {
                return [
                    'date' => $date,
                    'total_calls' => $dayData->count(),
                    'inbound_calls' => $dayData->where('direction', 'inbound')->count(),
                    'outbound_calls' => $dayData->where('direction', 'outbound')->count(),
                    'average_duration' => $dayData->avg('duration'),
                ];
            })->values();

            return ApiResponse([
                'trend_data' => $trendData,
                'summary' => $result['summary'],
            ], 'Call volume trends retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
