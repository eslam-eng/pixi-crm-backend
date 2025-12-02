<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class AutomationAction extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'key',
        'icon',
        'description',
        'configs',
        'is_active',
        'module_name',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'configs' => 'array',
    ];

    public $translatable = ['name'];

    /**
     * Get the localized name for a specific language
     */
    public function getName($locale = 'en')
    {
        return $this->getTranslation('name', $locale);
    }

    /**
     * Scope for active actions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordering by key
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('id', 'asc');
    }

    /**
     * Get all actions formatted for dropdown
     * If module_name is provided, returns actions where module_name is null OR matches the provided module_name
     */
    public static function getDropdownOptions(?string $moduleName = null)
    {
        $query = self::active()->ordered();

        if ($moduleName) {
            $query->where(function ($q) use ($moduleName) {
                $q->whereNull('module_name')
                    ->orWhere('module_name', $moduleName);
            });
        }

        return $query->get();
    }
}
