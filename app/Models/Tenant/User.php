<?php

namespace App\Models\Tenant;

use App\Enums\TargetType;
use App\Models\FcmToken;
use App\Models\Tenant\Team;
use App\Settings\UsersSettings;
use App\Traits\Filterable;
use Carbon\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens, Filterable, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'image',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'last_login_at',
        'department_id',
        'is_active',
        'target',
        'target_type',
        'job_title',
        'team_id',
        'lang',
    ];

    protected $casts = [
        'target' => 'float',
        'last_login_at' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'name',
    ];

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * @param $image
     * @return string
     */
    public function getImageAttribute($image): string
    {
        if (!empty($image) && file_exists(public_path('storage/users/' . $image))) {
            return asset('storage') . '/users/' . $image;
        }
        return asset('defaults/default-user.png');
    }

    /**
     * @param $image
     * @return void
     */
    public function setImageAttribute($image): void
    {
        if (!empty($image)) {
            $imageFields = $image;
            if (is_file($image)) {
                $imageFields = upload($image, 'users');
            }
            $this->attributes['image'] = $imageFields;
        }
    }

    public function targets()
    {
        return $this->hasMany(UserTarget::class);
    }

    public function currentTarget()
    {

        $settings = app(UsersSettings::class);
        $targetType = $settings->default_target_type;
        return match ($targetType) {
            TargetType::MONTHLY->value => $this->getMonthTarget(),
            TargetType::CALENDAR_QUARTERS->value => $this->getQuarterTarget(),
            default => null,
        };
    }

    private function getMonthTarget()
    {
        $date = Carbon::now();
        return $this->targets()->where('created_at', '<=', $date)->where('target_type', TargetType::MONTHLY->value)->latest('created_at')->first();
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Relationship with team
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get only active team assignments
     */
    public function activeTeams()
    {
        return $this->belongsToMany(Team::class, 'chairs')
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
     * Get chairs that belong to teams
     */
    public function teamChairs()
    {
        return $this->hasMany(Chair::class)->withTeam();
    }

    /**
     * Get chairs without teams (individual chairs)
     */
    public function individualChairs()
    {
        return $this->hasMany(Chair::class)->withoutTeam();
    }

    /**
     * Get active individual chair
     */
    public function activeIndividualChair()
    {
        return $this->hasOne(Chair::class)
            ->whereNull('team_id')
            ->whereNull('ended_at')
            ->latest('started_at');
    }

    /**
     * Get active individual chair
     */
    public function activeChair()
    {
        return $this->hasOne(Chair::class)
            ->whereNull('ended_at');
    }

    /**
     * Check if user is currently a chair (team or individual)
     */
    public function isChair(): bool
    {
        return $this->activeChairs()->exists();
    }

    /**
     * Check if user has an active individual chair
     */
    public function hasIndividualChair(): bool
    {
        return $this->activeIndividualChair()->exists();
    }

    /**
     * Check if user is chair of a specific team
     */
    public function isChairOf(Team $team): bool
    {
        return $this->activeTeams()->where('teams.id', $team->id)->exists();
    }

    /**
     * Get all deals across all chair assignments
     */
    public function deals()
    {
        return $this->hasManyThrough(Deal::class, Chair::class);
    }

    /**
     * Get teams user was chair of at a specific date
     */
    public function teamsAt(Carbon $date)
    {
        return $this->belongsToMany(Team::class, 'chairs')
            ->withPivot(['id', 'started_at', 'ended_at'])
            ->wherePivot('started_at', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->wherePivotNull('ended_at')
                    ->orWherePivot('ended_at', '>=', $date);
            })
            ->withTimestamps()
            ->using(Chair::class);
    }

    /**
     * Get the FCM tokens for the user.
     */
    public function fcm_tokens()
    {
        return $this->hasMany(FcmToken::class);
    }

    /**
     * Get all deals across all chair assignments
     */
    public function chairDeals()
    {
        return $this->hasManyThrough(Deal::class, Chair::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email'])
            ->logOnlyDirty() // Only log changed attributes
            ->dontSubmitEmptyLogs()
            ->useLogName('user')
            ->setDescriptionForEvent(fn(string $eventName) => "User has been {$eventName}");
    }
}
