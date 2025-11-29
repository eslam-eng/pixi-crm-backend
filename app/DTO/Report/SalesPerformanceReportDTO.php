<?php

namespace App\DTO\Report;

use App\DTO\BaseDTO;
use Illuminate\Http\Request;

class SalesPerformanceReportDTO extends BaseDTO
{
    public function __construct(
        public ?string $date_from = null,
        public ?string $date_to = null,
        public ?array $user_ids = null,
        public ?array $team_ids = null,
        public ?array $stage_ids = null,
        public ?array $deal_statuses = null,
        public ?array $sources = null,
        public ?string $value_range_min = null,
        public ?string $value_range_max = null,
        public ?string $probability_range_min = null,
        public ?string $probability_range_max = null,
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
            stage_ids: $data['stage_ids'] ?? null,
            deal_statuses: $data['deal_statuses'] ?? null,
            sources: $data['sources'] ?? null,
            value_range_min: $data['value_range_min'] ?? null,
            value_range_max: $data['value_range_max'] ?? null,
            probability_range_min: $data['probability_range_min'] ?? null,
            probability_range_max: $data['probability_range_max'] ?? null,
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
            stage_ids: $request->input('stage_ids'),
            deal_statuses: $request->input('deal_statuses'),
            sources: $request->input('sources'),
            value_range_min: $request->input('value_range_min'),
            value_range_max: $request->input('value_range_max'),
            probability_range_min: $request->input('probability_range_min'),
            probability_range_max: $request->input('probability_range_max'),
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
            'stage_ids' => $this->stage_ids,
            'deal_statuses' => $this->deal_statuses,
            'sources' => $this->sources,
            'value_range_min' => $this->value_range_min,
            'value_range_max' => $this->value_range_max,
            'probability_range_min' => $this->probability_range_min,
            'probability_range_max' => $this->probability_range_max,
            'search' => $this->search,
            'group_by' => $this->group_by,
            'sort_by' => $this->sort_by,
            'sort_direction' => $this->sort_direction,
            'per_page' => $this->per_page,
        ], fn($value) => $value !== null);
    }

    /**
     * Get value range as array
     */
    public function getValueRange(): array
    {
        return [
            'min' => $this->value_range_min ? (float) $this->value_range_min : null,
            'max' => $this->value_range_max ? (float) $this->value_range_max : null,
        ];
    }

    /**
     * Get probability range as array
     */
    public function getProbabilityRange(): array
    {
        return [
            'min' => $this->probability_range_min ? (float) $this->probability_range_min : null,
            'max' => $this->probability_range_max ? (float) $this->probability_range_max : null,
        ];
    }

    /**
     * Check if value range filter is specified
     */
    public function hasValueRange(): bool
    {
        return $this->value_range_min !== null || $this->value_range_max !== null;
    }

    /**
     * Check if probability range filter is specified
     */
    public function hasProbabilityRange(): bool
    {
        return $this->probability_range_min !== null || $this->probability_range_max !== null;
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
        return !empty($this->deal_statuses);
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
