<?php

namespace App\Services\Central;

use App\DTO\Central\MailSettingsDTO;
use Illuminate\Support\Facades\DB;

abstract class AbstractMailSettingService
{
    abstract protected function connectionName(): string;

    abstract protected function getMailSettingModel(): string;

    /**
     * @throws \Throwable
     */
    public function handle(MailSettingsDTO $mailSettingsDTO, $mailSetting)
    {
        return DB::connection($this->connectionName())
            ->transaction(function () use ($mailSettingsDTO, $mailSetting) {
                $mailSetting->fill($mailSettingsDTO->toArray());
                $mailSetting->save();

                self::setMailDriver($mailSetting);

                return $mailSetting;
            });
    }

    public static function setMailDriver($mailSetting): void
    {
        config([
            'mail.mailers.smtp.host' => $mailSetting->smtp_host,
            'mail.mailers.smtp.port' => $mailSetting->smtp_port,
            'mail.mailers.smtp.username' => $mailSetting->mail_username,
            'mail.mailers.smtp.password' => $mailSetting->mail_password,
            'mail.mailers.smtp.encryption' => $mailSetting->encryption ?? null,
            'mail.from.address' => $mailSetting->from_email_address,
            'mail.from.name' => $mailSetting->from_name,
        ]);
    }
}
