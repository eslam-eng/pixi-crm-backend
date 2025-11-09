<?php

namespace App\Models\Central\Settings;

use App\Models\Central\Settings\MailSetting;
use App\Services\Central\AbstractMailSettingService;

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
