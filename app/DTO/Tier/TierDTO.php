<?php

namespace App\DTO\Tier;

use App\DTO\BaseDTO;
use App\DTO\Interfaces\DTOInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class TierDTO extends BaseDTO implements DTOInterface
{
    public function __construct(
        protected string $package_name,
        protected ?string $description = null,
        protected float $price = 0.0,
        protected string $duration_unit = 'month',
        protected int $duration = 1,
        protected int $refund_period = 0,
        protected ?int $max_users = null,
        protected ?int $max_contacts = null,
        protected ?int $storage_limit = null,
        protected array $modules = [],
        protected string $status = 'active',
        protected string $availability = 'Public'
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            package_name: $request->package_name,
            description: $request->description,
            price: (float) $request->price,
            duration_unit: $request->duration_unit,
            duration: (int) $request->duration,
            refund_period: (int) $request->refund_period,
            max_users: $request->max_users ? (int) $request->max_users : null,
            max_contacts: $request->max_contacts ? (int) $request->max_contacts : null,
            storage_limit: $request->storage_limit ? (int) $request->storage_limit : null,
            modules: $request->modules ?? [],
            status: $request->status,
            availability: $request->availability
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            package_name: Arr::get($data, 'package_name'),
            description: Arr::get($data, 'description'),
            price: (float) Arr::get($data, 'price', 0),
            duration_unit: Arr::get($data, 'duration_unit', 'month'),
            duration: (int) Arr::get($data, 'duration', 1),
            refund_period: (int) Arr::get($data, 'refund_period', 0),
            max_users: Arr::get($data, 'max_users') ? (int) Arr::get($data, 'max_users') : null,
            max_contacts: Arr::get($data, 'max_contacts') ? (int) Arr::get($data, 'max_contacts') : null,
            storage_limit: Arr::get($data, 'storage_limit') ? (int) Arr::get($data, 'storage_limit') : null,
            modules: Arr::get($data, 'modules', []),
            status: Arr::get($data, 'status', 'active'),
            availability: Arr::get($data, 'availability', 'Public')
        );
    }

    public function toArray(): array
    {
        return [
            'package_name' => $this->package_name,
            'description' => $this->description,
            'price' => $this->price,
            'duration_unit' => $this->duration_unit,
            'duration' => $this->duration,
            'refund_period' => $this->refund_period,
            'max_users' => $this->max_users,
            'max_contacts' => $this->max_contacts,
            'storage_limit' => $this->storage_limit,
            'modules' => $this->modules,
            'status' => $this->status,
            'availability' => $this->availability,
        ];
    }

    // Getters
    public function getPackageName(): string
    {
        return $this->package_name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getDurationUnit(): string
    {
        return $this->duration_unit;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function getRefundPeriod(): int
    {
        return $this->refund_period;
    }

    public function getMaxUsers(): ?int
    {
        return $this->max_users;
    }

    public function getMaxContacts(): ?int
    {
        return $this->max_contacts;
    }

    public function getStorageLimit(): ?int
    {
        return $this->storage_limit;
    }

    public function getModules(): array
    {
        return $this->modules;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getAvailability(): string
    {
        return $this->availability;
    }
} 