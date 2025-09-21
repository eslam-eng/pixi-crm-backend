<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use Filterable;
    protected $fillable = ['name','price'];

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    // A service can belong to many clients
    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_service')
            ->withPivot('category_id');
    }

    // A service can belong to many leads
    public function leads()
    {
        return $this->belongsToMany(Lead::class, 'lead_service')->withPivot('category_id');
    }
}
