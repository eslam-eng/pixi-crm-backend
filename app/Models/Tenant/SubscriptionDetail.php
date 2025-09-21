<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class SubscriptionDetail extends Model
{
    protected $fillable = ['deal_id', 'recurring_amount', 'billing_cycle', 'subscription_start', 'subscription_end'];

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
}
    