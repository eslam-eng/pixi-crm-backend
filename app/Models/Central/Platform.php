<?php

namespace App\Models\Central;

use App\Traits\HasTranslatedFallback;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelPackageTools\Concerns\Package\HasTranslations;

class Platform extends Model
{
    use Filterable, HasFactory, HasTranslatedFallback, HasTranslations;

    protected $fillable = [
        'name',
        'slug',
        'is_active',
        'base_url',
        'auth_url',
        'token_url',
        'api_version',
        'settings',
    ];

    public $translatable = ['name'];
}
