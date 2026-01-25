<?php

namespace App\Services\Central;

use App\DTO\Central\FeatureSubscriptionDTO;
use App\Models\Central\FeatureSubscription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FeatureSubscriptionService extends BaseService
{
    public function getFilterClass(): ?string
    {
        return null;
    }

    protected function baseQuery(): Builder
    {
        return FeatureSubscription::query();
    }

    /**
     * Create a new feature subscription using DTO.
     *
     * @param FeatureSubscriptionDTO $dto
     * @return bool
     */
    public function create(FeatureSubscriptionDTO $dto): bool
    {
        $data = $dto->toArray();
        $data['created_at'] = now();
        $data['updated_at'] = now();

        // Name needs to be encoded for insert() because it bypasses casting
        if (is_array($data['name'])) {
            $data['name'] = json_encode($data['name']);
        }

        return DB::connection('landlord')->table('feature_subscriptions')->insert($data);
    }

    /**
     * Create multiple feature subscriptions at once.
     *
     * @param array $dtos Array of FeatureSubscriptionDTO objects
     * @return bool
     */
    public function createMany(array $dtos): bool
    {
        $data = array_map(function (FeatureSubscriptionDTO $dto) {
            $array = $dto->toArray();
            $array['created_at'] = now();
            $array['updated_at'] = now();

            // Name needs to be encoded for insert() because it bypasses casting
            if (is_array($array['name'])) {
                $array['name'] = json_encode($array['name']);
            }

            return $array;
        }, $dtos);

        if (empty($data)) {
            return false;
        }

        return DB::connection('landlord')->table('feature_subscriptions')->insert($data);
    }
}
