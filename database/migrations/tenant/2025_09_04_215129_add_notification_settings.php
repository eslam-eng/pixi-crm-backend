<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {

        $this->migrator->add('notification_settings.mail_notification', false);
        $this->migrator->add('notification_settings.system_notification', true);
    }
};
