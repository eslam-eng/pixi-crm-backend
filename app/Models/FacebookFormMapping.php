<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FacebookFormMapping extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'facebook_form_mappings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'facebook_form_id',
        'form_name',
        'total_contacts_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_contacts_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the field mappings for this form.
     */
    public function fieldMappings(): HasMany
    {
        return $this->hasMany(FacebookFormFieldMapping::class, 'form_id', 'form_id');
    }

    /**
     * Get the form name.
     *
     * @return string|null
     */
    public function getFormName(): ?string
    {
        return $this->form_name;
    }

    /**
     * Set the form name.
     *
     * @param string $formName
     * @return void
     */
    public function setFormName(string $formName): void
    {
        $this->form_name = $formName;
    }

    /**
     * Get the total contacts count.
     *
     * @return int
     */
    public function getTotalContactsCount(): int
    {
        return $this->total_contacts_count ?? 0;
    }

    /**
     * Set the total contacts count.
     *
     * @param int $count
     * @return void
     */
    public function setTotalContactsCount(int $count): void
    {
        $this->total_contacts_count = $count;
    }

    /**
     * Increment the total contacts count.
     *
     * @param int $increment
     * @return void
     */
    public function incrementContactsCount(int $increment = 1): void
    {
        $this->total_contacts_count = ($this->total_contacts_count ?? 0) + $increment;
    }

    /**
     * Get the mappings array.
     *
     * @return array
     */
    public function getMappings(): array
    {
        return $this->fieldMappings->map(function ($mapping) {
            return [
                'facebook_field_key' => $mapping->getFacebookFieldKey(),
                'contact_column' => $mapping->getContactColumn(),
                'is_required' => $mapping->isRequired(),
            ];
        })->toArray();
    }

    /**
     * Set the mappings array.
     *
     * @param array $mappings
     * @return void
     */
    public function setMappings(array $mappings): void
    {
        FacebookFormFieldMapping::createMappings($this->form_id, $mappings);
    }

    /**
     * Get the form ID.
     *
     * @return string
     */
    public function getFormId(): string
    {
        return $this->form_id;
    }

    /**
     * Find mapping by form ID.
     *
     * @param string $formId
     * @return FacebookFormMapping|null
     */
    public static function findByFormId(string $formId): ?FacebookFormMapping
    {
        return static::where('form_id', $formId)->first();
    }

    /**
     * Create or update mapping for a form.
     *
     * @param string $formId
     * @param array $mappings
     * @param string|null $formName
     * @param int $contactsCount
     * @return FacebookFormMapping
     */
    public static function createOrUpdate(string $formId, array $mappings, ?string $formName = null, int $contactsCount = 0): FacebookFormMapping
    {
        $formMapping = static::updateOrCreate(
            ['form_id' => $formId],
            [
                'form_name' => $formName,
                'total_contacts_count' => $contactsCount
            ]
        );

        // Create field mappings
        FacebookFormFieldMapping::createMappings($formId, $mappings);

        return $formMapping;
    }
}