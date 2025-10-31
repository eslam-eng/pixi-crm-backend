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
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
        return $query->orderBy('id','asc');
    }

    /**
     * Get all actions formatted for dropdown
     */
    public static function getDropdownOptions()
    {
        return self::active()
            ->ordered()
            ->get();
    }
}
