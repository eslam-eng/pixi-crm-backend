<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\TranslatableFields;
use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    use Filterable, TranslatableFields;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name'];

    /**
     * The attributes that should be translatable.
     *
     * @var array<int, string>
     */
    public function getTranslatableAttributes(): array
    {
        return ['name'];
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_industry');
    }

    public function leads()
    {
        return $this->belongsToMany(\App\Models\Tenant\Lead::class, 'lead_industry');
    }
}
