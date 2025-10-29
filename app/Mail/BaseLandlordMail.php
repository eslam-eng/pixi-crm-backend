<?php

namespace App\Mail;

use App\Models\Central\Settings\MailSetting;
use App\Services\Central\Settings\MailSettingService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BaseLandlordMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct()
    {
        $tenantSettings = app(MailSetting::class);
        MailSettingService::setMailDriver($tenantSettings);
    }
}
