<?php

namespace App\Http\Controllers\Api\Report;

use App\DTO\Report\SalesPerformanceReportDTO;
use App\Http\Controllers\Controller;
use App\Services\Tenant\Report\ReportService;
use App\Services\Tenant\Report\TeamPerformanceService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamPerformanceController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService,
        private readonly TeamPerformanceService $teamPerformanceService
    ) {
        // $this->middleware('permission:view-team-performance-reports');
    }

    /**
     * Get comprehensive team performance dashboard data
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);

            // Get sales performance data
            $dealsData = $this->reportService->executeSalesPerformanceReport($filters);
            $revenueData = $this->reportService->executeRevenueAnalysisReport($filters);

            // Calculate KPIs
            $kpis = $this->teamPerformanceService->calculateKPIs($dealsData['data'], $revenueData['data']);

            // Get individual performance
            $individualPerformance = $this->teamPerformanceService->getIndividualPerformance($dealsData['data'], $revenueData['data']);

            // Get win rate by team member
            $winRateByTeamMember = $this->teamPerformanceService->getWinRateByTeamMember($dealsData['data']);

            // Get activity distribution
            $activityDistribution = $this->teamPerformanceService->getActivityDistribution($filters);

            // Get team activity trend
            $teamActivityTrend = $this->teamPerformanceService->getTeamActivityTrend($filters);

            // Get quota attainment trend
            $quotaAttainmentTrend = $this->teamPerformanceService->getQuotaAttainmentTrend($dealsData['data'], $filters);

            // Get monthly revenue by team member
            $monthlyRevenueByTeamMember = $this->teamPerformanceService->getMonthlyRevenueByTeamMember($revenueData['data'], $filters);

            // Get pipeline contribution
            $pipelineContribution = $this->teamPerformanceService->getPipelineContribution($dealsData['data']);

            // Get team skills assessment (placeholder - would need actual skills data)
            $teamSkillsAssessment = $this->teamPerformanceService->getTeamSkillsAssessment($dealsData['data']);

            return apiResponse([
                'kpis' => $kpis,
                'individual_performance' => $individualPerformance,
                'win_rate_by_team_member' => $winRateByTeamMember,
                'activity_distribution' => $activityDistribution,
                'team_activity_trend' => $teamActivityTrend,
                'quota_attainment_trend' => $quotaAttainmentTrend,
                'monthly_revenue_by_team_member' => $monthlyRevenueByTeamMember,
                'pipeline_contribution' => $pipelineContribution,
                'team_skills_assessment' => $teamSkillsAssessment,
                'summary' => $dealsData['summary'],
                'records_count' => $dealsData['records_count'],
            ], 'Team performance dashboard data retrieved successfully');
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get KPIs data
     */
    public function kpis(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);

            $dealsData = $this->reportService->executeSalesPerformanceReport($filters);

            $revenueData = $this->reportService->executeRevenueAnalysisReport($filters);

            $kpis = $this->teamPerformanceService->calculateKPIs($dealsData['data'], $revenueData['data']);

            return apiResponse($kpis, 'KPIs retrieved successfully');
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get individual performance data
     */
    public function individualPerformance(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $dealsData = $this->reportService->executeSalesPerformanceReport($filters);
            $revenueData = $this->reportService->executeRevenueAnalysisReport($filters);

            $individualPerformance = $this->teamPerformanceService->getIndividualPerformance($dealsData['data'], $revenueData['data']);

            return apiResponse($individualPerformance, 'Individual performance data retrieved successfully');
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get win rate by team member
     */
    public function winRateByTeamMember(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $dealsData = $this->reportService->executeSalesPerformanceReport($filters);

            $winRateByTeamMember = $this->teamPerformanceService->getWinRateByTeamMember($dealsData['data']);

            return apiResponse($winRateByTeamMember, 'Win rate by team member retrieved successfully');
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get activity distribution
     */
    public function activityDistribution(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);

            $activityDistribution = $this->teamPerformanceService->getActivityDistribution($filters);

            return apiResponse($activityDistribution, 'Activity distribution retrieved successfully');
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get team activity trend
     */
    public function teamActivityTrend(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);

            $teamActivityTrend = $this->teamPerformanceService->getTeamActivityTrend($filters);

            return apiResponse($teamActivityTrend, 'Team activity trend retrieved successfully');
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get quota attainment trend
     */
    public function quotaAttainmentTrend(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $dealsData = $this->reportService->executeSalesPerformanceReport($filters);

            $quotaAttainmentTrend = $this->teamPerformanceService->getQuotaAttainmentTrend($dealsData['data'], $filters);

            return apiResponse($quotaAttainmentTrend, 'Quota attainment trend retrieved successfully');
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get monthly revenue by team member
     */
    public function monthlyRevenueByTeamMember(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $revenueData = $this->reportService->executeRevenueAnalysisReport($filters);

            $monthlyRevenueByTeamMember = $this->teamPerformanceService->getMonthlyRevenueByTeamMember($revenueData['data'], $filters);

            return apiResponse($monthlyRevenueByTeamMember, 'Monthly revenue by team member retrieved successfully');
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get pipeline contribution
     */
    public function pipelineContribution(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $dealsData = $this->reportService->executeSalesPerformanceReport($filters);

            $pipelineContribution = $this->teamPerformanceService->getPipelineContribution($dealsData['data']);

            return apiResponse($pipelineContribution, 'Pipeline contribution retrieved successfully');
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get team skills assessment
     */
    public function teamSkillsAssessment(Request $request): JsonResponse
    {
        try {
            $filters = SalesPerformanceReportDTO::fromRequest($request);
            $dealsData = $this->reportService->executeSalesPerformanceReport($filters);

            $teamSkillsAssessment = $this->teamPerformanceService->getTeamSkillsAssessment($dealsData['data']);

            return apiResponse($teamSkillsAssessment, 'Team skills assessment retrieved successfully');
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
