<?php

namespace App\Models\Central;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Landlord\SupportedLocalesEnum;
use App\Models\City;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia
{
    /** @use HasFactory<\Database\Factories\Landlord\UserFactory> */
    use Filterable, HasApiTokens, HasFactory, InteractsWithMedia, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email_verified_at',
        'email',
        'password',
        'job_title',
        'website',
        'city_id',
        'company_size',
        'industry_id',
        'postal_code',
        'address',
        'phone',
        'owner_id',
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

    // current tenant
    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class, 'owner_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    public function tenants()
    {
        return $this->belongsToMany(Tenant::class)
            ->withPivot('is_owner')
            ->withTimestamps();
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
            'locale' => SupportedLocalesEnum::class,
        ];
    }

    public function generateToken(string $name = 'auth_token', array $abilities = ['*']): string
    {
        return $this->createToken($name, $abilities)->plainTextToken;
    }

    public function socialLogins()
    {
        return $this->hasMany(SocialLogin::class);
    }
}
