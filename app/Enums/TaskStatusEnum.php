<?php

namespace App\Enums;

enum TaskStatusEnum: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case on_hold = 'on_hold';
    case overdue = 'overdue';


    public function label(): string
    {
        return match ($this) {
            static::PENDING => __('app.task_status.pending'),
            static::IN_PROGRESS => __('app.task_status.in_progress'),
            static::COMPLETED => __('app.task_status.completed'),
            static::CANCELLED => __('app.task_status.cancelled'),
            static::on_hold => __('app.task_status.on_hold'),
            static::overdue => __('app.task_status.overdue'),
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
