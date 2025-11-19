<?php

namespace App\Http\Controllers\Api;

use App\Enums\PermissionsEnum;
use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\DashboardRequest;
use App\Http\Resources\TeamDDLResource;
use App\Http\Resources\Tenant\Users\UserDDLResource;
use App\Services\TeamService;
use App\Services\Tenant\DashboardService;
use App\Services\Tenant\Users\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

class DashboardController extends Controller
{

    public function __construct(
        public DashboardService $dashboardService,
        public UserService $userService,
        public TeamService $teamService
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

    public function getfilterUsers(): JsonResponse
    {
        try {
            $filters = array_filter(request()->query());
            /** @var \App\Models\Tenant\User|null $user */
            $user = auth('api_tenant')->user();

            if ($user && method_exists($user, 'can') && $user->can(PermissionsEnum::VIEW_ADMIN_DASHBOARD->value)) {
                $users = $this->userService->index(filters: $filters);
            } elseif ($user && method_exists($user, 'can') && $user->can(PermissionsEnum::VIEW_MANAGER_DASHBOARD->value)) {
                if (!$user->team_id) {
                    throw new GeneralException(__('app.manager_must_have_team'));
                }
                $filters['team_id'] = $user->team_id;
                $users = $this->userService->index(filters: $filters);
            } else {
                $users = collect([]);
            }
            $data = UserDDLResource::collection($users);
            return ApiResponse($data, 'Users retrieved successfully');
        } catch (GeneralException $e) {
            return ApiResponse(message: $e->getMessage(), code: 422);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function getfilterTeams(): JsonResponse
    {
        try {
            $filters = array_filter(request()->query());
            /** @var \App\Models\Tenant\User|null $user */
            $user = auth('api_tenant')->user();
            if ($user && method_exists($user, 'can') && $user->can(PermissionsEnum::VIEW_ADMIN_DASHBOARD->value)) {
                $teams = $this->teamService->index(filters: $filters);
            } else {
                $teams = collect([]);
            }
            $data = TeamDDLResource::collection($teams);
            return ApiResponse($data, 'teams retrieved successfully');
        } catch (GeneralException $e) {
            return ApiResponse(message: $e->getMessage(), code: 422);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
