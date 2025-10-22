<?php

namespace App\Services\Landlord\Settings;

use App\Models\Settings\Landlord\MailSetting;
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
