<?php

namespace App\Models\Tenant;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemStatus extends Model
{
    use Filterable, HasFactory;
    protected $fillable =
    [
        'name',
        'status',
        'color',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
