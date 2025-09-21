<?php

namespace App\Models;

use App\Models\Tenant\Contact;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Source extends Model implements HasMedia
{
    use Filterable, InteractsWithMedia, HasFactory;

    protected $fillable=['name'];

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }

    /**
     * Accessor to get media collection.
     */
    public function getImageUrlAttribute()
    {
        return $this->getFirstMediaUrl('sources') ?? null;
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
