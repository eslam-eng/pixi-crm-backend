<?php

namespace App\DTO\Report;

use App\DTO\BaseDTO;
use Illuminate\Http\Request;

class LeadManagementReportDTO extends BaseDTO
{
    public function __construct(
        public ?string $date_from = null,
        public ?string $date_to = null,
        public ?array $user_ids = null,
        public ?array $team_ids = null,
        public ?array $lead_statuses = null,
        public ?array $sources = null,
        public ?array $lifecycle_stages = null,
        public ?string $score_range_min = null,
        public ?string $score_range_max = null,
        public ?string $search = null,
        public ?string $group_by = null,
        public ?string $sort_by = null,
        public ?string $sort_direction = 'desc',
        public ?int $per_page = 15,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            date_from: $data['date_from'] ?? null,
            date_to: $data['date_to'] ?? null,
            user_ids: $data['user_ids'] ?? null,
            team_ids: $data['team_ids'] ?? null,
            lead_statuses: $data['lead_statuses'] ?? null,
            sources: $data['sources'] ?? null,
            lifecycle_stages: $data['lifecycle_stages'] ?? null,
            score_range_min: $data['score_range_min'] ?? null,
            score_range_max: $data['score_range_max'] ?? null,
            search: $data['search'] ?? null,
            group_by: $data['group_by'] ?? null,
            sort_by: $data['sort_by'] ?? null,
            sort_direction: $data['sort_direction'] ?? 'desc',
            per_page: $data['per_page'] ?? 15,
        );
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            date_from: $request->input('date_from'),
            date_to: $request->input('date_to'),
            user_ids: $request->input('user_ids'),
            team_ids: $request->input('team_ids'),
            lead_statuses: $request->input('lead_statuses'),
            sources: $request->input('sources'),
            lifecycle_stages: $request->input('lifecycle_stages'),
            score_range_min: $request->input('score_range_min'),
            score_range_max: $request->input('score_range_max'),
            search: $request->input('search'),
            group_by: $request->input('group_by'),
            sort_by: $request->input('sort_by'),
            sort_direction: $request->input('sort_direction', 'desc'),
            per_page: $request->input('per_page', 15),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'user_ids' => $this->user_ids,
            'team_ids' => $this->team_ids,
            'lead_statuses' => $this->lead_statuses,
            'sources' => $this->sources,
            'lifecycle_stages' => $this->lifecycle_stages,
            'score_range_min' => $this->score_range_min,
            'score_range_max' => $this->score_range_max,
            'search' => $this->search,
            'group_by' => $this->group_by,
            'sort_by' => $this->sort_by,
            'sort_direction' => $this->sort_direction,
            'per_page' => $this->per_page,
        ], fn($value) => $value !== null);
    }

    /**
     * Get score range as array
     */
    public function getScoreRange(): array
    {
        return [
            'min' => $this->score_range_min ? (int) $this->score_range_min : null,
            'max' => $this->score_range_max ? (int) $this->score_range_max : null,
        ];
    }

    /**
     * Check if score range filter is specified
     */
    public function hasScoreRange(): bool
    {
        return $this->score_range_min !== null || $this->score_range_max !== null;
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
        return !empty($this->lead_statuses);
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
