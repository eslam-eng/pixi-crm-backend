<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    protected $cacheKey = 'app_settings';
    protected $cacheTime = 3600; // 1 hour

    public function get(string $key, $default = null)
    {
        $settings = $this->getAllCached();
        
        if (!isset($settings[$key])) {
            return $default;
        }
        
        return $this->convertValue($settings[$key]['value'], $settings[$key]['type']);
    }

    public function set(string $key, $value, string $type = 'string', string $description = null): bool
    {
        AppSetting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $this->convertToString($value, $type),
                'type' => $type,
                'description' => $description
            ]
        );

        return true;
    }

    public function has(string $key): bool
    {
        $settings = $this->getAllCached();
        return isset($settings[$key]);
    }

    public function forget(string $key): bool
    {
        $deleted = AppSetting::where('key', $key)->delete();
        Cache::forget($this->cacheKey);
        return $deleted > 0;
    }

    public function all(): array
    {
        return $this->getAllCached();
    }

    protected function getAllCached(): array
    {
        return Cache::remember($this->cacheKey, $this->cacheTime, function () {
            return AppSetting::all()->keyBy('key')->toArray();
        });
    }

    protected function convertValue($value, string $type)
    {
        return match($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'array', 'json' => json_decode($value, true),
            default => $value
        };
    }

    protected function convertToString($value, string $type): string
    {
        return match($type) {
            'boolean' => $value ? 'true' : 'false',
            'array', 'json' => json_encode($value),
            default => (string) $value
        };
    }
}