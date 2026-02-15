<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'module',
        'key',
        'name',
        'ordering',
    ];

    protected $casts = [
        'name' => 'array',
        'ordering' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function customFields(): HasMany
    {
        return $this->hasMany(CustomField::class)->orderBy('ordering');
    }

    public function scopeByModule($query, string $module)
    {
        return $query->where('module', $module);
    }
}
