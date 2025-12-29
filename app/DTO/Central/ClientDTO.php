<?php

namespace App\DTO\Central;

use App\DTO\BaseDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ClientDTO extends BaseDTO
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public string $company_name,
        public string $email,
        public ?string $job_title = null,
        public ?string $website = null,
        public ?int $city_id = null,
        public ?string $company_size = null,
        public ?int $industry_id = null,
        public ?string $postal_code = null,
        public ?string $address = null,
        public ?string $password = null,
        public ?string $phone = null,
        public string $domain,
        public ?int $plan_id = null,
        public ?string $period_type = null,
        public ?string $subscription_start = null,
        public ?string $activation_code = null,
        public ?bool $create_free_trial = false,
    ) {
    }

    public static function fromArray(array $data): static
    {
        return new self(
            first_name: Arr::get($data, 'first_name'),
            last_name: Arr::get($data, 'last_name'),
            company_name: Arr::get($data, 'company_name'),
            email: Arr::get($data, 'email'),
            job_title: Arr::get($data, 'job_title'),
            website: Arr::get($data, 'website'),
            city_id: Arr::get($data, 'city_id'),
            company_size: Arr::get($data, 'company_size'),
            industry_id: Arr::get($data, 'industry_id'),
            postal_code: Arr::get($data, 'postal_code'),
            address: Arr::get($data, 'address'),
            password: Arr::get($data, 'password'),
            phone: Arr::get($data, 'phone'),
            domain: Arr::get($data, 'domain'),
            plan_id: Arr::get($data, 'plan_id'),
            period_type: Arr::get($data, 'period_type'),
            subscription_start: Arr::get($data, 'subscription_start'),
        );
    }

    public static function fromRequest(Request $request): static
    {
        return new self(
            first_name: $request->first_name,
            last_name: $request->last_name,
            company_name: $request->company_name,
            email: $request->email,
            job_title: $request->job_title,
            website: $request->website,
            city_id: $request->city_id,
            company_size: $request->company_size,
            industry_id: $request->industry_id,
            postal_code: $request->postal_code,
            address: $request->address,
            password: $request->password,
            phone: $request->phone,
            domain: $request->domain,
            plan_id: $request->plan_id,
            period_type: $request->period_type,
            subscription_start: $request->subscription_start,
        );
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'company_name' => $this->company_name,
            'email' => $this->email,
            'job_title' => $this->job_title,
            'website' => $this->website,
            'city_id' => $this->city_id,
            'company_size' => $this->company_size,
            'industry_id' => $this->industry_id,
            'postal_code' => $this->postal_code,
            'address' => $this->address,
            'password' => $this->password,
            'phone' => $this->phone,
            'domain' => $this->domain,
            'plan_id' => $this->plan_id,
            'period_type' => $this->period_type,
            'subscription_start' => $this->subscription_start,
        ];
    }

    public function toUserArray(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
