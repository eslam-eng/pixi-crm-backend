<?php

namespace App\DTO\Tenant;

use App\DTO\BaseDTO;
use Illuminate\Support\Arr;

class UserDTO extends BaseDTO
{
    /**
     * @param string $first_name',
     * @param string $last_name',
     * @param string $email',
     * @param ?string $password',
     * @param ?string $phone',
     * @param ?string $job_title',
     * @param ?string $role',
     * @param ?int $department_id',
     * @param ?int $last_login_at',
     * @param ?string $lang',
     * @param ?bool $is_active',
     */

    public function __construct(
        public readonly string $first_name,
        public readonly string $last_name,
        public readonly string $email,
        public readonly ?string $password,
        public readonly ?string $phone,
        public readonly ?string $job_title,
        public readonly ?string $role,
        public readonly ?int $department_id,
        public readonly ?int $last_login_at,
        public readonly ?string $lang,
        public readonly ?bool $is_active = true,
        // public readonly ?array $monthly_target = [],
        // public readonly ?array $quarterly_target = [],
        public readonly ?string $period_type,
        public readonly ?array $targets,
        public readonly bool $requires_clock,
    ) {}

    public static function fromRequest($request): UserDTO
    {
        return new self(
            first_name: $request->first_name,
            last_name: $request->last_name,
            email: $request->email,
            password: $request->password,
            phone: $request->phone,
            job_title: $request->job_title,
            role: $request->role,
            department_id: $request->department_id,
            last_login_at: $request->last_login_at,
            lang: $request->lang,
            is_active: $request->is_active ?? true,
            period_type: $request->period_type,
            targets: $request->targets,
            requires_clock: $request->requires_clock
        );
    }

    public static function fromArray(array $data): UserDTO
    {
        return new self(
            first_name: Arr::get($data, 'first_name'),
            last_name: Arr::get($data, 'last_name'),
            email: Arr::get($data, 'email'),
            password: Arr::get($data, 'password'),
            phone: Arr::get($data, 'phone'),
            job_title: Arr::get($data, 'job_title'),
            role: Arr::get($data, 'role'),
            department_id: Arr::get($data, 'department_id'),
            last_login_at: Arr::get($data, 'last_login_at'),
            lang: Arr::get($data, 'lang'),
            is_active: Arr::get($data, 'is_active', true),
            // monthly_target: Arr::get($data, 'monthly_target', []),
            // quarterly_target: Arr::get($data, 'quarterly_target', []),
            period_type: Arr::get($data, 'period_type'),
            targets: Arr::get($data, 'targets'),
            requires_clock: Arr::get($data, 'requires_clock'),
        );
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'password' => $this->password,
            'phone' => $this->phone,
            'job_title' => $this->job_title,
            'department_id' => $this->department_id,
            'last_login_at' => $this->last_login_at,
            'lang' => $this->lang,
            'is_active' => $this->is_active,
            'requires_clock' => $this->requires_clock,
        ];
    }
}
