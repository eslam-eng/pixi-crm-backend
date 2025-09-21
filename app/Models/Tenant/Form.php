<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Form extends Model
{
    protected $fillable = [
        'title',
        'description',
        'slug',
        'is_active',
        'submissions_count'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class)->orderBy('order');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(FormAction::class)->where('is_active', true)->orderBy('order');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }

    public function incrementSubmissions(): void
    {
        $this->increment('submissions_count');
    }
}
