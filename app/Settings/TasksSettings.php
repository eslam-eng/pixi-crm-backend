<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class TasksSettings extends Settings
{
    public bool $enable_escalation;
    public bool $enable_priority;
    public int $escalation_time_hours;
    public ?array $default_followers_users;
    public bool $notify_manager;

    public static function group(): string
    {
        return 'tasks_settings';
    }

    public static function defaults(): array
    {
        return [
            'enable_escalation' => true,
            'enable_priority' => true,
            'escalation_time_hours' => 24,
            'default_followers_users' => [],
            'notify_manager' => false,
        ];
    }

}