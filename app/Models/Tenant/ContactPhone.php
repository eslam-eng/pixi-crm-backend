<?php

namespace App\Models\Tenant;


use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactPhone extends Model
{
    use HasFactory, Filterable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['contact_id', 'phone', 'is_primary', 'enable_whatsapp'];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
}
