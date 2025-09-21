<?php
// app/Models/AppSetting.php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'description'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Clear cache when model is saved or deleted
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            Cache::forget('app_settings');
        });

        static::deleted(function () {
            Cache::forget('app_settings');
        });
    }

    // Convert value based on type
    public function getValueAttribute($value)
    {
        return match ($this->type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'array', 'json' => json_decode($value, true),
            default => $value
        };
    }

    // Convert value to string for storage
    public function setValueAttribute($value)
    {
        $this->attributes['value'] = match ($this->type) {
            'boolean' => $value ? 'true' : 'false',
            'array', 'json' => json_encode($value),
            default => (string) $value
        };
    }
}
