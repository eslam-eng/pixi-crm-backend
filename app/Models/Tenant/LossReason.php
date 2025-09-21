<?php

namespace App\Models\Tenant;

use App\Models\Tenant\Pipeline;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

class LossReason extends Model
{
    use Filterable;
    protected $fillable = ['label', 'value', 'description', 'pipeline_id'];

    function pipeline()
    {
        return $this->belongsTo(Pipeline::class);
    }
}
