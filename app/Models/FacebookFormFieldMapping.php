<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacebookFormFieldMapping extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'facebook_form_field_mappings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'form_id',
        'facebook_field_key',
        'contact_column'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the form mapping that owns this field mapping.
     */
    public function formMapping(): BelongsTo
    {
        return $this->belongsTo(FacebookFormMapping::class, 'form_id', 'form_id');
    }

    /**
     * Get the Facebook field key.
     *
     * @return string
     */
    public function getFacebookFieldKey(): string
    {
        return $this->facebook_field_key;
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
                'facebook_field_key' => $mapping['facebook_field_key'],
                'contact_column' => $mapping['contact_column']
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
                    'facebook_field_key' => $mapping->getFacebookFieldKey(),
                    'contact_column' => $mapping->getContactColumn()
                ];
            })
            ->toArray();
    }
}
