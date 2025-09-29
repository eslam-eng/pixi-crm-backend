<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class DealPayment extends Model
{
    protected $fillable = [
        'deal_id',
        'amount',
        'pay_date',
        'payment_method_id',
    ];
    
    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
