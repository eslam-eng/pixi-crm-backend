<?php

namespace App\Models\Tenant;

use App\Models\Tenant\Contact;
use App\Models\Tenant\Deal;
use App\Models\Tenant\Lead;
use App\Models\Tenant\Task;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CustomField extends Model
{
    use Filterable,HasTranslations;
    protected $fillable =
        [
            'form_section_id',
            'module',
            'type',
            'name',
            'label',
            'placeholder',
            'help_text',
            'options',
            'validation_rules',
            'is_required',
            'is_active',
            'ordering',
        ];

    protected $casts = [
        'label' => 'array',
        'placeholder' => 'array',
        'help_text' => 'array',
        'options' => 'array',
        'validation_rules' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'ordering' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $translatable = ['label', 'placeholder', 'help_text'];

    public function formSection()
    {
        return $this->belongsTo(FormSection::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    // A custom field can belong to many clients
    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'contact_custom_fields')
            ->withPivot('value');
    }

    public function leads()
    {
        return $this->belongsToMany(Lead::class, 'lead_custom_fields')
            ->withPivot('value');
    }

    public function deals()
    {
        return $this->belongsToMany(Deal::class, 'deal_custom_fields')
            ->withPivot('value');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_custom_fields')
            ->withPivot('value');
    }
}
