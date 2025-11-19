<?php

namespace App\Services\Tenant;

use App\Enums\OpportunityStatus;
use App\Enums\TaskStatusEnum;
use App\Models\Tenant\Deal;
use App\Services\LeadService;
use App\Services\Tenant\Deals\DealService;
use App\Services\Tenant\Tasks\TaskService;
use App\Services\Tenant\Users\UserService;
use App\Services\ActivityService;

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
        $totalLeads = $this->leadService->getAll($filters)->count();

        $activeLeadsCount = $this->getActiveLeads($filters);

        $percentageWonLeads = $this->getPercentageWonLeads($filters);

        $average_of_deals_value = $this->getAverageDealsValue($filters);

        $dueTasks = $this->getDueTasks($filters);

        $avgTimeToAction = $this->getAvgTimeToAction($filters);

        $target = $this->getTarget($filters);

        return [
            'total_leads' => $totalLeads,
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
        unset($filters['start_date'], $filters['end_date']);
        $filters['due_today'] = true;
        return $this->taskService->getQuery($filters)->orderBy('created_at', 'desc')->take(5)->get();
    }

    public function getSaleFunnel(array $filters)
    {
        $opportunities = $this->leadService->index($filters, ['tasks.taskType']);
        $qualifyingOpportunities = $opportunities->where('is_qualifying', 1);
        $meetingOpportunities = $opportunities->filter(function ($opportunity) {
            return $opportunity->tasks->contains(function ($task) {
                return $task->taskType->name === 'Meeting';
            });
        });

        $wonOpportunities = $opportunities->where('status', OpportunityStatus::WON->value);
        if ($opportunities->count() != 0) {
            $qualifyingPrecentage = $qualifyingOpportunities->count() / $opportunities->count() * 100;
            $meetingPrecentage = $meetingOpportunities->count() / $opportunities->count() * 100;
            $wonPrecentage = $wonOpportunities->count() / $opportunities->count() * 100;
        }

        return [
            'total_opportunities' => $opportunities->count(),
            'qualifying_precentage' => $qualifyingPrecentage ?? 0,
            'meeting_precentage' => $meetingPrecentage ?? 0,
            'won_precentage' => $wonPrecentage ?? 0,
        ];
    }

    public function getUserRecentActivities()
    {
        return $this->activityService->getUserRecentActivities(auth()->id(), 5);
    }

    public function getTopPerformingSalesReps()
    {
        $filters = [];
        $relation = [];
        $data = $this->dealService->queryGet(filters: $filters, withRelations:$relation);
        dd()
        dd(Deal::VisibleForPage(auth()->user()));
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
        return 'still working on it';
    }
}
