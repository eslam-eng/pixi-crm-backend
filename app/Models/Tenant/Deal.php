<?php

namespace App\Models\Tenant;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Deal extends Model implements HasMedia
{
    use Filterable, InteractsWithMedia;
    protected $fillable = [
        'deal_type',
        'deal_name',
        'lead_id',
        'sale_date',
        'discount_type',
        'discount_value',
        'tax_rate',
        'payment_status',
        'payment_method_id',
        'notes',
        'assigned_to_id',
        'total_amount',
        'partial_amount_paid',
        'amount_due',
        'approval_status',
        'created_by_id',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($deal) {
            if (Auth::check() && !$deal->created_by_id) {
                $deal->created_by_id = Auth::id();
            }
        });
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function assigned_to()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'deal_items', 'deal_id', 'item_id')->withPivot('quantity', 'price', 'total')->withTimestamps();
    }

    public function attachments()
    {
        return $this->hasMany(DealAttachment::class);
    }

    /**
     * Register media conversions for the model.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('thumbnail')
            ->width(150)
            ->height(150)
            ->sharpen(10)
            ->nonQueued();

        $this
            ->addMediaConversion('preview')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->nonQueued();
    }
}
