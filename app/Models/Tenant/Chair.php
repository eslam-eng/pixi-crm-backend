<?php

namespace App\Models\Tenant;

use App\Enums\PeriodType;
use App\Traits\Filterable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Chair extends Model
{
    use HasFactory,Filterable;

    protected $fillable = ['team_id', 'user_id', 'started_at', 'ended_at'];

    protected $casts = [
        'started_at' => 'date',
        'ended_at' => 'date',
    ];

    /**
     * Indicates if the IDs are auto-incrementing
     */
    public $incrementing = true;

    // Relationships

    /**
     * Get the team
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all targets
     */
    public function targets()
    {
        return $this->hasMany(ChairTarget::class);
    }
    
    public function target($year, $period_number): HasOne
    {
        return $this->hasOne(ChairTarget::class)->ofMany([],
         function (Builder $query) use ($year, $period_number) {
           $query->where('year', $year)->where('period_number', $period_number);
        });
    }

    /**
     * Get active targets
     */
    public function activeTargets()
    {
        return $this->hasMany(ChairTarget::class)->whereNull('effective_to');
    }

    /**
     * Get achievements
     */
    public function achievements()
    {
        return $this->hasMany(ChairAchievement::class);
    }

    /**
     * Get monthly targets
     */
    public function monthlyTargets()
    {
        return $this->hasMany(ChairTarget::class)
            ->where('period_type', PeriodType::MONTHLY);
    }

    /**
     * Get quarterly targets
     */
    public function quarterlyTargets()
    {
        return $this->hasMany(ChairTarget::class)
            ->where('period_type', PeriodType::QUARTERLY);
    }



    /**
     * Get deals
     */
    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    /**
     * Scope to get active chairs
     */
    public function scopeActive($query)
    {
        return $query->whereNull('ended_at');
    }

    /**
     * Scope to get chairs active at a specific date
     */
    public function scopeActiveAt($query, $date)
    {
        $date = Carbon::parse($date);
        return $query->where('started_at', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('ended_at')
                    ->orWhere('ended_at', '>=', $date);
            });
    }

    /**
     * Check if chair is currently active
     */
    public function isActive(): bool
    {
        return is_null($this->ended_at);
    }

    /**
     * Check if chair was active at a specific date
     */
    public function wasActiveAt(Carbon $date): bool
    {
        return $this->started_at <= $date &&
            (is_null($this->ended_at) || $this->ended_at >= $date);
    }

    /**
     * Get the duration in days
     */
    public function getDurationInDays(): int
    {
        $end = $this->ended_at ?? Carbon::today();
        return $this->started_at->diffInDays($end);
    }

    /**
     * Scope to get chairs WITH teams
     */
    public function scopeWithTeam($query)
    {
        return $query->whereNotNull('team_id');
    }

    /**
     * Scope to get chairs WITHOUT teams (individual chairs)
     */
    public function scopeWithoutTeam($query)
    {
        return $query->whereNull('team_id');
    }

    /**
     * Scope to get individual chairs (alias)
     */
    public function scopeIndividual($query)
    {
        return $query->whereNull('team_id');
    }

    /**
     * Check if chair belongs to a team
     */
    public function hasTeam(): bool
    {
        return !is_null($this->team_id);
    }

    /**
     * Check if chair is individual (no team)
     */
    public function isIndividual(): bool
    {
        return is_null($this->team_id);
    }

    /**
     * Get display name (team name or "Individual")
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->team ? $this->team->name : 'Individual';
    }
}
