<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;


class PaymentMethod extends Model
{
    protected $fillable = ['name', 'is_checked', 'is_default', 'is_manual_added'];

    protected $hidden = ['created_at', 'updated_at'];
}
