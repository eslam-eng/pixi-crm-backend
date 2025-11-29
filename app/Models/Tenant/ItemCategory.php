<?php

namespace App\Models\Tenant;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ItemCategory extends Model
{
    use Filterable, HasFactory;
    protected $fillable =
    [
        'name',
        'parent_id',
        'type',
    ];

    public function items()
    {
        return $this->hasMany(Item::class, 'category_id', "id");
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    public function children()
    {
        return $this->hasMany(ItemCategory::class, 'parent_id');
    }

    // The parent category
    public function parent()
    {
        return $this->belongsTo(ItemCategory::class, 'parent_id');
    }

    public function scopeRoots($q)
    {
        return $q->whereNull($this->qualifyColumn('parent_id'));
    }
}
