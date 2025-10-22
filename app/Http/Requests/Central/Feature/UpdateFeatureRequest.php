<?php

namespace App\Http\Requests\Central\Feature;

use App\Enum\FeatureGroupEnum;
use App\Enum\SupportedLocalesEnum;
use Illuminate\Validation\Rule;

class UpdateFeatureRequest extends BaseFeatureRequest
{
    public function rules(): array
    {
        $rules = [
            'name' => 'required|array|min:1',
            'description' => 'nullable|array|min:1',
            'is_active' => 'required|boolean',
            'slug' => [
                'required',
                Rule::unique('features', 'slug')
                    ->whereNull('deleted_at')
                    ->ignore($this->feature),
                'string',
            ],
            'group' => ['required', Rule::in(FeatureGroupEnum::values())],
        ];

        // Get supported locales from the middleware or config
        $supportedLocales = SupportedLocalesEnum::values();

        // Add validation rules for each supported locale
        foreach ($supportedLocales as $locale) {
            $rules["name.{$locale}"] = 'required|string';
        }

        return $rules;
    }
}
