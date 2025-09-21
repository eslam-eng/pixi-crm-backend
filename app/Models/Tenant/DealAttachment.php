<?php

namespace App\Models\Tenant;

use App\Models\Tenant\Deal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DealAttachment extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'deal_id',
        'media_id',
        'name',
        'description',
        'file_type',
        'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    /**
     * Get the deal that owns the attachment.
     */
    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal ::class);
    }

    /**
     * Get the media that owns the attachment.
     */
    public function mediaFile(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    /**
     * Register media conversions for the model.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('thumbnail')
            ->width(150)
            ->height(150)
            ->sharpen(10)
            ->nonQueued();

        $this
            ->addMediaConversion('preview')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->nonQueued();
    }

    /**
     * Get the file URL.
     */
    public function getFileUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('deal_attachments');
    }

    /**
     * Get the thumbnail URL.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('deal_attachments', 'thumbnail');
    }

    /**
     * Get the preview URL.
     */
    public function getPreviewUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('deal_attachments', 'preview');
    }
}