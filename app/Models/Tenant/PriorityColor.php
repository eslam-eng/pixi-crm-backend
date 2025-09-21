<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PriorityColor extends Model
{
    protected $fillable = [
        'name',
        'hex_code',
    ];

    public function priorities(): HasMany
    {
        return $this->hasMany(Priority::class, 'color_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('name', 'asc');
    }
}