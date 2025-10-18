<?php

namespace App\DTO\Report;

use Illuminate\Http\Request;

class ContactManagementReportDTO
{
    public function __construct(
        public ?string $date_from = null,
        public ?string $date_to = null,
        public ?array $user_ids = null,
        public ?array $source_ids = null,
        public ?array $country_ids = null,
        public ?array $city_ids = null,
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
            source_ids: $data['source_ids'] ?? null,
            country_ids: $data['country_ids'] ?? null,
            city_ids: $data['city_ids'] ?? null,
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
            source_ids: $request->input('source_ids'),
            country_ids: $request->input('country_ids'),
            city_ids: $request->input('city_ids'),
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
            'source_ids' => $this->source_ids,
            'country_ids' => $this->country_ids,
            'city_ids' => $this->city_ids,
            'search' => $this->search,
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
     * Check if source filter is specified
     */
    public function hasSourceFilter(): bool
    {
        return !empty($this->source_ids);
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
