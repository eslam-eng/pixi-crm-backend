<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class TaskType extends Model
{
    public $fillable = [
        'name',
        'icon',
        'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function scopeOrdered($query)
    {
        return $query->orderBy('name', 'asc');
    }
}
