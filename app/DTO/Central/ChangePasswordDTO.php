<?php

namespace App\DTO\Central;

use App\DTO\BaseDTO;
use App\Models\Landlord\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ChangePasswordDTO extends BaseDTO
{
    public function __construct(
        public User $user,
        public string $password,
        public bool $logout_other_devices = false,

    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            user: Arr::get($data, 'user'),
            password: Arr::get($data, 'password'),
            logout_other_devices: Arr::get($data, 'logout_other_devices'),

        );
    }

    public static function fromRequest(Request $request): static
    {
        return new self(
            user: $request->user,
            password: $request->password,
            logout_other_devices: $request->logout_other_devices,

        );
    }

    public function toArray(): array
    {
        return [
            'user' => $this->user,
            'password' => $this->password,
            'logout_other_devices' => $this->logout_other_devices,
        ];
    }
}
