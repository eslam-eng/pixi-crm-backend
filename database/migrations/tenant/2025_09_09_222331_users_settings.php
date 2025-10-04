<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // General Users Settings
        $this->migrator->add('users_settings.default_send_email_notifications', true);
    }
};
