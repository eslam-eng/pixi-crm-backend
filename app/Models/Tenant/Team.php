<?php

namespace App\Models\Tenant;

use App\Models\Tenant\User;
use App\Traits\Filterable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory, Filterable;
    protected $table = 'teams';

    protected $fillable = [
        'title',
        'leader_id',
        'description',
        'status',
        'is_target',
        'period_type',
        'leader_has_target',
    ];

    protected $casts = [
        'is_target' => 'boolean',
        'leader_has_target' => 'boolean',
    ];

    public function leader()
    {
        return $this->hasOne(User::class, 'id', 'leader_id');
    }

    public function members()
    {
        return $this->hasMany(User::class, 'team_id', 'id');
    }

    /**
     * Many-to-many relationship with users through chair pivot
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'chairs')
            ->withPivot(['id', 'started_at', 'ended_at'])
            ->withTimestamps()
            ->using(Chair::class);
    }

    /**
     * Get only active chairs
     */
    public function activeUsers()
    {
        return $this->belongsToMany(User::class, 'chairs')
            ->withPivot(['id', 'started_at', 'ended_at'])
            ->wherePivotNull('ended_at')
            ->withTimestamps()
            ->using(Chair::class);
    }

    /**
     * Get chair records (pivot table records)
     */
    public function chairs()
    {
        return $this->hasMany(Chair::class);
    }

    /**
     * Get active chair records
     */
    public function activeChairs()
    {
        return $this->hasMany(Chair::class)->whereNull('ended_at');
    }

    /**
     * Get current chair (if only one chair per team)
     */
    public function currentChair()
    {
        return $this->hasOne(Chair::class)->whereNull('ended_at')->latest('started_at');
    }

    /**
     * Get all deals for this team across all chairs
     */
    public function deals()
    {
        return $this->hasManyThrough(Deal::class, Chair::class);
    }

    /**
     * Scope for teams with targets enabled
     */
    public function scopeWithTargets($query)
    {
        return $query->where('is_target', true);
    }


    public function sales()
    {
        return $this->hasMany(User::class, 'team_id', 'id');
    }
}
