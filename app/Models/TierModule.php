<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TierModule extends Model
{
    protected $fillable = [
        'tier_id',
        'module_id',
        'limit_value',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
