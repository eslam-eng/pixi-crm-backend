<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Billable;

class Client extends Model
{
    use Filterable, HasFactory, Billable;

    protected $guarded = ['id'];

    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    // A client belongs to one area
    public function area()
    {
        return $this->belongsTo(Location::class, 'area_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
    //
    //    // A client can have many industries
    //    public function industries()
    //    {
    //        return $this->belongsToMany(Industry::class, 'client_industry');
    //    }
    //
    //    // A client can have many services
    //    public function services()
    //    {
    //        return $this->belongsToMany(Service::class, 'client_service')
    //            ->withPivot('category_id'); // Include category_id in the pivot table
    //    }
    //
    //    // A client can have many custom fields
    //    public function customFields()
    //    {
    //        return $this->belongsToMany(CustomField::class, 'client_custom_fields')
    //            ->withPivot('value'); // Include value in the pivot table
    //    }
}
