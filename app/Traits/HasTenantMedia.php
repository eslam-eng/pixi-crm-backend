<?php

namespace App\Traits;

use Spatie\MediaLibrary\Conversions\Manipulations;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HasTenantMedia
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatars');

        $this->addMediaCollection('images');

        $this->addMediaCollection('documents');
    }

    /**
     * Convert original to WebP after upload
     */
    public function registerMediaConversions(Media $media = null): void
    {
        // Only convert images, not documents
        if ($media && !str_starts_with($media->mime_type, 'image/')) {
            return;
        }

        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(85);
    }

    /**
    * Collections that should be converted to WebP.
    * Override this in your model if needed.
    */
    protected function webpCollections(): array
    {
        return ['images', 'avatars', 'default'];
    }

    /**
     * Get correct tenant media URL
     */
    public function getTenantMediaUrl(string $collectionName = 'default', string $conversionName = ''): ?string
    {
        $media = $this->getFirstMedia($collectionName);

        if (!$media) {
            return null;
        }

        if ($conversionName && $media->hasGeneratedConversion($conversionName)) {
            $path = $media->getPath($conversionName);
        } else {
            $path = $media->getPath();
        }

        // Extract relative path from full system path
        // Matches storage/tenant{id}/app/public/... OR storage/{id}/app/public/...
        if (preg_match('/storage\/(?:tenant)?([^\/]+)\/app\/public\/(.+)$/', $path, $matches)) {
            return url("/storage/tenant{$matches[1]}/{$matches[2]}");
        }
        // Fallback to default
        return $conversionName ? $media->getUrl($conversionName) : $media->getUrl();
    }

    /**
     * Get all tenant media URLs for a collection
     */
    public function getTenantMediaUrls(string $collectionName = 'default'): array
    {
        return $this->getMedia($collectionName)->map(function ($media) {
            return [
                'id' => $media->id,
                // 'name' => $media->file_name,
                'url' => $this->extractCorrectUrl($media->getPath()),
                // 'size' => $media->human_readable_size,
                // 'mime_type' => $media->mime_type,
            ];
        })->toArray();
    }

    protected function extractCorrectUrl(string $path): string
    {
        if (preg_match('/storage\/(?:tenant)?([^\/]+)\/app\/public\/(.+)$/', $path, $matches)) {
            return url("/storage/tenant{$matches[1]}/{$matches[2]}");
        }
        return $path;
    }
}