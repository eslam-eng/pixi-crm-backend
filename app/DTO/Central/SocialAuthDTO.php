<?php

namespace App\DTO\Central;

use App\DTO\BaseDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SocialAuthDTO extends BaseDTO
{
    public function __construct(
        public string $provider_name,
        public string $access_token,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            provider_name: Arr::get($data, 'provider_name'),
            access_token: Arr::get($data, 'access_token'),
        );
    }

    public static function fromRequest(Request $request): static
    {
        return new self(
            provider_name: $request->provider_name,
            access_token: $request->access_token,
        );
    }

    public function toArray(): array
    {
        return [
            'provider_name' => $this->provider_name,
            'access_token' => $this->access_token,
        ];
    }
}
