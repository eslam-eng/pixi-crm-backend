<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormField extends Model
{
    protected $fillable = [
        'form_id',
        'name',
        'label',
        'type',
        'options',
        'required',
        'placeholder',
        'order'
    ];

    protected $casts = [
        'options' => 'array',
        'required' => 'boolean'
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}
