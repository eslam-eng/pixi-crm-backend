<?php

namespace App\Models\Central;

use App\Traits\HasTranslatedFallback;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Feature extends Model
{
    use Filterable, HasFactory, HasTranslatedFallback, HasTranslations, SoftDeletes;

    protected $fillable = ['slug', 'name', 'description', 'group', 'is_active'];

    public $translatable = ['name', 'description'];

    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class, 'feature_plan')
            ->withPivot('value')
            ->using(FeaturePlan::class);
    }

    public function featureSubscriptions(): BelongsToMany
    {
        return $this->belongsToMany(Subscription::class, 'feature_subscriptions')
            ->withPivot('value', 'usage')
            ->using(FeatureSubscription::class);
    }

    public static function booted()
    {
        static::creating(function ($feature) {
            $feature->slug = Str::slug($feature->getTranslation('name', 'en'));
        });
    }
}
