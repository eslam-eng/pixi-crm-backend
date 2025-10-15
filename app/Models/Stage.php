<?php

namespace App\Models;

use App\Models\Tenant\Lead;
use App\Models\Tenant\Pipeline;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    use Filterable;
    protected $fillable = ['name', 'probability', 'seq_number','pipeline_id'];

    public function pipeline()
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'stage_id');
    }
}
