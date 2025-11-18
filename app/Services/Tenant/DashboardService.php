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
        $filters['due_today'] = true;
        $filters['user_id'] = user_id();
       
        return $this->taskService->getQuery($filters)->take(3)->get();
    }

    public function getSaleFunnel(array $filters)
    {

        $settings = app(ChartsSettings::class);
        $third_phase_type_id = $settings->third_phase_type;
        $opportunities = $this->leadService->index($filters, ['tasks.taskType']);
        $qualifyingOpportunities = $opportunities->where('is_qualifying', 1);
        $thirdPhase = $opportunities->filter(function ($opportunity) use ($third_phase_type_id) {
            return $opportunity->tasks->contains(function ($task) use ($third_phase_type_id) {
                return $task->taskType->id === $third_phase_type_id;
            });
        });
        $wonOpportunities = $opportunities->where('status', OpportunityStatus::WON->value);
        $total_opportunty = $opportunities->count();
        $qualifyingPrecentage = $qualifyingOpportunities->count() / $total_opportunty * 100;
        $thirdPhasePrecentage = $thirdPhase->count() / $total_opportunty * 100;
        $wonPrecentage = $wonOpportunities->count() / $total_opportunty * 100;

        return [
            'total_opportunities' => $total_opportunty,
            'qualifying_precentage' => $qualifyingPrecentage,
            'third_phase_precentage' => $thirdPhasePrecentage,
            'won_precentage' => $wonPrecentage,
        ];
    }

    public function getUserRecentActivities()
    {
        return $this->activityService->getUserRecentActivities(user_id(), 5);
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
