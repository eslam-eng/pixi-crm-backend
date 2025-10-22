<?php

namespace App\Enums\Central;

enum CustomerFeedbackStatusEnum: string
{
    case NEW = 'new';
    case RESPONDED = 'responded';
    case RESOLVED = 'resolved';
    case ESCALATED = 'escalated';

    public function getLabel(): string
    {
        return trans('feedback_status.'.$this->value);
    }

    public static function getLabels(): array
    {
        return [
            self::NEW->value => self::NEW->getLabel(),
            self::RESPONDED->value => self::RESPONDED->getLabel(),
            self::RESOLVED->value => self::RESOLVED->getLabel(),
            self::ESCALATED->value => self::ESCALATED->getLabel(),
        ];
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
