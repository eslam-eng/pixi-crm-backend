<?php

namespace App\DTO\Tenant;

use App\DTO\BaseDTO;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

class UserUpdateProfileDTO extends BaseDTO
{
    public function __construct(
        public readonly string $first_name,
        public readonly string $last_name,
        public readonly UploadedFile|null $profile_image

    ) {}

    public static function fromRequest($request): UserUpdateProfileDTO
    {
        return new self(
            first_name: $request->first_name,
            last_name: $request->last_name,
            profile_image: $request->profile_image,
        );
    }

    public static function fromArray(array $data): UserUpdateProfileDTO
    {
        return new self(
            first_name: Arr::get($data, 'first_name'),
            last_name: Arr::get($data, 'last_name'),
            profile_image: Arr::get($data, 'profile_image'),
        );
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
        ];
    }

    public function hasProfileImage(): bool
    {
        return $this->profile_image instanceof UploadedFile;
    }
}
