<?php

namespace App\DTO\Report;

use App\DTO\BaseDTO;
use Illuminate\Http\Request;

class ReportDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public ?string $description,
        public string $report_type,
        public string $category,
        public bool $is_active = true,
        public bool $is_scheduled = false,
        public ?string $schedule_frequency = null,
        public ?string $schedule_time = null,
        public ?array $recipients = null,
        public ?int $created_by_id = null,
        public ?array $settings = null,
        public ?array $permissions = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'] ?? null,
            report_type: $data['report_type'],
            category: $data['category'],
            is_active: $data['is_active'] ?? true,
            is_scheduled: $data['is_scheduled'] ?? false,
            schedule_frequency: $data['schedule_frequency'] ?? null,
            schedule_time: $data['schedule_time'] ?? null,
            recipients: $data['recipients'] ?? null,
            created_by_id: $data['created_by_id'] ?? null,
            settings: $data['settings'] ?? null,
            permissions: $data['permissions'] ?? null,
        );
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            description: $request->input('description'),
            report_type: $request->input('report_type'),
            category: $request->input('category'),
            is_active: $request->boolean('is_active', true),
            is_scheduled: $request->boolean('is_scheduled', false),
            schedule_frequency: $request->input('schedule_frequency'),
            schedule_time: $request->input('schedule_time'),
            recipients: $request->input('recipients'),
            created_by_id: $request->input('created_by_id'),
            settings: $request->input('settings'),
            permissions: $request->input('permissions'),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'report_type' => $this->report_type,
            'category' => $this->category,
            'is_active' => $this->is_active,
            'is_scheduled' => $this->is_scheduled,
            'schedule_frequency' => $this->schedule_frequency,
            'schedule_time' => $this->schedule_time,
            'recipients' => $this->recipients,
            'created_by_id' => $this->created_by_id,
            'settings' => $this->settings,
            'permissions' => $this->permissions,
        ];
    }
}
