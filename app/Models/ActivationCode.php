<?php

namespace App\Models;

use App\Enums\ModuleType;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivationCode extends Model
{
    /** @use HasFactory<\Database\Factories\ActivationCodeFactory> */
    use HasFactory, Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'source',
        'tier_id',
        'trial_days',
        'expires_at',
        'status',
        'create_by',
        'used_at',
    ];
    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'used_at' => 'datetime',
        ];
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [];


    public function tier()
    {
        return $this->belongsTo(Tier::class);
    }

    public function createBy()
    {
        return $this->belongsTo(User::class, 'create_by');
    }

    /**
     * Get the tenant that owns the activation code.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Check if the activation code is valid.
     *
     * @return bool
     */
    public function getIsValidAttribute(): bool
    {
        return !$this->is_used && !$this->is_expired && $this->expires_at > now();
    }
    /**
     * Check if the activation code is expired.
     *
     * @return bool
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at <= now() || $this->is_expired;
    }
    /**
     * Check if the activation code is used.
     *
     * @return bool
     */
    public function getIsUsedAttribute(): bool
    {
        return $this->is_used || $this->used_at !== null;
    }
    /**
     * Scope a query to only include valid activation codes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeValid($query)
    {
        return $query->where('is_used', false)
            ->where('is_expired', false)
            ->where('expires_at', '>', now());
    }
    /**
     * Scope a query to only include expired activation codes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query)
    {
        return $query->where('is_expired', true)
            ->orWhere('expires_at', '<=', now());
    }
    /**
     * Scope a query to only include used activation codes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsed($query)
    {
        return $query->where('is_used', true)
            ->orWhereNotNull('used_at');
    }
    /**
     * Scope a query to only include activation codes for a specific module type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $moduleType
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForModuleType($query, string $moduleType)
    {
        return $query->where('module_type', ModuleType::from($moduleType)->value);
    }
}
