# Localization Usage Guide

## Overview
This application now supports automatic language detection from API headers and provides easy access to localized content through model accessors.

## How It Works

### 1. Middleware (SetLocaleFromHeader)
The `SetLocaleFromHeader` middleware automatically detects the language from API headers and sets the application locale.

**Supported Headers:**
- `X-Locale`: Custom header for explicit locale setting (e.g., `X-Locale: ar`)
- `Accept-Language`: Standard HTTP header (e.g., `Accept-Language: ar,en;q=0.9`)

**Supported Locales:**
- `en` (English) - Default
- `ar` (Arabic)

### 2. Model Accessors
The `Module` model now includes accessors that automatically return localized values:

```php
// Instead of accessing the full translation object:
$module->name // Returns: {"en": "Voice Calls", "ar": "المكالمات الصوتية"}

// You can now use:
$module->localized_name // Returns: "Voice Calls" (or "المكالمات الصوتية" if locale is 'ar')
$module->localized_group_label
$module->localized_number_field_label
```

## Usage Examples

### API Requests

**Set Arabic locale:**
```bash
curl -H "X-Locale: ar" https://your-api.com/api/helpers/modules
```

**Set English locale:**
```bash
curl -H "X-Locale: en" https://your-api.com/api/helpers/modules
```

**Using Accept-Language header:**
```bash
curl -H "Accept-Language: ar,en;q=0.9" https://your-api.com/api/helpers/modules
```

### In Controllers
```php
public function index()
{
    // The locale is automatically set by middleware
    $modules = Module::all()->map(function ($module) {
        return [
            'id' => $module->id,
            'name' => $module->localized_name, // Automatically localized
            'group_label' => $module->localized_group_label, // Automatically localized
        ];
    });
    
    return response()->json($modules);
}
```

### In Blade Views
```php
// The locale is automatically set by middleware
{{ $module->localized_name }}
{{ $module->localized_group_label }}
```

## Adding New Locales

To add support for new languages:

1. **Update the middleware** (`app/Http/Middleware/SetLocaleFromHeader.php`):
```php
$supportedLocales = ['en', 'ar', 'fr', 'es']; // Add new locales
```

2. **Add translations** to your translatable fields in the database

3. **Update language files** in `lang/` directory if needed

## Benefits

1. **Automatic**: No need to manually set locale in each controller
2. **Consistent**: All API endpoints automatically respect the language header
3. **Easy to use**: Simple accessors like `$module->localized_name`
4. **Fallback support**: Automatically falls back to English if translation is missing
5. **Standards compliant**: Uses standard HTTP headers

## Testing

You can test the localization by sending requests with different headers:

```bash
# Test Arabic
curl -H "X-Locale: ar" https://your-api.com/api/helpers/modules

# Test English
curl -H "X-Locale: en" https://your-api.com/api/helpers/modules

# Test Accept-Language
curl -H "Accept-Language: ar,en;q=0.9" https://your-api.com/api/helpers/modules
```

The response will automatically contain the localized content based on the header you send. 