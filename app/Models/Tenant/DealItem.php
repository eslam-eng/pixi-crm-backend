<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DealItem extends Model
{
    protected $table = 'deal_items';
    
    protected $fillable = [
        'deal_id',
        'item_id',
        'quantity',
        'price',
        'total',
    ];


    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }

    /**
     * Get the subscription for this deal item.
     */
    public function subscription(): HasOne
    {
        return $this->hasOne(DealItemSubscription::class);
    }
}
