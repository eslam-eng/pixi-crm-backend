<?php

namespace App\DTO\Central;

use App\DTO\BaseDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ShopifyCallbackDTO extends BaseDTO
{
    public function __construct(
        public readonly string $hmac,
        public readonly string $shop,
        public readonly string $code,
        public readonly string $state,
        public readonly array $requestData
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            hmac: Arr::get($data, 'hmac'),
            shop: Arr::get($data, 'shop'),
            code: Arr::get($data, 'code'),
            state: Arr::get($data, 'state'),
            requestData: Arr::get($data, 'requestData', []),
        );
    }

    public static function fromRequest(Request $request): static
    {
        return new self(
            hmac: $request->get('hmac'),
            shop: $request->get('shop'),
            code: $request->get('code'),
            state: $request->get('state'),
            requestData: $request->except('hmac')
        );
    }

    public function toArray(): array
    {
        return [];
    }
}
