<?php

namespace App\DTO\Central;

use App\DTO\BaseDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class FeatureSubscriptionDTO extends BaseDTO
{
    public function __construct(
        public int $subscription_id,
        public int $feature_id,
        public string $slug,
        public string|array $name,
        public string $group,
        public $value = null,
        public int $usage = 0,
    ) {
    }

    public static function fromArray(array $data): static
    {
        return new self(
            subscription_id: Arr::get($data, 'subscription_id'),
            feature_id: Arr::get($data, 'feature_id'),
            slug: Arr::get($data, 'slug'),
            name: Arr::get($data, 'name'),
            group: Arr::get($data, 'group'),
            value: Arr::get($data, 'value'),
            usage: Arr::get($data, 'usage', 0),
        );
    }

    public static function fromRequest(Request $request): static
    {
        return new self(
            subscription_id: $request->subscription_id,
            feature_id: $request->feature_id,
            slug: $request->slug,
            name: $request->name,
            group: $request->group,
            value: $request->value,
            usage: $request->usage ?? 0,
        );
    }

    public function toArray(): array
    {
        return [
            'subscription_id' => $this->subscription_id,
            'feature_id' => $this->feature_id,
            'slug' => $this->slug,
            'name' => $this->name,
            'group' => $this->group,
            'value' => $this->value,
            'usage' => $this->usage,
        ];
    }
}
