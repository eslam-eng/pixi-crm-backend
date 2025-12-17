<?php

namespace App\DTO\Central;

use App\DTO\BaseDTO;
use App\Enums\Landlord\ActivationStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class AdminDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone = null,
        public ?string $password = null,
        public ?string $email_verified_at = null,
        public ?int $role_id = null,
        public ?int $department_id = null,
        public ?string $job_title = null,
        public bool $is_active = true,
    ) {
    }

    public static function fromArray(array $data): static
    {
        return new self(
            name: Arr::get($data, 'name'),
            email: Arr::get($data, 'email'),
            phone: Arr::get($data, 'phone'),
            password: Arr::get($data, 'password'),
            role_id: Arr::get($data, 'role_id'),
            department_id: Arr::get($data, 'department_id'),
            job_title: Arr::get($data, 'job_title'),
            is_active: Arr::get($data, 'is_active', ActivationStatusEnum::ACTIVE->value),
        );
    }

    public static function fromRequest(Request $request): static
    {
        return new self(
            name: $request->name,
            email: $request->email,
            phone: $request->phone,
            password: $request->password,
            role_id: $request->role_id,
            department_id: $request->department_id,
            job_title: $request->job_title,
            is_active: $request->is_active,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => $this->password,
            'email_verified_at' => $this->email_verified_at,
            'role_id' => $this->role_id,
            'department_id' => $this->department_id,
            'job_title' => $this->job_title,
            'is_active' => $this->is_active,
        ];
    }
}
