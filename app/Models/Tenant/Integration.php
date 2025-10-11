<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Integration extends Model
{
    protected $fillable = [
        'name',
        'app_id',
        'app_secret',
        'redirect_uri',
        'access_token',
        'refresh_token',
        'token_expires_at',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
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
        return !empty($this->app_id) && !empty($this->app_secret);
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
