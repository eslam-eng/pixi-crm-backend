<?php

namespace App\DTO\Central;

use App\DTO\BaseDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ClientDTO extends BaseDTO
{
    public function __construct(
        public string $contact_name,
        public string $contact_email,
        public ?string $password = '123456',
        public string $subdomain,
        public int $package_id,
        public string $period_type,
        public string $subscription_start,
    ) {
    }

    public static function fromArray(array $data): static
    {
        return new self(
            contact_name: Arr::get($data, 'contact_name'),
            contact_email: Arr::get($data, 'contact_email'),
            password: Arr::get($data, 'password'),
            subdomain: Arr::get($data, 'subdomain'),
            package_id: Arr::get($data, 'package_id'),
            period_type: Arr::get($data, 'period_type'),
            subscription_start: Arr::get($data, 'subscription_start'),
        );
    }

    public static function fromRequest(Request $request): static
    {
        return new self(
            contact_name: $request->contact_name,
            contact_email: $request->contact_email,
            password: $request->password,
            subdomain: $request->subdomain,
            package_id: $request->package_id,
            period_type: $request->period_type,
            subscription_start: $request->subscription_start,
        );
    }

    public function toArray(): array
    {
        return [
            'contact_name' => $this->contact_name,
            'contact_email' => $this->contact_email,
            'password' => $this->password,
            'subdomain' => $this->subdomain,
        ];
    }

    public function toUserArray(): array
    {
        return [
            'first_name' => $this->contact_name,
            'last_name' => $this->contact_name,
            'email' => $this->contact_email,
            'password' => $this->password,
        ];
    }
}
