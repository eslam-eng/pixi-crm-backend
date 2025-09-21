<?php

namespace App\DTO\Central;

use App\DTO\BaseDTO;
use Illuminate\Support\Arr;

class UserDTO extends BaseDTO
{
    /**
     * @param string $first_name',
     * @param string $last_name',
     * @param string $email',
     * @param ?string $password',
     * @param string $phone',
     * @param ?string $role',
     * @param ?int $department_id',
     */
    
    public function __construct(
        public readonly string $first_name,
        public readonly string $last_name,
        public readonly string $email,
        public readonly ?string $password,
        public readonly string $phone,
        public readonly ?string $role,
        public readonly ?int $department_id,
        public readonly ?int $last_login_at,
        public readonly ?string $lang,
    ) {}

    public static function fromRequest($request): UserDTO
    {
        return new self(
            first_name: $request->first_name,
            last_name: $request->last_name,
            email: $request->email,
            password: $request->password,
            phone: $request->phone,
            role: $request->role,
            department_id: $request->department_id,
            last_login_at: $request->last_login_at,
            lang: $request->lang
        );
    }

    /**
     * @param array $data
     * @return $this
     */
    public static function fromArray(array $data): UserDTO
    {
        return new self(
            first_name: Arr::get($data, 'first_name'),
            last_name: Arr::get($data, 'last_name'),
            email: Arr::get($data, 'email'),
            password: Arr::get($data, 'password'),
            phone: Arr::get($data, 'phone'),
            role: Arr::get($data, 'role'),
            department_id: Arr::get($data, 'department_id'),
            last_login_at: Arr::get($data, 'last_login_at'),
            lang: Arr::get($data, 'lang')
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'password' => $this->password,
            'phone' => $this->phone,
            'role' => $this->role,
            'department_id' => $this->department_id,
            'last_login_at' => $this->last_login_at,
            'lang' => $this->lang
        ];
    }
}
