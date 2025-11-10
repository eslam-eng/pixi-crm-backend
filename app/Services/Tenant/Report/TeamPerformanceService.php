<?php

namespace App\Services\Tenant\Report;

use App\DTO\Report\SalesPerformanceReportDTO;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TeamPerformanceService
{
    /**
     * Calculate KPIs for the dashboard
     */
    public function calculateKPIs($dealsData, $revenueData): array
    {
        // Get active team members count (users with active chairs)
        $teamSize = DB::table('chairs')
            ->join('users', 'chairs.user_id', '=', 'users.id')
            ->where('users.is_active', true)
            ->whereNull('chairs.ended_at')
            ->distinct()
            ->count('chairs.user_id');

        // Calculate total revenue
        $totalRevenue = $revenueData->sum('total_amount') ?? 0;

        // Calculate average deal size
        $avgDealSize = $dealsData->avg('deal_value') ?? 0;

        // Calculate quota attainment based on active chairs (team members at current time)
        $totalQuota = DB::table('chairs')
            ->join('users', 'chairs.user_id', '=', 'users.id')
            ->where('users.is_active', true)
            ->whereNull('chairs.ended_at')
            ->sum('users.target') ?? 0;

        $quotaAttainment = $totalQuota > 0 ? ($totalRevenue / $totalQuota) * 100 : 0;

        // Calculate previous period for comparison
        $previousPeriodStart = Carbon::now()->subMonths(3)->startOfMonth();
        $previousPeriodEnd = Carbon::now()->subMonths(1)->endOfMonth();

        // Get previous revenue using chair_id from deals
        $previousRevenue = DB::table('deals')
            ->whereBetween('deals.sale_date', [$previousPeriodStart, $previousPeriodEnd])
            ->whereNotNull('deals.chair_id')
            ->sum('deals.total_amount') ?? 0;

        $previousAvgDealSize = DB::table('leads')
            ->whereBetween('leads.created_at', [$previousPeriodStart, $previousPeriodEnd])
            ->avg('deal_value') ?? 0;

        // Calculate previous quota based on chairs active during that period
        $previousQuota = DB::table('chairs')
            ->join('users', 'chairs.user_id', '=', 'users.id')
            ->where('users.is_active', true)
            ->where(function ($q) use ($previousPeriodStart, $previousPeriodEnd) {
                $q->where(function ($q2) use ($previousPeriodStart, $previousPeriodEnd) {
                    // Chairs that were active during the previous period
                    $q2->where('chairs.started_at', '<=', $previousPeriodEnd)
                        ->where(function ($q3) use ($previousPeriodStart) {
                            $q3->whereNull('chairs.ended_at')
                                ->orWhere('chairs.ended_at', '>=', $previousPeriodStart);
                        });
                });
            })
            ->sum('users.target') ?? 0;

        $previousQuotaAttainment = $previousQuota > 0 ? ($previousRevenue / $previousQuota) * 100 : 0;

        return [
            'team_size' => [
                'value' => $teamSize,
                'change' => null, // Team size doesn't typically have percentage change
            ],
            'total_revenue' => [
                'value' => $totalRevenue,
                'formatted' => '$' . number_format($totalRevenue / 1000000, 1) . 'M',
                'change' => $previousRevenue > 0 ? round((($totalRevenue - $previousRevenue) / $previousRevenue) * 100, 1) : 0,
                'change_type' => $totalRevenue >= $previousRevenue ? 'increase' : 'decrease',
            ],
            'avg_deal_size' => [
                'value' => $avgDealSize,
                'formatted' => '$' . number_format($avgDealSize / 1000, 0) . 'K',
                'change' => $previousAvgDealSize > 0 ? round((($avgDealSize - $previousAvgDealSize) / $previousAvgDealSize) * 100, 1) : 0,
                'change_type' => $avgDealSize >= $previousAvgDealSize ? 'increase' : 'decrease',
            ],
            'quota_attainment' => [
                'value' => $quotaAttainment,
                'formatted' => round($quotaAttainment, 0) . '%',
                'change' => $previousQuotaAttainment > 0 ? round(($quotaAttainment - $previousQuotaAttainment), 1) : 0,
                'change_type' => $quotaAttainment >= $previousQuotaAttainment ? 'increase' : 'decrease',
            ],
        ];
    }

    /**
     * Get individual performance data
     */
    public function getIndividualPerformance($dealsData, $revenueData): array
    {
        $performance = $dealsData->groupBy(function ($item) {
            return ($item->user_first_name ?? '') . ' ' . ($item->user_last_name ?? '');
        })->map(function ($userDeals, $userName) use ($revenueData) {
            // Get revenue for this user by matching user name
            $userRevenue = $revenueData->filter(function ($deal) use ($userName) {
                $dealUserName = ($deal->user_first_name ?? '') . ' ' . ($deal->user_last_name ?? '');
                return $dealUserName === $userName;
            })->sum('total_amount') ?? 0;

            // Count deals closed (won deals)
            $dealsClosed = $userDeals->where('status', 'won')->count();

            return [
                'user_name' => $userName ?: 'Unassigned',
                'revenue' => $userRevenue,
                'revenue_formatted' => round($userRevenue / 1000, 0),
                'deals_closed' => $dealsClosed,
            ];
        })->sortByDesc('revenue')->values();

        return $performance->toArray();
    }

    /**
     * Get win rate by team member
     */
    public function getWinRateByTeamMember($dealsData): array
    {
        $winRates = $dealsData->groupBy(function ($item) {
            return ($item->user_first_name ?? '') . ' ' . ($item->user_last_name ?? '');
        })->map(function ($userDeals, $userName) {
            $totalDeals = $userDeals->count();
            $wonDeals = $userDeals->where('status', 'won')->count();
            $winRate = $totalDeals > 0 ? ($wonDeals / $totalDeals) * 100 : 0;

            return [
                'user_name' => $userName ?: 'Unassigned',
                'win_rate' => round($winRate, 1),
                'total_deals' => $totalDeals,
                'won_deals' => $wonDeals,
            ];
        })->sortByDesc('win_rate')->values();

        return $winRates->toArray();
    }

    /**
     * Get activity distribution
     */
    public function getActivityDistribution(SalesPerformanceReportDTO $filters): array
    {
        $dateRange = $filters->hasDateRange() ? $filters->getDateRange() : [
            'from' => Carbon::now()->subMonths(6)->startOfMonth(),
            'to' => Carbon::now()->endOfMonth(),
        ];

        $fromDate = $dateRange['from'] ?? Carbon::now()->subMonths(6)->startOfMonth();
        $toDate = $dateRange['to'] ?? Carbon::now()->endOfMonth();

        // Get activity log table name from config
        $activityLogTable = config('activitylog.table_name', 'activity_log');

        // Count activities by type (using log_name or description patterns)
        $calls = DB::table($activityLogTable)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->where(function ($q) {
                $q->where('description', 'like', '%call%')
                    ->orWhere('log_name', 'like', '%call%');
            })
            ->count();

        $meetings = DB::table($activityLogTable)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->where(function ($q) {
                $q->where('description', 'like', '%meeting%')
                    ->orWhere('log_name', 'like', '%meeting%');
            })
            ->count();

        $emails = DB::table($activityLogTable)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->where(function ($q) {
                $q->where('description', 'like', '%email%')
                    ->orWhere('log_name', 'like', '%email%');
            })
            ->count();

        // Count tasks
        $tasksQuery = DB::table('tasks')
            ->whereBetween('created_at', [$fromDate, $toDate]);

        if ($filters->hasUserFilter()) {
            $tasksQuery->whereIn('assigned_to_id', $filters->user_ids);
        }

        $tasks = $tasksQuery->count();

        return [
            [
                'activity_type' => 'Calls',
                'count' => $calls,
            ],
            [
                'activity_type' => 'Meetings',
                'count' => $meetings,
            ],
            [
                'activity_type' => 'Emails',
                'count' => $emails,
            ],
            [
                'activity_type' => 'Tasks',
                'count' => $tasks,
            ],
        ];
    }

    /**
     * Get team activity trend
     */
    public function getTeamActivityTrend(SalesPerformanceReportDTO $filters): array
    {
        $months = [];
        $dateRange = $filters->hasDateRange() ? $filters->getDateRange() : [
            'from' => Carbon::now()->subMonths(6)->startOfMonth(),
            'to' => Carbon::now()->endOfMonth(),
        ];

        $startDate = $dateRange['from'] ?? Carbon::now()->subMonths(6)->startOfMonth();
        $endDate = $dateRange['to'] ?? Carbon::now()->endOfMonth();

        $current = $startDate->copy();
        while ($current <= $endDate) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();

            // Get activity log table name from config
            $activityLogTable = config('activitylog.table_name', 'activity_log');

            // Count activities by type for this month
            $calls = DB::table($activityLogTable)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->where(function ($q) {
                    $q->where('description', 'like', '%call%')
                        ->orWhere('log_name', 'like', '%call%');
                })
                ->count();

            $meetings = DB::table($activityLogTable)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->where(function ($q) {
                    $q->where('description', 'like', '%meeting%')
                        ->orWhere('log_name', 'like', '%meeting%');
                })
                ->count();

            $emails = DB::table($activityLogTable)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->where(function ($q) {
                    $q->where('description', 'like', '%email%')
                        ->orWhere('log_name', 'like', '%email%');
                })
                ->count();

            $tasks = DB::table('tasks')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            $months[] = [
                'month' => $current->format('M'),
                'calls' => $calls,
                'meetings' => $meetings,
                'emails' => $emails,
                'tasks' => $tasks,
            ];

            $current->addMonth();
        }

        return $months;
    }

    /**
     * Get quota attainment trend
     */
    public function getQuotaAttainmentTrend($dealsData, SalesPerformanceReportDTO $filters): array
    {
        $months = [];
        $dateRange = $filters->hasDateRange() ? $filters->getDateRange() : [
            'from' => Carbon::now()->subMonths(6)->startOfMonth(),
            'to' => Carbon::now()->endOfMonth(),
        ];

        $startDate = $dateRange['from'] ?? Carbon::now()->subMonths(6)->startOfMonth();
        $endDate = $dateRange['to'] ?? Carbon::now()->endOfMonth();

        $current = $startDate->copy();
        while ($current <= $endDate) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();

            // Get revenue for this month using chair_id from deals
            $monthRevenue = DB::table('deals')
                ->whereBetween('deals.sale_date', [$monthStart, $monthEnd])
                ->whereNotNull('deals.chair_id')
                ->sum('deals.total_amount') ?? 0;

            // Calculate quota for this month based on chairs active during this month
            $monthQuota = DB::table('chairs')
                ->join('users', 'chairs.user_id', '=', 'users.id')
                ->where('users.is_active', true)
                ->where(function ($q) use ($monthStart, $monthEnd) {
                    // Chairs that were active during this month
                    $q->where('chairs.started_at', '<=', $monthEnd)
                        ->where(function ($q2) use ($monthStart) {
                            $q2->whereNull('chairs.ended_at')
                                ->orWhere('chairs.ended_at', '>=', $monthStart);
                        });
                })
                ->sum('users.target') ?? 0;

            $attainment = $monthQuota > 0 ? ($monthRevenue / $monthQuota) * 100 : 0;

            $months[] = [
                'month' => $current->format('M'),
                'attainment_percentage' => round($attainment, 1),
                'target_percentage' => 80, // Default target
            ];

            $current->addMonth();
        }

        return $months;
    }

    /**
     * Get monthly revenue by team member
     */
    public function getMonthlyRevenueByTeamMember($revenueData, SalesPerformanceReportDTO $filters): array
    {
        $dateRange = $filters->hasDateRange() ? $filters->getDateRange() : [
            'from' => Carbon::now()->subMonths(6)->startOfMonth(),
            'to' => Carbon::now()->endOfMonth(),
        ];

        $startDate = $dateRange['from'] ?? Carbon::now()->subMonths(6)->startOfMonth();
        $endDate = $dateRange['to'] ?? Carbon::now()->endOfMonth();

        // Get all users with their active chairs
        $users = DB::table('users')
            ->join('chairs', function ($join) {
                $join->on('users.id', '=', 'chairs.user_id')
                    ->whereNull('chairs.ended_at');
            })
            ->where('users.is_active', true)
            ->select('users.id', 'users.first_name', 'users.last_name', 'chairs.id as chair_id')
            ->groupBy('users.id', 'users.first_name', 'users.last_name', 'chairs.id')
            ->get();

        $result = [];
        $months = [];

        $current = $startDate->copy();
        while ($current <= $endDate) {
            $months[] = $current->format('M');
            $current->addMonth();
        }

        foreach ($users as $user) {
            $userRevenue = [];
            $current = $startDate->copy();

            while ($current <= $endDate) {
                $monthStart = $current->copy()->startOfMonth();
                $monthEnd = $current->copy()->endOfMonth();

                // Get revenue using chair_id from deals table
                $monthRevenue = DB::table('deals')
                    ->join('chairs', 'deals.chair_id', '=', 'chairs.id')
                    ->where('chairs.user_id', $user->id)
                    ->whereBetween('deals.sale_date', [$monthStart, $monthEnd])
                    ->where(function ($q) use ($monthStart, $monthEnd) {
                        // Ensure the chair was active during this month
                        $q->where('chairs.started_at', '<=', $monthEnd)
                            ->where(function ($q2) use ($monthStart) {
                                $q2->whereNull('chairs.ended_at')
                                    ->orWhere('chairs.ended_at', '>=', $monthStart);
                            });
                    })
                    ->sum('deals.total_amount') ?? 0;

                $userRevenue[] = round($monthRevenue / 1000, 0); // Convert to thousands

                $current->addMonth();
            }

            $result[] = [
                'user_name' => trim($user->first_name . ' ' . $user->last_name),
                'months' => $months,
                'revenue' => $userRevenue,
            ];
        }

        return $result;
    }

    /**
     * Get pipeline contribution by team member
     */
    public function getPipelineContribution($dealsData): array
    {
        $contribution = $dealsData->groupBy(function ($item) {
            return ($item->user_first_name ?? '') . ' ' . ($item->user_last_name ?? '');
        })->map(function ($userDeals, $userName) {
            return [
                'user_name' => $userName ?: 'Unassigned',
                'pipeline_value' => $userDeals->sum('deal_value'),
            ];
        })->sortByDesc('pipeline_value')->values();

        return $contribution->toArray();
    }

    /**
     * Get team skills assessment (placeholder - would need actual skills data)
     */
    public function getTeamSkillsAssessment($dealsData): array
    {
        // This is a placeholder implementation
        // In a real scenario, you would have a skills assessment table
        // For now, we'll calculate based on performance metrics

        $skills = ['Prospecting', 'Qualification', 'Presentation', 'Negotiation', 'Closing', 'Follow-up'];

        $teamAvg = [];
        $topPerformer = [];

        // Calculate team average (simplified - using win rates and deal metrics)
        $totalDeals = $dealsData->count();
        $wonDeals = $dealsData->where('status', 'won')->count();
        $avgWinRate = $totalDeals > 0 ? ($wonDeals / $totalDeals) * 100 : 0;

        // Set team average scores (placeholder logic)
        foreach ($skills as $skill) {
            $teamAvg[] = [
                'skill' => $skill,
                'score' => round($avgWinRate * 0.8), // Simplified calculation
            ];
        }

        // Get top performer
        $topPerformerData = $dealsData->groupBy(function ($item) {
            return ($item->user_first_name ?? '') . ' ' . ($item->user_last_name ?? '');
        })->map(function ($userDeals) {
            $total = $userDeals->count();
            $won = $userDeals->where('status', 'won')->count();
            return $total > 0 ? ($won / $total) * 100 : 0;
        })->sortByDesc(function ($rate) {
            return $rate;
        })->first();

        $topPerformerRate = $topPerformerData ?? 100;

        foreach ($skills as $skill) {
            $topPerformer[] = [
                'skill' => $skill,
                'score' => round($topPerformerRate * 1.1), // Top performer scores higher
            ];
        }

        return [
            'team_avg' => $teamAvg,
            'top_performer' => $topPerformer,
        ];
    }
}
