<?php

namespace App\Models\Tenant;

use App\Models\Tenant\IntegratedForm;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntegratedFormFieldMapping extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'integrated_form_fields_mapping';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'form_id',
        'external_field_key',
        'contact_column',
        'is_required',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_required' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the form mapping that owns this field mapping.
     */
    public function formMapping(): BelongsTo
    {
        return $this->belongsTo(IntegratedForm::class, 'form_id', 'form_id');
    }

    /**
     * Get the external field key.
     *
     * @return string
     */
    public function getExternalFieldKey(): string
    {
        return $this->external_field_key;
    }


    /**
     * Get the contact column.
     *
     * @return string
     */
    public function getContactColumn(): string
    {
        return $this->contact_column;
    }

    /**
     * Check if the field is required.
     *
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->is_required ?? false;
    }


    /**
     * Find field mappings by form ID.
     *
     * @param string $formId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function findByFormId(string $formId)
    {
        return static::where('form_id', $formId)->get();
    }

    /**
     * Create field mappings for a form.
     *
     * @param string $formId
     * @param array $mappings
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function createMappings(string $formId, array $mappings)
    {
        // Delete existing mappings for this form
        static::where('form_id', $formId)->delete();

        // Create new mappings
        $fieldMappings = [];
        foreach ($mappings as $mapping) {
            $fieldMappings[] = static::create([
                'form_id' => $formId,
                'external_field_key' => $mapping['external_field_key'],
                'contact_column' => $mapping['contact_column'],
                'is_required' => $mapping['is_required'] ?? false,
            ]);
        }

        return collect($fieldMappings);
    }

    /**
     * Get mappings as array format.
     *
     * @param string $formId
     * @return array
     */
    public static function getMappingsArray(string $formId): array
    {
        return static::where('form_id', $formId)
            ->get()
            ->map(function ($mapping) {
                return [
                    'external_field_key' => $mapping->getExternalFieldKey(),
                    'contact_column' => $mapping->getContactColumn(),
                    'is_required' => $mapping->isRequired(),
                ];
            })
            ->toArray();
    }
}
