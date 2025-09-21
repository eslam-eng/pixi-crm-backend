<?php

namespace App\Models;

use App\Enums\ActivationStatus;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Location extends Model
{
    use HasFactory, NodeTrait, Filterable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['title', 'status', '_lft', '_lgt', 'parent_id'];

    public $casts = [
        'status' => ActivationStatus::class,
    ];


    public function parent()
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    public function getCityAncestor(): ?Location
    {
        return $this->ancestors()
            ->withDepth()
            ->having('depth', 1)
            ->first();
    }



    //////////////// scopes ////////////////
    public function scopeCountries($query)
    {
        return $query->withDepth()->having('depth', 0)->where('status', 1);
    }

    public function scopeGovernorates($query)
    {
        return $query->withDepth()->having('depth', 1)->where('status', 1);
    }

    public function scopeCities($query)
    {
        return $query->withDepth()->having('depth', 2)->where('status', 1);
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('status', 1);
    }

    public function clients()
    {
        return $this->hasMany(Client::class, 'city_id');
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'city_id');
    }
}
