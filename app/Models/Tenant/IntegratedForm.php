<?php

namespace App\Models\Tenant;

use App\Enums\PlatformEnum;
use App\Models\Tenant\IntegratedFormFieldMapping;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IntegratedForm extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'integrated_forms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_form_id',
        'form_name',
        'platform',
        'total_contacts_count',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_contacts_count' => 'integer',
        'is_active' => 'boolean',
        'platform' => PlatformEnum::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the field mappings for this form.
     */
    public function fieldMappings(): HasMany
    {
        return $this->hasMany(IntegratedFormFieldMapping::class, 'form_id');
    }

    /**
     * Get the platform label.
     *
     * @return string
     */
    public function getPlatformLabel(): string
    {
        return $this->platform->label();
    }

    /**
     * Check if the form is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->is_active ?? false;
    }

    /**
     * Set the form as active or inactive.
     *
     * @param bool $active
     * @return void
     */
    public function setActive(bool $active): void
    {
        $this->is_active = $active;
    }

    /**
     * Find forms by platform.
     *
     * @param PlatformEnum $platform
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function findByPlatform(PlatformEnum $platform)
    {
        return static::where('platform', $platform)->get();
    }

    /**
     * Get active forms by platform.
     *
     * @param PlatformEnum $platform
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getActiveByPlatform(PlatformEnum $platform)
    {
        return static::where('platform', $platform)
            ->where('is_active', true)
            ->get();
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
                'external_field_key' => $mapping->getExternalFieldKey(),
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
        IntegratedFormFieldMapping::createMappings($this->form_id, $mappings);
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
    public static function findByFormId(string $formId): ?IntegratedFormFieldMapping
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
    public static function createOrUpdate(string $formId, array $mappings, ?string $formName = null, int $contactsCount = 0): IntegratedFormFieldMapping
    {
        $formMapping = static::updateOrCreate(
            ['form_id' => $formId],
            [
                'form_name' => $formName,
                'total_contacts_count' => $contactsCount
            ]
        );

        // Create field mappings
        IntegratedFormFieldMapping::createMappings($formId, $mappings);

        return $formMapping;
    }
}