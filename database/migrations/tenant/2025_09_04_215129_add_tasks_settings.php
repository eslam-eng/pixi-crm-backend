<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('tasks_settings.enable_escalation', true);
        $this->migrator->add('tasks_settings.escalation_time_hours', 24);
        $this->migrator->add('tasks_settings.default_followers_users', []); // save here json of array of ids
        $this->migrator->add('tasks_settings.notify_manager', false);
        $this->migrator->add('tasks_settings.mail_notification', false);
        $this->migrator->add('tasks_settings.system_notification', true);
    }
};
