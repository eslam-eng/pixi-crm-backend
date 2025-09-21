<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Priority extends Model
{
    protected $fillable = [
        'name',
        'color_id',
        'level',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function color(): BelongsTo
    {
        return $this->belongsTo(PriorityColor::class, 'color_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('level', 'asc');
    }
}