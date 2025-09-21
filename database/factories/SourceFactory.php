<?php

namespace Database\Factories;

use App\Models\Source;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pipeline>
 */
class SourceFactory extends Factory
{
//    protected $model = Source::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
        ];
    }

    // /**
    //  * Configure the factory with an image.
    //  */
    // public function withImage(): Factory
    // {
    //     return $this->afterCreating(function (Source $source) {
    //         // Generate a fake image and save it to storage
    //         $imagePath = Storage::disk('public')->putFile('sources', UploadedFile::fake()->image('source.jpg'));

    //         // Attach the image to the media library
    //         $source->addMedia(storage_path("app/public/{$imagePath}"))
    //             ->preservingOriginal()
    //             ->toMediaCollection('sources');
    //     });
    // }
}
