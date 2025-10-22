<?php

namespace App\Enums\Central;

enum DayOfWeekEnum: string
{
    case MONDAY = 'monday';
    case TUESDAY = 'tuesday';
    case WEDNESDAY = 'wednesday';
    case THURSDAY = 'thursday';
    case FRIDAY = 'friday';
    case SATURDAY = 'saturday';
    case SUNDAY = 'sunday';

    public function getLabel(): string
    {
        return match ($this) {
            self::MONDAY => __('app.days.monday'),
            self::TUESDAY => __('app.days.tuesday'),
            self::WEDNESDAY => __('app.days.wednesday'),
            self::THURSDAY => __('app.days.thursday'),
            self::FRIDAY => __('app.days.friday'),
            self::SATURDAY => __('app.days.saturday'),
            self::SUNDAY => __('app.days.sunday'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
