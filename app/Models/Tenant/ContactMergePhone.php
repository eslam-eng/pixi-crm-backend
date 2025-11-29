<?php

namespace App\Models\Tenant;


use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMergePhone extends Model
{
    use HasFactory, Filterable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['contact_merge_id', 'phone', 'is_primary', 'enable_whatsapp'];

    public function contactMerge()
    {
        return $this->belongsTo(ContactMerge::class, 'contact_merge_id');
    }
}
