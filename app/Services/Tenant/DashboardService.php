<?php

namespace App\Services\Tenant;

use App\Enums\OpportunityStatus;
use App\Enums\TaskStatusEnum;
use App\Services\LeadService;
use App\Services\Tenant\Deals\DealService;
use App\Services\Tenant\Tasks\TaskService;
use App\Services\Tenant\Users\UserService;
use App\Services\ActivityService;
use App\Services\Tenant\Users\ChairService;
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
        public ChairService $chairService,
    ) {}

    public function getWidgets(array $filters)
    {
        $totalLeadsWithCompare = $this->totalLeadsWithCompare($filters);

        $totalDeals = $this->getTotalDealsWithCompare($filters);

        $percentageWonLeads = $this->getPercentageWonLeads($filters);

        $average_of_deals_value = $this->getAverageDealsValue($filters);

        // $dueTasks = $this->getDueTasks($filters);
        $dueTasks = $this->getAllTodayTasksCount($filters);

        $avgTimeToAction = $this->getAvgTimeToAction($filters);

        $target = $this->getTarget($filters);

        return [
            'total_leads' => $totalLeadsWithCompare,
            'total_deals' => $totalDeals,
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
            $qualifyingPrecentage = $qualifyingOpportunities->count();
            $thirdPhase = $thirdPhase->count();
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
            $user = $this->userService->findById($data['user_id']);
            if (!$user) {
                return null;
            }
            return [
                'user' => $user->first_name,
                'user_image' => $user->image,
                'count' => $data['count'],
                'total_amount' => $data['total_amount'],
            ];
        })->values();
    }

    private function getTotalDealsWithCompare(array $filters)
    {
        $totalDealsCurrent = $this->dealService->getAll($filters)->count();

        $oldFilters = $this->getOldRangeDate($filters); //get past range date

        $totalDealsOldRange = $this->dealService->getAll($oldFilters)->count();

        return calcChange($totalDealsOldRange, $totalDealsCurrent);
    }

    private function getPercentageWonLeads(array $filters)
    {
        $filters['status'] = OpportunityStatus::WON->value;

        $wonLeads = $this->leadService->getAll($filters)->count();

        $oldFilters = $this->getOldRangeDate($filters); //get past range date

        $wonLeadsOldRange = $this->leadService->getAll($oldFilters)->count();

        return calcChange($wonLeadsOldRange, $wonLeads);
    }

    private function getAverageDealsValue(array $filters)
    {
        $avgDealsValueCurrent = $this->dealService->getAll($filters)->avg('total_amount') ?? 0;

        $oldFilters = $this->getOldRangeDate($filters); //get past range date

        $avgDealsValueOldRange = $this->dealService->getAll($oldFilters)->avg('total_amount') ?? 0;

        return calcChange($avgDealsValueOldRange, $avgDealsValueCurrent);
    }

    private function getDueTasks(array $filters)
    {
        $filters['status'] = TaskStatusEnum::IN_PROGRESS->value;
        return $this->taskService->getAll($filters)->count();
    }

    private function getTarget(array $filters)
    {
        $filters['user_id'] = $filters['user_id'] ?? user_id();
        $requireTarget = $this->getRequiredTarget($filters);
        $deals_values = $this->dealService->queryGet($filters)->sum('total_amount');

        if ($deals_values == 0) {
            return 0;
        }

        if (is_null($requireTarget)) {
            return 'user has no target';
        }

        $target_progress = $requireTarget / $deals_values * 100;
        return $target_progress;
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
        $totalLeadsNewRange = $this->leadService->queryGet(filters: $filters)->count();

        $oldFilters = $this->getOldRangeDate($filters); //get past range date

        $totalLeadsOldRange = $this->leadService->queryGet(filters: $oldFilters)->count();
        return calcChange($totalLeadsOldRange, $totalLeadsNewRange);
    }

    private function getOldRangeDate(array $filters): array
    {
        if (!isset($filters['start_date']) || !isset($filters['end_date'])) {
            return $filters;
        }
        $first_date = Carbon::parse($filters['start_date'])->copy();
        $end_date = Carbon::parse($filters['end_date'])->copy();
        $days = $first_date->diffInDays($end_date);

        $filters['start_date'] = $first_date->subDay($days)->toDateString();
        $filters['end_date'] = $end_date->subDay($days)->toDateString();

        return $filters;
    }

    private function getRequiredTarget(array $filters)
    {
        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $requierMonth = Carbon::parse($filters['start_date'])->copy()->month;
            $requierYear = Carbon::parse($filters['start_date'])->copy()->year;
        } else {
            $requierMonth = Carbon::now()->copy()->month;
            $requierYear = Carbon::now()->copy()->year;
        }

        $newFilters = [
            'user' => $filters['user_id'],
            'period_number' => $requierMonth,
            'year' => $requierYear,
        ];

        $chair = $this->chairService->queryGet([
            'chair_rarget' => $newFilters
        ])->first();

        if (!$chair || !$chair->exists()) {
            return null;
        }
        return $chair->target($requierYear, $requierMonth)->value('amount');
    }
}
