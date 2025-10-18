<?php

namespace App\Http\Controllers\Api\Report;

use App\DTO\Report\TeamPerformanceReportDTO;
use App\Http\Controllers\Controller;
use App\Services\Report\ReportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamPerformanceController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    /**
     * Get individual performance report
     */
    public function individualPerformance(Request $request): JsonResponse
    {
        try {
            $filters = TeamPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeTeamPerformanceReport($filters);

            return ApiResponse([
                'data' => $result['data'],
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Individual performance report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get team performance report
     */
    public function teamPerformance(Request $request): JsonResponse
    {
        try {
            $filters = TeamPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeTeamPerformanceReport($filters);

            return ApiResponse([
                'data' => $result['data'],
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Team performance report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get target vs achievement report
     */
    public function targetVsAchievement(Request $request): JsonResponse
    {
        try {
            $filters = TeamPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeTeamPerformanceReport($filters);

            // Calculate target vs achievement metrics
            $targetData = $result['data']->map(function ($teamData) {
                // This would need to integrate with your target system
                $target = 0; // Get from chair targets
                $achieved = $teamData->total_revenue ?? 0;

                return [
                    'team_name' => $teamData->team_name,
                    'target' => $target,
                    'achieved' => $achieved,
                    'achievement_percentage' => $target > 0 ? ($achieved / $target) * 100 : 0,
                    'gap_to_target' => $target - $achieved,
                ];
            });

            return ApiResponse([
                'target_data' => $targetData,
                'summary' => $result['summary'],
            ], 'Target vs achievement report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get team leaderboard
     */
    public function teamLeaderboard(Request $request): JsonResponse
    {
        try {
            $filters = TeamPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeTeamPerformanceReport($filters);

            $leaderboard = $result['data']->sortByDesc('total_revenue')->values()->map(function ($teamData, $index) {
                return [
                    'rank' => $index + 1,
                    'team_name' => $teamData->team_name,
                    'total_revenue' => $teamData->total_revenue,
                    'total_deals' => $teamData->total_deals,
                    'average_deal_size' => $teamData->avg_deal_size,
                    'team_size' => $teamData->team_size,
                ];
            });

            return ApiResponse([
                'leaderboard' => $leaderboard,
                'summary' => $result['summary'],
            ], 'Team leaderboard retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get team productivity metrics
     */
    public function teamProductivity(Request $request): JsonResponse
    {
        try {
            $filters = TeamPerformanceReportDTO::fromRequest($request);
            $result = $this->reportService->executeTeamPerformanceReport($filters);

            $productivityData = $result['data']->map(function ($teamData) {
                $teamSize = $teamData->team_size ?? 1;
                $teamSize = max($teamSize, 1); // Ensure minimum team size of 1

                return [
                    'team_name' => $teamData->team_name,
                    'revenue_per_member' => $teamData->total_revenue / $teamSize,
                    'deals_per_member' => $teamData->total_deals / $teamSize,
                    'pipeline_per_member' => $teamData->total_pipeline_value / $teamSize,
                    'team_size' => $teamSize,
                    'total_revenue' => $teamData->total_revenue,
                    'total_deals' => $teamData->total_deals,
                ];
            });

            return ApiResponse([
                'productivity_data' => $productivityData,
                'summary' => $result['summary'],
            ], 'Team productivity metrics retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
