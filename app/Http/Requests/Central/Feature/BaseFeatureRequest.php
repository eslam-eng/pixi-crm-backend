<?php

namespace App\Http\Requests\Central\Feature;

use App\Enum\SupportedLocalesEnum;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class BaseFeatureRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'slug' => Str::slug(Arr::get($this->name, 'en')),
        ]);
    }

    public function messages(): array
    {
        $messages = [
            'name.required' => __('validation.name_required'),
            'name.array' => __('validation.name_array'),
            'name.min' => __('validation.name_min'),
            'is_active.required' => __('validation.is_active_required'),
            'is_active.boolean' => __('validation.is_active_boolean'),
            'slug.required' => __('validation.slug_required'),
            'slug.unique' => __('validation.slug_unique'),
            'slug.string' => __('validation.slug_string'),
            'group.required' => __('validation.group_required'),
            'group.in' => __('validation.group_invalid'),
        ];

        $supportedLocales = SupportedLocalesEnum::values();

        foreach ($supportedLocales as $locale) {
            $messages["name.{$locale}.required"] = __('validation.name_locale_required', ['locale' => strtoupper($locale)]);
            $messages["name.{$locale}.string"] = __('validation.name_locale_string', ['locale' => strtoupper($locale)]);
        }

        return $messages;
    }
}
