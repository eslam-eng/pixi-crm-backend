<?php

namespace App\Models;

use App\Enums\ModuleType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tier extends Model
{
    /** @use HasFactory<\Database\Factories\TierFactory> */
    use HasFactory;

    protected $fillable = [
        'package_name',
        'description',
        'price',
        'duration_unit',
        'duration',
        'refund_period',
        'max_users',
        'max_contacts',
        'storage_limit',
        'status',
        'availability',
    ];

    protected $casts = [
        'price' => 'float',
        'modules' => 'array',

    ];

    public function tenant()
    {
        return $this->belongsToMany(Tenant::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function tier_modules()
    {
        return $this->hasMany(TierModule::class);
    }

    protected function modules(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $modules = json_decode($value, true) ?? [];
                return collect($modules)->map(function ($module) {
                    try {
                        return ModuleType::from($module);
                    } catch (\ValueError $e) {
                        return null; // Invalid module, return null
                    }
                })->filter()->values();
            },
            set: function ($value) {
                if (is_array($value)) {
                    // Validate all modules are valid enum values
                    $validModules = collect($value)->filter(function ($module) {
                        return ModuleType::isValid($module);
                    })->unique()->values()->toArray();

                    return json_encode($validModules);
                }
                return json_encode([]);
            }
        );
    }

    // Check if tier has specific module
    public function hasModule(ModuleType $module): bool
    {
        return in_array($module->value, $this->getRawOriginal('modules') ? json_decode($this->getRawOriginal('modules'), true) : []);
    }

    // Get module labels for display
    public function getModuleLabels(): array
    {
        return collect($this->modules)->map(fn($module) => $module->label())->toArray();
    }

    // Validate modules against enum
    public function validateModules(): array
    {
        $rawModules = json_decode($this->getRawOriginal('modules'), true) ?? [];
        $validModules = [];
        $invalidModules = [];

        foreach ($rawModules as $module) {
            if (ModuleType::isValid($module)) {
                $validModules[] = $module;
            } else {
                $invalidModules[] = $module;
            }
        }

        return [
            'valid' => $validModules,
            'invalid' => $invalidModules,
            'has_invalid' => !empty($invalidModules)
        ];
    }

    // Scope for active tiers
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for tiers with specific module
    public function scopeWithModule($query, ModuleType $module)
    {
        return $query->whereJsonContains('modules', $module->value);
    }
}
