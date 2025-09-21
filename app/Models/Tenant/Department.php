<?php

namespace App\Models\Tenant;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Department extends Model
{
    use Filterable,HasTranslations;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    public $translatable = ['name'];


    protected $casts = [
        'is_active' => 'boolean',
    ];


    public function getLocalizedNameAttribute()
    {
        return $this->getTranslation('name', app()->getLocale());
    }

    /**
     * Scope for active departments
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
