<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use Filterable;
    protected $fillable = ['name', 'service_id', 'price'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
