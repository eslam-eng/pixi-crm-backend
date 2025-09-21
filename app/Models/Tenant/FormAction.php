<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormAction extends Model
{
    protected $fillable = [
        'form_id',
        'type',
        'settings',
        'is_active',
        'order'
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean'
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}