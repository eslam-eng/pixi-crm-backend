<?php

namespace App\Models\Central;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantPlatform extends Model
{
    use Filterable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'platform_id',
        'external_id',
        'access_token',
        'refresh_token',
        'expires_at',
        'meta',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'meta' => 'array',
    ];

    /**
     * Get the tenant that owns the platform.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the platform that owns the tenant platform.
     */
    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }
}
