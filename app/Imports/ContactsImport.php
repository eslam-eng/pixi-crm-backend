<?php

namespace App\Imports;

use App\Models\Tenant\Contact;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class ContactsImport implements ToModel, WithValidation, SkipsOnFailure, WithHeadingRow
{
    use Importable, SkipsFailures;

    protected $columnMapping;
    protected $importSettings;

    public function __construct(array $columnMapping = [], array $importSettings = [])
    {
        $this->columnMapping = $columnMapping;
        $this->importSettings = $importSettings;
    }

    public function model(array $row)
    {
        // Log what we're receiving for debugging
        \Log::info('Processing row:', $row);
        \Log::info('Column mapping:', $this->columnMapping);
        // Skip empty rows
        if (empty(array_filter($row))) {
            return null;
        }

        $mappedData = [];

        // Map Excel columns to database fields
        foreach ($this->columnMapping as $dbField => $excelColumn) {
            if ($excelColumn && isset($row[$excelColumn])) {
                $mappedData[$dbField] = $this->processValue($dbField, $row[$excelColumn]);
            }
        }

        // Set default values
        $mappedData = $this->setDefaults($mappedData);

        return new Contact($mappedData);
    }

    public function rules(): array
    {
        $rules = [];
        // Only validate fields that are mapped
        foreach ($this->columnMapping as $dbField => $excelColumn) {
            if ($excelColumn) {
                $rules[$excelColumn] = $this->getFieldRules($dbField);
            }
        }
        return $rules;
    }

    private function processValue($field, $value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Field-specific processing
        switch ($field) {
            case 'email_permission':
            case 'phone_permission':
            case 'whatsapp_permission':
                return $this->convertToBoolean($value);

            case 'email':
                return strtolower(trim($value));

            case 'website':
                return $this->processUrl($value);

            default:
                return trim($value);
        }
    }

    private function convertToBoolean($value): ?bool
    {
        if (is_bool($value)) return $value;

        $value = strtolower(trim($value));
        return in_array($value, ['yes', 'y', '1', 'true', 'on', 'enabled', 'checked']);
    }

    private function processUrl($value): ?string
    {
        if (empty($value)) return null;

        $value = trim($value);
        if (!preg_match('/^https?:\/\//', $value)) {
            $value = 'https://' . $value;
        }

        return $value;
    }

    private function setDefaults(array $data): array
    {
        $defaults = $this->importSettings['defaults'] ?? [];

        // Set user-defined defaults
        foreach ($defaults as $field => $defaultValue) {
            if (!isset($data[$field]) || $data[$field] === null) {
                $data[$field] = $defaultValue;
            }
        }

        // Set system defaults
        if (!isset($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }

        if (!isset($data['status'])) {
            $data['status'] = 'active';
        }

        return $data;
    }

    private function getFieldRules($field): string
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'business_phone' => 'required|string|max:20',
            'mobile_phone' => 'required|string|max:20',
            'job_title' => 'required|string|max:100',
            'department' => 'required|string|max:100',
            'status' => 'required|string|in:active,inactive,pending',
            'source_id' => 'nullable|exists:sources,id',
            'contact_method_id' => 'nullable|exists:contact_methods,id',
            'email_permission' => 'required|boolean',
            'phone_permission' => 'required|boolean',
            'whatsapp_permission' => 'required|boolean',
            'company_name' => 'required|string|max:255',
            'website' => 'required|url|max:255',
            'industry' => 'required|string|max:100',
            'company_size' => 'required|string|max:50',
            'address' => 'required|string|max:500',
            'country_id' => 'nullable|exists:countries,id',
            'city_id' => 'nullable|exists:cities,id',
            'state' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'user_id' => 'nullable|exists:users,id',
            'tags' => 'required|json',
            'notes' => 'required|string|max:1000',
        ];

        return $rules[$field] ?? 'nullable|string|max:255';
    }

    private function processTags($tags)
    {
        if (empty($tags)) {
            return null;
        }

        $tags = trim($tags);

        // Split by common separators
        $tagArray = preg_split('/[,;|]/', $tags);

        // Clean each tag
        $cleanTags = array_map(function ($tag) {
            return trim($tag);
        }, $tagArray);

        // Remove empty tags
        $cleanTags = array_filter($cleanTags, function ($tag) {
            return !empty($tag);
        });

        // Remove duplicates and reindex
        $cleanTags = array_values(array_unique($cleanTags));

        // Return as array (will be stored as JSON)
        return $cleanTags;
    }
}
