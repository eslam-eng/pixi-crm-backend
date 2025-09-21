<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TranslatableExampleController extends Controller
{
    /**
     * Display a listing of industries with translations.
     */
    public function index(Request $request): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale());
        
        $industries = Industry::all()->map(function ($industry) use ($locale) {
            return [
                'id' => $industry->id,
                'name' => $industry->getTranslation('name', $locale),
                'name_translations' => $industry->getAllTranslations('name'),
                'current_locale' => $locale,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $industries,
            'message' => 'Industries retrieved successfully'
        ]);
    }

    /**
     * Store a newly created industry with translations.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'nullable|string|max:255',
        ]);

        $industry = new Industry();
        $industry->setTranslations('name', $request->name);
        $industry->save();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $industry->id,
                'name' => $industry->getAllTranslations('name'),
            ],
            'message' => 'Industry created successfully with translations'
        ], 201);
    }

    /**
     * Display the specified industry with translations.
     */
    public function show(Request $request, Industry $industry): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale());
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $industry->id,
                'name' => $industry->getTranslation('name', $locale),
                'name_translations' => $industry->getAllTranslations('name'),
                'current_locale' => $locale,
            ],
            'message' => 'Industry retrieved successfully'
        ]);
    }

    /**
     * Update the specified industry with translations.
     */
    public function update(Request $request, Industry $industry): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|array',
            'name.en' => 'sometimes|string|max:255',
            'name.ar' => 'sometimes|string|max:255',
        ]);

        if ($request->has('name')) {
            $industry->setTranslations('name', $request->name);
            $industry->save();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $industry->id,
                'name' => $industry->getAllTranslations('name'),
            ],
            'message' => 'Industry updated successfully with translations'
        ]);
    }

    /**
     * Change the current locale for the industry.
     */
    public function changeLocale(Request $request, Industry $industry): JsonResponse
    {
        $request->validate([
            'locale' => 'required|string|in:en,ar'
        ]);

        $locale = $request->locale;
        $industry->setLocale($locale);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $industry->id,
                'name' => $industry->name, // This will now return the name in the specified locale
                'current_locale' => $locale,
            ],
            'message' => 'Industry locale changed successfully'
        ]);
    }
} 