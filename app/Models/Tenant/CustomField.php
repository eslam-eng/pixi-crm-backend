<?php

namespace App\Models\Tenant;

use App\Models\Tenant\Contact;
use App\Models\Tenant\Deal;
use App\Models\Tenant\Lead;
use App\Models\Tenant\Task;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    use Filterable;
    protected $fillable=
    [
        'name',
        'type',
        'options',
        'module',
        'is_required',
        'is_active',
        'label',
        'placeholder',
        'help_text',
    ];

    protected $casts = [
        'options' => 'array', // Cast JSON to array
    ];

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
