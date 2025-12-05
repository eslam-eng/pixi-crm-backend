<?php

namespace App\DTO\Tenant\Opportunity;

use App\DTO\BaseDTO;
use Illuminate\Http\Request;

class SendOpportunityItemsDTO extends BaseDTO
{
    public function __construct(
        public string $channel,
        public ?string $subject,
        public array $selected_item_columns = [],
    ) {
    }

    public static function fromRequest(Request $request): static
    {
        return new self(
            channel: $request->validated('channel'),
            subject: $request->validated('subject'),
            selected_item_columns: $request->validated('selected_item_columns', []),
        );
    }

    public static function fromArray(array $data): static
    {
        return new self(
            channel: $data['channel'],
            subject: $data['subject'] ?? null,
            selected_item_columns: $data['selected_item_columns'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'channel' => $this->channel,
            'subject' => $this->subject,
            'selected_item_columns' => $this->selected_item_columns,
        ];
    }
}
