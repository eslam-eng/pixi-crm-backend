<?php

namespace App\Models\Tenant;

use App\Enums\OpportunityNoteTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpportunityNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'title',
        'type',
        'content',
        'user_id',
    ];

    protected $casts = [
        'type' => OpportunityNoteTypeEnum::class,
    ];

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
