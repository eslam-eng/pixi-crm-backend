<?php

namespace App\Models\Tenant;

use App\Models\Tenant\LossReason;
use App\Models\Stage;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pipeline extends Model
{
    use Filterable;
    protected $fillable = ['name'];

    public function stages(): HasMany
    {
        return $this->hasMany(Stage::class);
    }

    public function lossReasons(): HasMany
    {
        return $this->hasMany(LossReason::class);
    }
}
