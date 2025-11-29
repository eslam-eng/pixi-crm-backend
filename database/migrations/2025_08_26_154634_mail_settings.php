<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('mail_settings.smtp_host', 'smtp.gmail.com');
        $this->migrator->add('mail_settings.smtp_port', 587);
        $this->migrator->add('mail_settings.encryption', 'tls');
        $this->migrator->add('mail_settings.mail_username', 'neomrs.helpcenter@gmail.com');
        $this->migrator->add('mail_settings.mail_password', 'idhwwbjjvggfawwj');
        $this->migrator->add('mail_settings.from_email_address', 'neomrs.helpcenter@gmail.com');
        $this->migrator->add('mail_settings.from_name', 'Mijra');
    }
};
