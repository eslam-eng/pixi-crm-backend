<?php

namespace App\DTO\Central;

use App\DTO\BaseDTO;
use Illuminate\Support\Arr;

class MailSettingsDTO extends BaseDTO
{
    public function __construct(
        public ?string $smtp_host = null,
        public ?string $smtp_port = null,
        //        public ?string   $encryption = null,
        public ?string $mail_username = null,
        public ?string $mail_password = null,
        public ?string $from_email_address = null,
        public ?string $from_name = null,
    ) {}

    public static function fromRequest($request): static
    {
        return new self(
            smtp_host: $request->smtp_host ?: null,
            smtp_port: $request->smtp_port ?: null,
            //            encryption: $request->encryption ?: null,
            mail_username: $request->mail_username ?: null,
            mail_password: $request->mail_password ?: null,
            from_email_address: $request->from_email_address ?: null,
            from_name: $request->from_name ?: null,
        );
    }

    /**
     * @return $this
     */
    public static function fromArray(array $data): static
    {
        return new self(
            smtp_host: Arr::get($data, 'smtp_host'),
            smtp_port: Arr::get($data, 'smtp_port'),
            //            encryption: Arr::get($data, 'encryption'),
            mail_username: Arr::get($data, 'mail_username'),
            mail_password: Arr::get($data, 'mail_password'),
            from_email_address: Arr::get($data, 'from_email_address'),
            from_name: Arr::get($data, 'from_name'),
        );
    }

    public function toArray(): array
    {
        return [

            'smtp_host' => $this->smtp_host,
            'smtp_port' => $this->smtp_port,
            //            'encryption' => $this->encryption,
            'mail_username' => $this->mail_username,
            'mail_password' => $this->mail_password,
            'from_email_address' => $this->from_email_address,
            'from_name' => $this->from_name,
        ];
    }
}
