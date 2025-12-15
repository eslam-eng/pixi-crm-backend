<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MediaRequest;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class MediaController extends Controller
{
    public function __invoke(MediaRequest $request)
    {
        $data = $request->validated();

        $medias = [];

        foreach ($data['images'] as $image) {
            $path= $this->convertToWebp($image['file']);
            $medias[] = [
                'name' => $image['name'],
                'path' => $path,
                'url'  => Storage::disk('public')->url('tenant' . tenant('id') . '/' .$path),
            ];
        }
        
        foreach ($data['documents'] as $document) {
            $fileName = Str::uuid() . '.' . $document['file']->getClientOriginalExtension();
            $path = 'temp/uploads/' . $fileName;
            Storage::disk('public')->put($path, file_get_contents($document['file']));
            $medias[] = [
                'name' => $document['name'],
                'path' => $path,
                'url'  => Storage::disk('public')->url('tenant' . tenant('id') . '/' .$path),
            ];
        }

        return ApiResponse(message: 'Media uploaded successfully', data: $medias, code: Response::HTTP_CREATED);
    }

    protected function convertToWebp(
        UploadedFile $file,
        int $quality = 90
    ): string {
        // Read image
        $image = Image::read($file);

        // Convert to WebP
        $encoded = $image->toWebp(quality: $quality);

        $fileName = Str::uuid() . '.webp';
        // Relative path (NO tenant here)
        $path = 'temp/uploads/' . $fileName;

        // Save to tenant-aware disk
        Storage::disk('public')->put($path, $encoded);

        return $path;
    }
}