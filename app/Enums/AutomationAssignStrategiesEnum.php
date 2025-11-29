<?php

namespace App\Enums;

enum AutomationAssignStrategiesEnum: string
{
    case SPASIFIC_USER = "spasific_user";
    case OWNER_CONTACT_USER = "owner_contact_user"; //SELECT FROM CONTACT
    case ROUND_ROBIN_SEQUENCE = "round_robin_sequence";
    case ROUND_ROBIN_RANDOM_ACTIVE_OPPORTUNITY = "round_robin_random_active_opportunity";
    case ROUND_ROBIN_RANDOM_ACTIVE_TASKS = "round_robin_random_active_tasks";
    case ROUND_ROBIN_RANDOM_PERFORMANCE = "round_robin_random_performance";
    case ROUND_ROBIN_RANDOM_BEST_SELLER = "round_robin_random_best_seller";

    public function label(): string
    {
        return match ($this) {
            self::SPASIFIC_USER => __('app.spasific_user'),
            self::OWNER_CONTACT_USER => __('app.owner_contact_user'),
            self::ROUND_ROBIN_SEQUENCE => __('app.round_robin_sequence'),
            self::ROUND_ROBIN_RANDOM_ACTIVE_OPPORTUNITY => __('app.round_robin_random_active_opportunity'),
            self::ROUND_ROBIN_RANDOM_ACTIVE_TASKS => __('app.round_robin_random_active_tasks'),
            self::ROUND_ROBIN_RANDOM_PERFORMANCE => __('app.round_robin_random_performance'),
            self::ROUND_ROBIN_RANDOM_BEST_SELLER => __('app.round_robin_random_best_seller'),
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}



