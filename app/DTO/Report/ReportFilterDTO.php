<?php

namespace App\DTO\Report;

use App\DTO\BaseDTO;
use Illuminate\Http\Request;

class ReportFilterDTO
{
    public function __construct(
        public ?string $date_from = null,
        public ?string $date_to = null,
        public ?array $user_ids = null,
        public ?array $team_ids = null,
        public ?array $stage_ids = null,
        public ?array $statuses = null,
        public ?array $sources = null,
        public ?string $search = null,
        public ?string $group_by = null,
        public ?string $sort_by = null,
        public ?string $sort_direction = 'asc',
        public ?int $per_page = 15,
        public ?array $custom_filters = null,
    ) {}

    public static function fromArray(SalesPerformanceReportDTO $salesPerformanceReportDTO): self
    {
        return new self(
            date_from: $salesPerformanceReportDTO->date_from,
            date_to: $salesPerformanceReportDTO->date_to,
            user_ids: $salesPerformanceReportDTO->user_ids,
            team_ids: $salesPerformanceReportDTO->team_ids,
        );
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            date_from: $request->input('date_from'),
            date_to: $request->input('date_to'),
            user_ids: $request->input('user_ids'),
            team_ids: $request->input('team_ids'),
            stage_ids: $request->input('stage_ids'),
            statuses: $request->input('statuses'),
            sources: $request->input('sources'),
            search: $request->input('search'),
            group_by: $request->input('group_by'),
            sort_by: $request->input('sort_by'),
            sort_direction: $request->input('sort_direction', 'asc'),
            per_page: $request->input('per_page', 15),
            custom_filters: $request->input('custom_filters'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'user_ids' => $this->user_ids,
            'team_ids' => $this->team_ids,
            'stage_ids' => $this->stage_ids,
            'statuses' => $this->statuses,
            'sources' => $this->sources,
            'search' => $this->search,
            'group_by' => $this->group_by,
            'sort_by' => $this->sort_by,
            'sort_direction' => $this->sort_direction,
            'per_page' => $this->per_page,
            'custom_filters' => $this->custom_filters,
        ], fn($value) => $value !== null);
    }

    /**
     * Get date range as Carbon instances
     */
    public function getDateRange(): array
    {
        return [
            'from' => $this->date_from ? \Carbon\Carbon::parse($this->date_from) : null,
            'to' => $this->date_to ? \Carbon\Carbon::parse($this->date_to) : null,
        ];
    }

    /**
     * Check if date range is specified
     */
    public function hasDateRange(): bool
    {
        return $this->date_from !== null || $this->date_to !== null;
    }

    /**
     * Check if user filter is specified
     */
    public function hasUserFilter(): bool
    {
        return !empty($this->user_ids);
    }

    /**
     * Check if team filter is specified
     */
    public function hasTeamFilter(): bool
    {
        return !empty($this->team_ids);
    }

    /**
     * Check if stage filter is specified
     */
    public function hasStageFilter(): bool
    {
        return !empty($this->stage_ids);
    }

    /**
     * Check if status filter is specified
     */
    public function hasStatusFilter(): bool
    {
        return !empty($this->statuses);
    }

    /**
     * Check if source filter is specified
     */
    public function hasSourceFilter(): bool
    {
        return !empty($this->sources);
    }

    /**
     * Check if search is specified
     */
    public function hasSearch(): bool
    {
        return !empty($this->search);
    }

    /**
     * Check if grouping is specified
     */
    public function hasGrouping(): bool
    {
        return !empty($this->group_by);
    }

    /**
     * Check if sorting is specified
     */
    public function hasSorting(): bool
    {
        return !empty($this->sort_by);
    }
}
