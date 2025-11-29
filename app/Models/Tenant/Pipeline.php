<?php

namespace App\Models\Tenant;

use App\Models\Tenant\LossReason;
use App\Models\Stage;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pipeline extends Model
{
    use Filterable;
    protected $fillable = ['name', 'is_default'];

    public function stages(): HasMany
    {
        return $this->hasMany(Stage::class);
    }

    public function firstStage(): HasOne
    {
        return $this->hasOne(Stage::class)->orderBy('id', 'asc');
    }

    public function lossReasons(): HasMany
    {
        return $this->hasMany(LossReason::class);
    }
}
