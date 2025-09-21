<?php

namespace App\Traits;

use Spatie\Translatable\HasTranslations;

trait TranslatableFields
{
    use HasTranslations;

    /**
     * Get the translatable attributes for the model.
     */
    abstract public function getTranslatableAttributes(): array;

    /**
     * Get the current locale.
     */
    public function getLocale(): string
    {
        return app()->getLocale();
    }

    /**
     * Set the locale for the model.
     */
    public function setLocale(string $locale): self
    {
        $this->translationLocale = $locale;
        return $this;
    }

    /**
     * Get all translations for a given attribute.
     */
    public function getAllTranslations(string $attribute): array
    {
        return $this->getTranslations($attribute);
    }

    /**
     * Check if a translation exists for a given attribute and locale.
     */
    public function hasTranslation(string $attribute, string $locale): bool
    {
        $translations = $this->getTranslations($attribute);
        return isset($translations[$locale]) && !empty($translations[$locale]);
    }

    /**
     * Get the fallback translation for a given attribute.
     */
    public function getFallbackTranslation(string $attribute): mixed
    {
        return $this->getTranslation($attribute, config('translatable.fallback_locale'), false);
    }

    /**
     * Fill translatable attributes with translations.
     */
    public function fillTranslatable(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->getTranslatableAttributes())) {
                if (is_array($value)) {
                    $this->setTranslations($key, $value);
                } else {
                    $this->setTranslation($key, $this->getLocale(), $value);
                }
            }
        }
        return $this;
    }
} 