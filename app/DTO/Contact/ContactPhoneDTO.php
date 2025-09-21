<?php

namespace App\DTO\Contact;

class ContactPhoneDTO
{
    public function __construct(
        public readonly string $phone,
        public readonly bool $is_primary = false,
        public readonly bool $enable_whatsapp = false,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            phone: $data['phone'] ?? '',
            is_primary: (bool) ($data['is_primary'] ?? false),
            enable_whatsapp: (bool) ($data['enable_whatsapp'] ?? false),
        );
    }

    public function toArray(): array
    {
        return [
            'phone' => $this->phone,
            'is_primary' => $this->is_primary,
            'enable_whatsapp' => $this->enable_whatsapp,
        ];
    }
}
