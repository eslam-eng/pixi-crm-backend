<?php

namespace App\Models\Tenant;

use App\Models\City;
use App\Models\Country;
use App\Models\Source;
use App\Models\Tenant\ContactPhone;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    use Filterable;
    protected $fillable =
    [
        'first_name',
        'last_name',
        'email',
        'job_title',
        'department',
        'status',
        'source_id',
        'contact_method',
        'email_permission',
        'phone_permission',
        'whatsapp_permission',
        'company_name',
        'campaign_name',
        'website',
        'industry',
        'company_size',
        'address',
        'country_id',
        'city_id',
        'state',
        'zip_code',
        'user_id',
        'tags',
        'notes',
    ];

    protected $casts = [
        'tags' => 'array', // Automatically handle JSON encoding/decoding
        'contact_phones' => 'array',
    ];

    public function contactMerges(): HasMany
    {
        return $this->hasMany(ContactMerge::class, 'contact_id');
    }

    /**
     * Get the full name by combining first_name and last_name
     */
    public function getNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function contactPhones(): HasMany
    {
        return $this->hasMany(ContactPhone::class, 'contact_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    // Contact belongs to a resource
    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class, 'source_id');
    }

    // Contact has many leads
    public function leads()
    {
        return $this->hasMany(Lead::class, 'contact_id');
    }

    public function activeLead()
    {
        return $this->hasOne(Lead::class, 'contact_id')->where('status', 'Active')->latest();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    // Search contacts by tag
    public function scopeWithTag($query, $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    // Search contacts with any of the given tags
    public function scopeWithAnyTag($query, $tags)
    {
        foreach ($tags as $tag) {
            $query->orWhereJsonContains('tags', $tag);
        }
        return $query;
    }
}
