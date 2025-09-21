<?php

namespace App\Models;

use App\Enums\landlord\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Subscription extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'subscriptions_landlord';
    protected $fillable = [
        'client_id',
        'tier_id',
        'subscription_start_date',
        'subscription_end_date',
        'subscription_status',
        'activation_method',
        'source',
        'auto_renew',
        'payment_status',
    ];

    protected $casts = [
        'subscription_start_date' => 'date',
        'subscription_end_date' => 'date',
        'auto_renew' => 'string',
        'payment_status' => PaymentStatus::class,
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function tier()
    {
        return $this->belongsTo(Tier::class, 'tier_id', 'id');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }
}
