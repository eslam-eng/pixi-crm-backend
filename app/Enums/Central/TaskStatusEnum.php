<?php

namespace App\Enums\Central;

enum TaskStatusEnum: int
{
    case PENDING = 0;
    case IN_PROGRESS = 1;
    case COMPLETED = 2;
    case OVERDUE = 3;
    case CANCELLED = 4;

    public function getLabel(?string $locale = null): string
    {
        $key = match ($this) {
            self::PENDING => 'pending',
            self::IN_PROGRESS => 'in_progress',
            self::COMPLETED => 'completed',
            self::OVERDUE => 'overdue',
            self::CANCELLED => 'cancelled',
        };

        return __("app.task_status.$key");
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
