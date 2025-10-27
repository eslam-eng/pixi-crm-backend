<?php

namespace App\Traits;

trait HasTranslatedFallback
{
    public function getTranslatedFallback(string $attribute, ?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $fallbackLocales = config('app.fallback_locales', ['en']);

        $value = $this->getTranslation($attribute, $locale, false);

        // If value is missing in current locale, loop over fallbacks
        if (! $value) {
            foreach ($fallbackLocales as $fallbackLocale) {
                $value = $this->getTranslation($attribute, $fallbackLocale, false);
                if ($value) {
                    break;
                }
            }
        }

        return $value;
    }
}
