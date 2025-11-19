<?php

namespace App\Models\Tenant;

use App\Enums\IdenticalContactType;
use App\Enums\MergeContactType;
use App\Traits\Filterable;
use App\Models\Source;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContactMerge extends Model
{
    use Filterable;

    protected $fillable =
    [
        'contact_id',
        'identical_contact_type',
        'merge_status',
        'first_name',
        'last_name',
        'email',
        'job_title',
        'department',
        'status',
        'contact_method',
        'email_permission',
        'phone_permission',
        'whatsapp_permission',
        'company_name',
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
        'tags' => 'array',
        'merge_status' => MergeContactType::class,
        'identical_contact_type' => IdenticalContactType::class,
        'whatsapp_permission' => 'boolean',
        'email_permission' => 'boolean',
        'phone_permission' => 'boolean',
    ];

    public function contactMergePhones(): HasMany
    {
        return $this->hasMany(ContactMergePhone::class, 'contact_merge_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
}
