<?php

namespace App\Models\Tenant;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\FcmToken;
use App\Traits\Filterable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens, Filterable;

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
    ];

    protected $casts = [

        'last_login_at' => 'date',
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

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the FCM tokens for the user.
     */
    public function fcm_tokens()
    {
        return $this->hasMany(FcmToken::class);
    }
}
