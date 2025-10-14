<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chair extends Model
{
    use HasFactory;

    protected $fillable = ['team_id', 'user_id', 'started_at', 'ended_at'];

    protected $casts = [
        'started_at' => 'date',
        'ended_at' => 'date',
    ];

    // Relationships
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function targets()
    {
        return $this->hasMany(ChairTarget::class);
    }

    public function monthlyTargets()
    {
        return $this->hasMany(ChairTarget::class)
            ->where('period_type', 'monthly');
    }

    public function quarterlyTargets()
    {
        return $this->hasMany(ChairTarget::class)
            ->where('period_type', 'quarterly');
    }

    public function achievements()
    {
        return $this->hasMany(ChairAchievement::class);
    }

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNull('ended_at');
    }
}
