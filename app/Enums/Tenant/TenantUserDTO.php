<?php

namespace App\DTO\Tenant;

use App\DTO\BaseDTO;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role as ModelsRole;

class TenantUserDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public int|ModelsRole $role,
        public ?string $phone = null,
        public ?string $department_id = null,
        public ?string $password = null,
        public int|User|null $landlordUser = null,
        public ?string $email_verified_at = null,
        public ?bool $send_credential_email = false,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            name: Arr::get($data, 'name'),
            email: Arr::get($data, 'email'),
            role: Arr::get($data, 'role'),
            phone: Arr::get($data, 'phone'),
            department_id: Arr::get($data, 'department_id'),
            landlordUser: Arr::get($data, 'landlordUser'),
        );
    }

    public static function fromRequest(Request $request): static
    {
        return new self(
            name: $request->name,
            email: $request->email,
            role: $request->role,
            phone: $request->phone,
            department_id: $request->department_id,
            landlordUser: $request->landlordUser,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'department_id' => $this->department_id,
            'landlord_user_id' => $this->landlordUser?->id,
            'email_verified_at' => $this->email_verified_at,
        ];
    }
}
