<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use App\Enums\IntegrationStatusEnum;
use App\Enums\PlatformEnum;

class Integration extends Model
{
    protected $fillable = [
        'name',
        'platform',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'last_sync',
        'status',
        'is_active',
    ];

    protected $casts = [
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
        'token_expires_at' => 'datetime',
        'last_sync' => 'datetime',
        'status' => IntegrationStatusEnum::class,
        'platform' => PlatformEnum::class,
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    /**
     * Check if the access token is expired
     */
    public function isTokenExpired(): bool
    {
        if (!$this->token_expires_at) {
            return true;
        }

        return $this->token_expires_at->isPast();
    }

    /**
     * Check if the integration has valid credentials
     */
    public function hasValidCredentials(): bool
    {
        return !empty($this->access_token);
    }

    /**
     * Check if the integration is connected (has access token)
     */
    public function isConnected(): bool
    {
        return !empty($this->access_token) && !$this->isTokenExpired();
    }

    /**
     * Get the time until token expires
     */
    public function getTokenExpiresInAttribute(): ?int
    {
        if (!$this->token_expires_at) {
            return null;
        }

        return $this->token_expires_at->diffInSeconds(now());
    }
}
