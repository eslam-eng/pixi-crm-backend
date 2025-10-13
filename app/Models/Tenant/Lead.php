<?php

namespace App\Models\Tenant;

use App\Enums\OpportunityStatus;
use App\Models\City;
use App\Models\CustomField;
use App\Models\Industry;
use App\Models\Reason;
use App\Models\Service;
use App\Models\Stage;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Lead extends Model implements Auditable
{

    use Filterable, AuditableTrait;

    protected $table = 'leads';
    protected $fillable = [
        'status',
        'contact_id',
        'stage_id',
        'is_qualifying',
        'deal_value',
        'win_probability',
        'expected_close_date',
        'assigned_to_id',
        'notes',
        'description',
    ];

    protected $casts = [
        'status' => OpportunityStatus::class,
        'is_qualifying' => 'boolean',
        'deal_value'           => 'decimal:2',
        'win_probability'      => 'decimal:0',
        'expected_close_date'  => 'date',
    ];

    // Lead belongs to a Contact
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function transformAudit(array $data): array
    {
        // dd($data,$this->getOriginal('stage_id') ,$this->getAttribute('stage_id'));
        if (Arr::has($data, 'new_values.stage_id')) {
            if ($this->getOriginal('stage_id')) {
                $data['old_values']['stage'] = Stage::find($this->getOriginal('stage_id'))->name;
            }
            if ($this->getAttribute('stage_id')) {
                $data['new_values']['stage'] = Stage::find($this->getAttribute('stage_id'))->name;
            }
            $data['new_values']['stage'] = Stage::find($this->getAttribute('stage_id'))?->name;
        }

        return $data;
    }

    // Lead belongs to a User (Sales Representative)
    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    // Lead belongs to a Reason (if lost)
    public function reason()
    {
        return $this->belongsTo(Reason::class);
    }

    // Lead has many Industries (Many-to-Many)
    public function industries()
    {
        return $this->belongsToMany(Industry::class, 'lead_industry')->withTimestamps();
    }

    // Lead has many Services (Many-to-Many)
    public function services()
    {
        return $this->belongsToMany(Service::class, 'lead_service')
            ->withPivot('category_id'); // Include category_id in the pivot table
    }

    // Lead has many Custom Fields (Many-to-Many)
    public function customFields()
    {
        return $this->belongsToMany(CustomField::class, 'lead_custom_fields')->withPivot('value')->withTimestamps();
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function variants()
    {
        return $this->belongsToMany(ItemVariant::class, 'leads_variants', 'lead_id', 'item_variant_id')->withPivot('quantity', 'price');
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'leads_items', 'lead_id', 'item_id')->withPivot('quantity', 'price');
    }

    // Lead has many Stages (Many-to-Many)
    public function stages()
    {
        return $this->belongsToMany(Stage::class, 'lead_stage')
            ->withPivot('start_date', 'exit_date', 'pipline_id')
            ->withTimestamps();
    }
}
