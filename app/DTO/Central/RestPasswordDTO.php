<?php

namespace App\DTO\Central;

use App\DTO\BaseDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class RestPasswordDTO extends BaseDTO
{
    public function __construct(public string $email, public string $code, public string $password) {}

    public static function fromArray(array $data): static
    {
        return new self(
            email: Arr::get($data, 'email'),
            code: Arr::get($data, 'code'),
            password: Arr::get($data, 'password')
        );
    }

    public static function fromRequest(Request $request): static
    {
        return new self(
            email: $request->email,
            code: $request->code,
            password: $request->password
        );
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'code' => $this->code,
            'password' => $this->password,
        ];
    }
}
