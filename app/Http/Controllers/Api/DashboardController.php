<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\DashboardRequest;
use App\Services\Tenant\DashboardService;

class DashboardController extends Controller
{
    public function __construct(
        public DashboardService $dashboardService
    ) {}

    public function getWidgets(DashboardRequest $request)
    {
        $filters = array_filter(
            $request->only('start_date', 'end_date', 'user_id', 'team_id'),
            function ($value) {
                return !is_null($value) && $value !== '';
            }
        );

        $widgets = $this->dashboardService->getWidgets($filters);
        return apiResponse(data: $widgets);
    }

    public function getOpportunitiesByStage(DashboardRequest $request)
    {
        $filters = array_filter(
            $request->only('start_date', 'end_date', 'user_id', 'team_id', 'pipeline_id'),
            function ($value) {
                return !is_null($value) && $value !== '';
            }
        );

        $stageCounts = $this->dashboardService->getOpportunitiesByStage($filters);
        return apiResponse(data: $stageCounts);
    }

    public function getSaleFunnel(DashboardRequest $request)
    {
        $filters = array_filter(
            $request->only('start_date', 'end_date', 'user_id', 'team_id', 'pipeline_id'),
            function ($value) {
                return !is_null($value) && $value !== '';
            }
        );

        $saleFunnel = $this->dashboardService->getSaleFunnel($filters);
        return apiResponse(data: $saleFunnel);
    }

    public function getTodayTasks(DashboardRequest $request)
    {
        $filters = array_filter(
            $request->only('user_id', 'team_id'),
            function ($value) {
                return !is_null($value) && $value !== '';
            }
        );
        $tasks = $this->dashboardService->getTodayTasks($filters);
        return apiResponse(data: $tasks);
    }

    public function getUserRecentActivities()
    {
        $activities = $this->dashboardService->getUserRecentActivities(
            user_id(),
            5
        );
        return apiResponse(data: $activities);
    }
}
