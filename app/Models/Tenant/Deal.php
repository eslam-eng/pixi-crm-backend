<?php

namespace App\Models\Tenant;

use App\Models\Tenant\Contact;
use App\Models\Stage;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Deal extends Model implements HasMedia
{
    use Filterable, InteractsWithMedia;
    protected $fillable = [
        'deal_type',
        'deal_name',
        'contact_id',
        'sale_date',
        'discount_type',
        'discount_value',
        'tax_rate',
        'payment_status',
        'payment_method_id',
        'notes',
        'assigned_to_id',
        'stage_id',
        'total_amount',
        'partial_amount_paid',
        'partial_amount_due',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }
    public function assigned_to()
    {
        return $this->belongsTo(User::class,'assigned_to_id');
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
