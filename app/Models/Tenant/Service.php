<?php

namespace App\Models\Tenant;

use App\Enums\ServiceDuration;
use App\Enums\ServiceType;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use Filterable;
    protected $fillable =
    [
        'service_type',
        'duration',
        'category_id',
    ];

    protected $casts = [
        'service_type' => ServiceType::class,
        'duration' => ServiceDuration::class,
    ];

    // Polymorphic relationship (reverse)
    public function item(): MorphOne
    {
        return $this->morphOne(Item::class, 'itemable');
    }

    public function deals()
    {
        return $this->belongsToMany(Deal::class, 'deal_items', 'item_id', 'deal_id');
    }

    public function opportunities()
    {
        return $this->belongsToMany(Lead::class, 'leads_items', 'item_id', 'lead_id');
    }

    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'category_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    public function variants()
    {
        return $this->hasMany(ItemVariant::class);
    }
}
