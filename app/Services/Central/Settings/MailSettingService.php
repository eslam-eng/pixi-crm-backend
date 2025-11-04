<?php

namespace App\Services\Central\Settings;

use App\Models\Central\Settings\MailSetting;
use App\Services\AbstractMailSettingService;

class MailSettingService extends AbstractMailSettingService
{
    protected function connectionName(): string
    {
        return 'landlord';
    }

    protected function getMailSettingModel(): string
    {
        return MailSetting::class;
    }
}
