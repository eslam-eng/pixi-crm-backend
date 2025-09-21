<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

class Reason extends Model
{
    use Filterable;
    protected $fillable=['name'];

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}
