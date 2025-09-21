<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Module extends Model
{
    use Filterable, HasTranslations;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'description'];
    
    public $translatable = ['name', 'group_label', 'number_field_label'];
    
    /**
     * Get the localized name attribute
     */
    public function getLocalizedNameAttribute()
    {
        return $this->getTranslation('name', app()->getLocale());
    }
    
    /**
     * Get the localized group label attribute
     */
    public function getLocalizedGroupLabelAttribute()
    {
        return $this->getTranslation('group_label', app()->getLocale());
    }
    
    /**
     * Get the localized number field label attribute
     */
    public function getLocalizedNumberFieldLabelAttribute()
    {
        return $this->getTranslation('number_field_label', app()->getLocale());
    }
    
    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_module');
    }
}
