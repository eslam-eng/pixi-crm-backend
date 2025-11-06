<?php

namespace App\Models\Tenant;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    use HasFactory, Filterable, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'subject',
        'body',
        'variables',
        'is_active',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Scope for active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for email templates
     */
    public function scopeEmail($query)
    {
        return $query->where('type', 'email');
    }

    /**
     * Scope for WhatsApp templates
     */
    public function scopeWhatsapp($query)
    {
        return $query->where('type', 'whatsapp');
    }

    /**
     * Get template by slug
     */
    public static function findBySlug(string $slug, string $type = 'email'): ?self
    {
        return static::where('slug', $slug)
            ->where('type', $type)
            ->active()
            ->first();
    }
}

