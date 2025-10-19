<?php

namespace App\Services\Tenant\Report;

use App\DTO\Report\ReportDTO;
use App\DTO\Report\ReportFilterDTO;
use App\Models\Tenant\Report;
use App\Models\Tenant\ReportExecution;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportService extends BaseService
{
    public function __construct(
        public Report $model,
        public ReportExecution $executionModel,
    ) {}

    public function getModel(): Report
    {
        return $this->model;
    }

    /**
     * Execute Sales Performance Report
     */
    public function executeSalesPerformanceReport($filters = null): array
    {
        $query = DB::table('leads')
            ->leftJoin('contacts', 'leads.contact_id', '=', 'contacts.id')
            ->leftJoin('users', 'leads.assigned_to_id', '=', 'users.id')
            ->leftJoin('stages', 'leads.stage_id', '=', 'stages.id')
            ->leftJoin('sources', 'contacts.source_id', '=', 'sources.id')
            ->leftJoin('teams', 'users.team_id', '=', 'teams.id');

        // Apply filters
        if ($filters) {
            $this->applyCommonFilters($query, $filters);
        }

        $data = $query->select([
            'leads.id',
            'leads.deal_value',
            'leads.win_probability',
            'leads.status',
            'leads.created_at',
            'leads.expected_close_date',
            'contacts.first_name',
            'contacts.last_name',
            'contacts.company_name',
            'users.first_name as user_first_name',
            'users.last_name as user_last_name',
            'stages.name as stage_name',
            'sources.name as source_name',
            'teams.title as team_name',
        ])->get();

        return [
            'data' => $data,
            'records_count' => $data->count(),
            'summary' => $this->calculateSalesPerformanceSummary($data),
        ];
    }

    /**
     * Execute Lead Management Report
     */
    public function executeLeadManagementReport($filters = null): array
    {
        $query = DB::table('leads')
            ->leftJoin('contacts', 'leads.contact_id', '=', 'contacts.id')
            ->leftJoin('users', 'leads.assigned_to_id', '=', 'users.id')
            ->leftJoin('stages', 'leads.stage_id', '=', 'stages.id')
            ->leftJoin('sources', 'contacts.source_id', '=', 'sources.id');

        // Apply filters
        if ($filters) {
            $this->applyCommonFilters($query, $filters);
        }

        $data = $query->select([
            'leads.id',
            'leads.status',
            'leads.is_qualifying',
            'leads.deal_value',
            'leads.win_probability',
            'leads.created_at',
            'contacts.first_name',
            'contacts.last_name',
            'contacts.email',
            'contacts.company_name',
            'users.first_name as user_first_name',
            'users.last_name as user_last_name',
            'stages.name as stage_name',
            'sources.name as source_name',
        ])->get();

        return [
            'data' => $data,
            'records_count' => $data->count(),
            'summary' => $this->calculateLeadManagementSummary($data),
        ];
    }

    /**
     * Execute Team Performance Report
     */
    public function executeTeamPerformanceReport($filters = null): array
    {
        $query = DB::table('teams')
            ->leftJoin('chairs', 'teams.id', '=', 'chairs.team_id')
            ->leftJoin('users', 'chairs.user_id', '=', 'users.id')
            ->leftJoin('leads', function ($join) {
                $join->on('users.id', '=', 'leads.assigned_to_id')
                    ->whereNull('chairs.ended_at');
            })
            ->leftJoin('deals', function ($join) {
                $join->on('users.id', '=', 'deals.assigned_to_id')
                    ->whereNull('chairs.ended_at');
            });

        // Apply filters
        if ($filters) {
            if ($filters->hasTeamFilter()) {
                $query->whereIn('teams.id', $filters->team_ids);
            }

            if ($filters->hasDateRange()) {
                $dateRange = $filters->getDateRange();
                if ($dateRange['from']) {
                    $query->where('leads.created_at', '>=', $dateRange['from']);
                }
                if ($dateRange['to']) {
                    $query->where('leads.created_at', '<=', $dateRange['to']);
                }
            }
        }

        $data = $query->select([
            'teams.id as team_id',
            'teams.title as team_name',
            DB::raw('COUNT(DISTINCT chairs.user_id) as team_size'),
            DB::raw('COUNT(DISTINCT leads.id) as total_leads'),
            DB::raw('SUM(leads.deal_value) as total_pipeline_value'),
            DB::raw('AVG(leads.deal_value) as avg_deal_size'),
            DB::raw('COUNT(DISTINCT deals.id) as total_deals'),
            DB::raw('SUM(deals.total_amount) as total_revenue'),
        ])
            ->whereNull('chairs.ended_at')
            ->groupBy('teams.id', 'teams.title')
            ->get();

        return [
            'data' => $data,
            'records_count' => $data->count(),
            'summary' => $this->calculateTeamPerformanceSummary($data),
        ];
    }

    /**
     * Execute Task Completion Report
     */
    public function executeTaskCompletionReport($filters = null): array
    {
        $query = DB::table('tasks')
            ->leftJoin('users', 'tasks.assigned_to_id', '=', 'users.id')
            ->leftJoin('teams', 'users.team_id', '=', 'teams.id')
            ->leftJoin('priorities', 'tasks.priority_id', '=', 'priorities.id');

        // Apply filters
        if ($filters) {
            $this->applyCommonFilters($query, $filters);

            if ($filters->hasDateRange()) {
                $dateRange = $filters->getDateRange();
                if ($dateRange['from']) {
                    $query->where('tasks.created_at', '>=', $dateRange['from']);
                }
                if ($dateRange['to']) {
                    $query->where('tasks.created_at', '<=', $dateRange['to']);
                }
            }
        }

        $data = $query->select([
            'tasks.id',
            'tasks.title',
            'tasks.status',
            'tasks.priority_id',
            'tasks.due_date',
            'tasks.created_at',
            'users.first_name as user_first_name',
            'users.last_name as user_last_name',
            'teams.title as team_name',
            'priorities.name as priority_name',
        ])->get();

        return [
            'data' => $data,
            'records_count' => $data->count(),
            'summary' => $this->calculateTaskCompletionSummary($data),
        ];
    }

    /**
     * Execute Revenue Analysis Report
     */
    public function executeRevenueAnalysisReport($filters = null): array
    {
        $query = DB::table('deals')
            ->leftJoin('leads', 'deals.lead_id', '=', 'leads.id')
            ->leftJoin('contacts', 'leads.contact_id', '=', 'contacts.id')
            ->leftJoin('users', 'deals.assigned_to_id', '=', 'users.id')
            ->leftJoin('teams', 'users.team_id', '=', 'teams.id');

        // Apply filters
        if ($filters) {
            $this->applyCommonFilters($query, $filters);

            if ($filters->hasDateRange()) {
                $dateRange = $filters->getDateRange();
                if ($dateRange['from']) {
                    $query->where('deals.sale_date', '>=', $dateRange['from']);
                }
                if ($dateRange['to']) {
                    $query->where('deals.sale_date', '<=', $dateRange['to']);
                }
            }
        }

        $data = $query->select([
            'deals.id',
            'deals.deal_name',
            'deals.total_amount',
            'deals.partial_amount_paid',
            'deals.sale_date',
            'deals.payment_status',
            'contacts.company_name',
            'users.first_name as user_first_name',
            'users.last_name as user_last_name',
            'teams.title as team_name',
        ])->get();

        return [
            'data' => $data,
            'records_count' => $data->count(),
            'summary' => $this->calculateRevenueAnalysisSummary($data),
        ];
    }

    /**
     * Execute Opportunity Pipeline Report
     */
    public function executeOpportunityPipelineReport($filters = null): array
    {
        $query = DB::table('leads')
            ->leftJoin('contacts', 'leads.contact_id', '=', 'contacts.id')
            ->leftJoin('users', 'leads.assigned_to_id', '=', 'users.id')
            ->leftJoin('stages', 'leads.stage_id', '=', 'stages.id')
            ->leftJoin('teams', 'users.team_id', '=', 'teams.id');

        // Apply filters
        if ($filters) {
            $this->applyCommonFilters($query, $filters);
        }

        $data = $query->select([
            'leads.id',
            'leads.deal_value',
            'leads.win_probability',
            'leads.status',
            'leads.created_at',
            'leads.expected_close_date',
            'contacts.company_name',
            'users.first_name as user_first_name',
            'users.last_name as user_last_name',
            'stages.name as stage_name',
            'stages.probability as stage_probability',
            'teams.title as team_name',
        ])->get();

        return [
            'data' => $data,
            'records_count' => $data->count(),
            'summary' => $this->calculateOpportunityPipelineSummary($data),
        ];
    }

    /**
     * Execute Call Activity Report
     */
    public function executeCallActivityReport($filters = null): array
    {
        // This would need to be implemented based on your call logging system
        // For now, returning empty data structure
        return [
            'data' => collect(),
            'records_count' => 0,
            'summary' => [],
        ];
    }

    /**
     * Execute Contact Management Report
     */
    public function executeContactManagementReport($filters = null): array
    {
        $query = DB::table('contacts')
            ->leftJoin('users', 'contacts.user_id', '=', 'users.id')
            ->leftJoin('sources', 'contacts.source_id', '=', 'sources.id')
            ->leftJoin('countries', 'contacts.country_id', '=', 'countries.id')
            ->leftJoin('cities', 'contacts.city_id', '=', 'cities.id');

        // Apply filters
        if ($filters) {
            $this->applyCommonFilters($query, $filters);

            if ($filters->hasDateRange()) {
                $dateRange = $filters->getDateRange();
                if ($dateRange['from']) {
                    $query->where('contacts.created_at', '>=', $dateRange['from']);
                }
                if ($dateRange['to']) {
                    $query->where('contacts.created_at', '<=', $dateRange['to']);
                }
            }
        }

        $data = $query->select([
            'contacts.id',
            'contacts.first_name',
            'contacts.last_name',
            'contacts.email',
            'contacts.company_name',
            'contacts.status',
            'contacts.created_at',
            'users.first_name as user_first_name',
            'users.last_name as user_last_name',
            'sources.name as source_name',
            'countries.name as country_name',
            'cities.name as city_name',
        ])->get();

        return [
            'data' => $data,
            'records_count' => $data->count(),
            'summary' => $this->calculateContactManagementSummary($data),
        ];
    }

    /**
     * Execute Product Performance Report
     */
    public function executeProductPerformanceReport($filters = null): array
    {
        $query = DB::table('deal_items')
            ->leftJoin('deals', 'deal_items.deal_id', '=', 'deals.id')
            ->leftJoin('items', 'deal_items.item_id', '=', 'items.id')
            ->leftJoin('item_categories', 'items.category_id', '=', 'item_categories.id');

        // Apply filters
        if ($filters) {
            if ($filters->hasDateRange()) {
                $dateRange = $filters->getDateRange();
                if ($dateRange['from']) {
                    $query->where('deals.sale_date', '>=', $dateRange['from']);
                }
                if ($dateRange['to']) {
                    $query->where('deals.sale_date', '<=', $dateRange['to']);
                }
            }
        }

        $data = $query->select([
            'items.id',
            'items.name as product_name',
            'item_categories.name as category_name',
            DB::raw('SUM(deal_items.quantity) as total_quantity'),
            DB::raw('SUM(deal_items.total) as total_revenue'),
            DB::raw('AVG(deal_items.price) as avg_price'),
            DB::raw('COUNT(DISTINCT deals.id) as total_deals'),
        ])
            ->groupBy('items.id', 'items.name', 'item_categories.name')
            ->get();

        return [
            'data' => $data,
            'records_count' => $data->count(),
            'summary' => $this->calculateProductPerformanceSummary($data),
        ];
    }

    /**
     * Execute Forecasting Report
     */
    public function executeForecastingReport($filters = null): array
    {
        $query = DB::table('leads')
            ->leftJoin('contacts', 'leads.contact_id', '=', 'contacts.id')
            ->leftJoin('users', 'leads.assigned_to_id', '=', 'users.id')
            ->leftJoin('stages', 'leads.stage_id', '=', 'stages.id');

        // Apply filters
        if ($filters) {
            $this->applyCommonFilters($query, $filters);
        }

        $data = $query->select([
            'leads.id',
            'leads.deal_value',
            'leads.win_probability',
            'leads.expected_close_date',
            'stages.name as stage_name',
            'stages.probability as stage_probability',
            DB::raw('(leads.deal_value * leads.win_probability / 100) as weighted_value'),
        ])->get();

        return [
            'data' => $data,
            'records_count' => $data->count(),
            'summary' => $this->calculateForecastingSummary($data),
        ];
    }

    /**
     * Execute SuperAdmin Report
     */
    public function executeSuperAdminReport($filters = null): array
    {
        // This would need to be implemented based on your superadmin data structure
        // For now, returning empty data structure
        return [
            'data' => collect(),
            'records_count' => 0,
            'summary' => [
                'total_clients' => 0,
                'active_subscriptions' => 0,
                'total_revenue' => 0,
            ],
        ];
    }

    /**
     * Apply common filters to query
     */
    protected function applyCommonFilters($query, $filters): void
    {
        if (!$filters) {
            return;
        }

        // Use method-based approach if available, otherwise fall back to property-based
        if (method_exists($filters, 'hasUserFilter') && $filters->hasUserFilter()) {
            $query->whereIn('leads.assigned_to_id', $filters->user_ids);
        } elseif (property_exists($filters, 'user_ids') && !empty($filters->user_ids)) {
            $query->whereIn('leads.assigned_to_id', $filters->user_ids);
        }

        if (method_exists($filters, 'hasTeamFilter') && $filters->hasTeamFilter()) {
            $query->whereIn('users.team_id', $filters->team_ids);
        } elseif (property_exists($filters, 'team_ids') && !empty($filters->team_ids)) {
            $query->whereIn('users.team_id', $filters->team_ids);
        }

        if (method_exists($filters, 'hasStageFilter') && $filters->hasStageFilter()) {
            $query->whereIn('leads.stage_id', $filters->stage_ids);
        } elseif (property_exists($filters, 'stage_ids') && !empty($filters->stage_ids)) {
            $query->whereIn('leads.stage_id', $filters->stage_ids);
        }

        if (method_exists($filters, 'hasStatusFilter') && $filters->hasStatusFilter()) {
            $statusField = property_exists($filters, 'deal_statuses') ? 'deal_statuses' : 'statuses';
            $query->whereIn('leads.status', $filters->$statusField);
        } elseif (property_exists($filters, 'statuses') && !empty($filters->statuses)) {
            $query->whereIn('leads.status', $filters->statuses);
        } elseif (property_exists($filters, 'deal_statuses') && !empty($filters->deal_statuses)) {
            $query->whereIn('leads.status', $filters->deal_statuses);
        }

        if (method_exists($filters, 'hasSourceFilter') && $filters->hasSourceFilter()) {
            $query->whereIn('contacts.source_id', $filters->sources);
        } elseif (property_exists($filters, 'sources') && !empty($filters->sources)) {
            $query->whereIn('contacts.source_id', $filters->sources);
        }

        if (method_exists($filters, 'hasSearch') && $filters->hasSearch()) {
            $query->where(function ($q) use ($filters) {
                $q->where('contacts.first_name', 'like', '%' . $filters->search . '%')
                    ->orWhere('contacts.last_name', 'like', '%' . $filters->search . '%')
                    ->orWhere('contacts.company_name', 'like', '%' . $filters->search . '%')
                    ->orWhere('contacts.email', 'like', '%' . $filters->search . '%');
            });
        } elseif (property_exists($filters, 'search') && !empty($filters->search)) {
            $query->where(function ($q) use ($filters) {
                $q->where('contacts.first_name', 'like', '%' . $filters->search . '%')
                    ->orWhere('contacts.last_name', 'like', '%' . $filters->search . '%')
                    ->orWhere('contacts.company_name', 'like', '%' . $filters->search . '%')
                    ->orWhere('contacts.email', 'like', '%' . $filters->search . '%');
            });
        }

        // Value range filtering
        if (property_exists($filters, 'value_range_min') && $filters->value_range_min !== null && $filters->value_range_min !== '') {
            $query->where('leads.deal_value', '>=', $filters->value_range_min);
        }
        if (property_exists($filters, 'value_range_max') && $filters->value_range_max !== null && $filters->value_range_max !== '') {
            $query->where('leads.deal_value', '<=', $filters->value_range_max);
        }

        // Probability range filtering
        if (property_exists($filters, 'probability_range_min') && $filters->probability_range_min !== null && $filters->probability_range_min !== '') {
            $query->where('leads.win_probability', '>=', $filters->probability_range_min);
        }
        if (property_exists($filters, 'probability_range_max') && $filters->probability_range_max !== null && $filters->probability_range_max !== '') {
            $query->where('leads.win_probability', '<=', $filters->probability_range_max);
        }

        // Date range filtering
        if (method_exists($filters, 'hasDateRange') && $filters->hasDateRange()) {
            $dateRange = $filters->getDateRange();
            if ($dateRange['from']) {
                $query->where('leads.created_at', '>=', $dateRange['from']);
            }
            if ($dateRange['to']) {
                $query->where('leads.created_at', '<=', $dateRange['to']);
            }
        } else {
            if (property_exists($filters, 'date_from') && $filters->date_from) {
                $query->where('leads.created_at', '>=', $filters->date_from);
            }
            if (property_exists($filters, 'date_to') && $filters->date_to) {
                $query->where('leads.created_at', '<=', $filters->date_to);
            }
        }
    }

    /**
     * Calculate next run time for scheduled reports
     */
    protected function calculateNextRunTime(string $frequency, string $time = null): Carbon
    {
        $time = $time ?: '09:00:00';
        $now = Carbon::now();

        switch ($frequency) {
            case 'daily':
                return $now->addDay()->setTimeFromTimeString($time);
            case 'weekly':
                return $now->addWeek()->setTimeFromTimeString($time);
            case 'monthly':
                return $now->addMonth()->setTimeFromTimeString($time);
            case 'quarterly':
                return $now->addQuarter()->setTimeFromTimeString($time);
            case 'yearly':
                return $now->addYear()->setTimeFromTimeString($time);
            default:
                return $now->addDay()->setTimeFromTimeString($time);
        }
    }

    /**
     * Calculate sales performance summary
     */
    protected function calculateSalesPerformanceSummary($data): array
    {
        return [
            'total_deals' => $data->count(),
            'total_pipeline_value' => $data->sum('deal_value'),
            'average_deal_size' => $data->avg('deal_value'),
            'win_rate' => $data->where('status', 'won')->count() / max($data->count(), 1) * 100,
            'conversion_by_stage' => $data->groupBy('stage_name')->map->count(),
        ];
    }

    /**
     * Calculate lead management summary
     */
    protected function calculateLeadManagementSummary($data): array
    {
        return [
            'total_leads' => $data->count(),
            'qualified_leads' => $data->where('is_qualifying', true)->count(),
            'conversion_rate' => $data->where('status', 'converted')->count() / max($data->count(), 1) * 100,
            'leads_by_source' => $data->groupBy('source_name')->map->count(),
            'leads_by_status' => $data->groupBy('status')->map->count(),
        ];
    }

    /**
     * Calculate team performance summary
     */
    protected function calculateTeamPerformanceSummary($data): array
    {
        return [
            'total_teams' => $data->count(),
            'total_team_members' => $data->sum('team_size'),
            'total_pipeline_value' => $data->sum('total_pipeline_value'),
            'total_revenue' => $data->sum('total_revenue'),
            'average_deal_size' => $data->avg('avg_deal_size'),
        ];
    }

    /**
     * Calculate task completion summary
     */
    protected function calculateTaskCompletionSummary($data): array
    {
        return [
            'total_tasks' => $data->count(),
            'completed_tasks' => $data->where('status', 'completed')->count(),
            'completion_rate' => $data->where('status', 'completed')->count() / max($data->count(), 1) * 100,
            'tasks_by_status' => $data->groupBy('status')->map->count(),
            'tasks_by_priority' => $data->groupBy('priority_name')->map->count(),
        ];
    }

    /**
     * Calculate revenue analysis summary
     */
    protected function calculateRevenueAnalysisSummary($data): array
    {
        return [
            'total_revenue' => $data->sum('total_amount'),
            'total_deals' => $data->count(),
            'average_deal_value' => $data->avg('total_amount'),
            'revenue_by_payment_status' => $data->groupBy('payment_status')->map->sum('total_amount'),
            'revenue_by_team' => $data->groupBy('team_name')->map->sum('total_amount'),
        ];
    }

    /**
     * Calculate opportunity pipeline summary
     */
    protected function calculateOpportunityPipelineSummary($data): array
    {
        return [
            'total_opportunities' => $data->count(),
            'total_pipeline_value' => $data->sum('deal_value'),
            'weighted_pipeline_value' => $data->sum('weighted_value'),
            'opportunities_by_stage' => $data->groupBy('stage_name')->map->count(),
            'pipeline_value_by_stage' => $data->groupBy('stage_name')->map->sum('deal_value'),
        ];
    }

    /**
     * Calculate contact management summary
     */
    protected function calculateContactManagementSummary($data): array
    {
        return [
            'total_contacts' => $data->count(),
            'contacts_by_source' => $data->groupBy('source_name')->map->count(),
            'contacts_by_status' => $data->groupBy('status')->map->count(),
            'contacts_by_country' => $data->groupBy('country_name')->map->count(),
        ];
    }

    /**
     * Calculate product performance summary
     */
    protected function calculateProductPerformanceSummary($data): array
    {
        return [
            'total_products' => $data->count(),
            'total_revenue' => $data->sum('total_revenue'),
            'total_quantity_sold' => $data->sum('total_quantity'),
            'average_price' => $data->avg('avg_price'),
            'revenue_by_category' => $data->groupBy('category_name')->map->sum('total_revenue'),
        ];
    }

    /**
     * Calculate forecasting summary
     */
    protected function calculateForecastingSummary($data): array
    {
        return [
            'total_opportunities' => $data->count(),
            'total_pipeline_value' => $data->sum('deal_value'),
            'weighted_pipeline_value' => $data->sum('weighted_value'),
            'forecast_by_stage' => $data->groupBy('stage_name')->map->sum('weighted_value'),
            'expected_close_by_month' => $data->groupBy(function ($item) {
                return Carbon::parse($item->expected_close_date)->format('Y-m');
            })->map->sum('weighted_value'),
        ];
    }
}
