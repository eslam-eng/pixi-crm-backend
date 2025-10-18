<?php

namespace App\DTO\Report;

use Illuminate\Http\Request;

class RevenueAnalysisReportDTO
{
    public function __construct(
        public ?string $date_from = null,
        public ?string $date_to = null,
        public ?array $user_ids = null,
        public ?array $team_ids = null,
        public ?array $product_ids = null,
        public ?array $customer_segments = null,
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
            product_ids: $data['product_ids'] ?? null,
            customer_segments: $data['customer_segments'] ?? null,
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
            product_ids: $request->input('product_ids'),
            customer_segments: $request->input('customer_segments'),
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
            'product_ids' => $this->product_ids,
            'customer_segments' => $this->customer_segments,
            'group_by' => $this->group_by,
            'sort_by' => $this->sort_by,
            'sort_direction' => $this->sort_direction,
            'per_page' => $this->per_page,
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
     * Check if search is specified
     */
    public function hasSearch(): bool
    {
        return false; // Revenue analysis doesn't use search
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
