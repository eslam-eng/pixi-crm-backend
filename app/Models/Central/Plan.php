<?php

namespace App\Models\Central;

use App\Enums\Landlord\ActivationStatusEnum;
use App\Enums\Landlord\FeatureGroupEnum;
use App\Traits\HasTranslatedFallback;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
    use Filterable, HasFactory, HasTranslatedFallback, HasTranslations, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'refund_period',
        'duration',
        'duration_unit',
        'price',
        'is_active',
    ];

    public $translatable = ['name', 'description'];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_unit' => 'string',
        'duration' => 'integer',
        'is_active' => ActivationStatusEnum::class,
    ];

    public function features()
    {
        return $this->belongsToMany(Feature::class, 'feature_plans')
            ->withPivot(['value', 'is_unlimited'])
            ->withTimestamps()
            ->using(FeaturePlan::class);
    }

    public function limitFeatures(): BelongsToMany
    {
        return $this->features()
            ->where('group', FeatureGroupEnum::LIMIT->value);
    }

    public function addonFeatures(): BelongsToMany
    {
        return $this->features()
            ->where('group', FeatureGroupEnum::FEATURE->value);
    }

    public function scopeTrial($query)
    {
        return $query->where('trial_days', '>', 0);
    }

    /**
     * Get feature value for specific feature key
     */
    public function getFeatureValue($featureSlug)
    {
        $feature = $this->features()->where('slug', $featureSlug)->first();

        return $feature ? $feature->pivot->value : null;
    }

    /**
     * Check if plan includes feature
     */
    public function hasFeature($featureSlug)
    {
        return $this->features()->where('slug', $featureSlug)->exists();
    }
}
