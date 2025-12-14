<?php

namespace App\Enums;

use Illuminate\Support\Traits\Macroable;

enum OpportunityNoteTypeEnum: string
{
    case GENERAL_NOTE = 'general_note';
    case STRATEGY = 'strategy';
    case FOLLOW_UP = 'follow_up';
    case CONCERN = 'concern';
    case DECISION = 'decision';

    public function label(): string
    {
        return match ($this) {
            self::GENERAL_NOTE => trans('app.general_note'),
            self::STRATEGY => trans('app.strategy'),
            self::FOLLOW_UP => trans('app.follow_up'),
            self::CONCERN => trans('app.concern'),
            self::DECISION => trans('app.decision'),
        };
    }

    public static function options(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ], self::cases());
    }
}
