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
        'monthly_price',
        'annual_price',
        'lifetime_price',
        'is_active',
        'trial_days',
        'sort_order',
        'currency_code',
        'refund_days',
    ];

    public $translatable = ['name', 'description'];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'annual_price' => 'decimal:2',
        'lifetime_price' => 'decimal:2',
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
