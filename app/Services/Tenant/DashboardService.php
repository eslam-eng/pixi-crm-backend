<?php

namespace App\Services\Tenant;

use App\Enums\OpportunityStatus;
use App\Enums\TaskStatusEnum;
use App\Services\LeadService;
use App\Services\Tenant\Deals\DealService;
use App\Services\Tenant\Tasks\TaskService;
use App\Services\Tenant\Users\UserService;
use App\Services\ActivityService;
use App\Settings\ChartsSettings;
use Carbon\Carbon;

class DashboardService
{
    public function __construct(
        public DealService $dealService,
        public LeadService $leadService,
        public TaskService $taskService,
        public UserService $userService,
        public ActivityService $activityService,
    ) {}

    public function getWidgets(array $filters)
    {
        $totalLeadsWithCompare = $this->totalLeadsWithCompare($filters);

        $activeLeadsCount = $this->getActiveLeads($filters);

        $percentageWonLeads = $this->getPercentageWonLeads($filters);

        $average_of_deals_value = $this->getAverageDealsValue($filters);

        // $dueTasks = $this->getDueTasks($filters);
        $dueTasks = $this->getAllTodayTasksCount($filters);

        $avgTimeToAction = $this->getAvgTimeToAction($filters);

        $target = $this->getTarget($filters);

        return [
            'total_leads' => $totalLeadsWithCompare,
            'active_leads' => $activeLeadsCount,
            'percentage_won_leads' => $percentageWonLeads,
            'average_of_deals_value' => $average_of_deals_value,
            'due_tasks' => $dueTasks,
            'target' => $target, // still working on it
            'avg_time_to_action' => $avgTimeToAction, // still working on it
        ];
    }

    public function getOpportunitiesByStage(array $filters)
    {
        $leadsByStage = $this->leadService->getAll($filters)->groupBy('stage.name');
        $stageCounts = $leadsByStage->map(function ($leads, $stageName) {
            return [
                'stage_name' => $stageName,
                'lead_count' => $leads->count(),
            ];
        })->values();
        return $stageCounts;
    }

    public function getTodayTasks(array $filters)
    {
        $filters['due_today'] = true;
        $filters['user_id'] = user_id();

        return $this->taskService->getQuery($filters)->take(3)->get();
    }

    public function getAllTodayTasksCount(array $filters)
    {
        $filters['due_today'] = true;
        $filters['user_id'] = user_id();

        return $this->taskService->getQuery($filters)->count();
    }

    public function getSaleFunnel(array $filters)
    {
        $settings = app(ChartsSettings::class);
        $third_phase_type_id = $settings->third_phase_type;
        $opportunities = $this->leadService->index($filters, ['tasks.taskType']);
        $qualifyingOpportunities = $opportunities->where('is_qualifying', 1);
        $thirdPhase = $qualifyingOpportunities->filter(function ($opportunity) use ($third_phase_type_id) {
            return $opportunity->tasks->contains(function ($task) use ($third_phase_type_id) {
                return $task->taskType->id === $third_phase_type_id;
            });
        });

        $wonOpportunities = $thirdPhase->where('status', OpportunityStatus::WON->value);
        $total_opportunty = $opportunities->count();
        
        if ($total_opportunty != 0) {
            $qualifyingPrecentage = $qualifyingOpportunities->count() ;
            $thirdPhase = $thirdPhase->count() ;
            $wonPrecentage = $wonOpportunities->count();
        }

        return [
            'total_opportunities' => $total_opportunty,
            'qualifying' => $qualifyingPrecentage,
            'third_phase' => $thirdPhase,
            'won' => $wonPrecentage,
        ];
    }

    public function getUserRecentActivities()
    {
        return $this->activityService->getUserRecentActivities(user_id(), 5);
    }

    public function getTopPerformingSalesReps(array $filters)
    {

        $deals = $this->dealService->queryGet(filters: $filters)->get();
        
        $allUser = $deals->groupBy('assigned_to_id')->map(function ($leads, $user_id) {
            return [
                'user_id' => $user_id,
                'count' => $leads->count(),
                'total_amount' => $leads->sum('total_amount'),
            ];
        });
        $topThree = $allUser->sortByDesc('total_amount')->take(3);

        return $topThree->map(function ($data) {
            return [
                'user' => $this->userService->findById($data['user_id'])->first_name,
                'count' => $data['count'],
                'total_amount' => $data['total_amount'],
            ];
        })->values();
    }

    private function getActiveLeads(array $filters)
    {
        $filters['status'] = OpportunityStatus::ACTIVE->value;
        return $this->leadService->getAll($filters)->count();
    }

    private function getPercentageWonLeads(array $filters)
    {
        $totalLeads = $this->leadService->getAll($filters)->count();

        $filters['status'] = OpportunityStatus::WON->value;
        $wonLeads = $this->leadService->getAll($filters)->count();

        $wonPercentage = 0;

        if ($totalLeads > 0) {
            $wonPercentage = ($wonLeads / $totalLeads) * 100;
            $wonPercentage = round($wonPercentage, 2);
        }

        return $wonPercentage;
    }

    private function getAverageDealsValue(array $filters)
    {
        return $this->dealService->getAll($filters)->avg('total_amount');
    }

    private function getDueTasks(array $filters)
    {
        $filters['status'] = TaskStatusEnum::IN_PROGRESS->value;
        return $this->taskService->getAll($filters)->count();
    }

    private function getTarget(array $filters)
    {
        return 'still working on it';
    }

    private function getAvgTimeToAction(array $filters)
    {
        // Get all leads with filters applied
        $leads = $this->leadService->getAll($filters);

        // Filter leads that have avg_action_time set (not null)
        $leadsWithActionTime = $leads->filter(function ($lead) {
            return !is_null($lead->avg_action_time) && $lead->avg_action_time > 0;
        });

        // If no leads have action time, return 0
        if ($leadsWithActionTime->isEmpty()) {
            return 0;
        }

        // Calculate average time to action in seconds
        $avgSeconds = $leadsWithActionTime->avg('avg_action_time');

        // Return average in seconds (rounded to 2 decimal places)
        return round($avgSeconds, 2);
    }

    private function totalLeadsWithCompare(array $filters)
    {
        if (array_key_exists('user_id', $filters)) {
            unset($filters['user_id']);
        }

        $totalLeadsNewRange = $this->leadService->queryGet(filters: $filters)->count();

        $first_date = Carbon::parse($filters['start_date'])->copy();
        $end_date = Carbon::parse($filters['end_date'])->copy();
        $days = $first_date->diffInDays($end_date);

        $filters['start_date'] = $first_date->subDay($days)->toDateString();
        $filters['end_date'] = $end_date->subDay($days)->toDateString();

        $totalLeadsOldRange = $this->leadService->queryGet(filters: $filters)->count();
        return calcChange($totalLeadsOldRange, $totalLeadsNewRange);
    }
}
