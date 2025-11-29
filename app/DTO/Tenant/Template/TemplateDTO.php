<?php

namespace App\DTO\Tenant\Template;

use App\DTO\BaseDTO;
use App\DTO\Interfaces\DTOInterface;
use App\Models\Tenant\Template;
use Illuminate\Http\Request;

class TemplateDTO extends BaseDTO implements DTOInterface
{
    public function __construct(
        public string $name,
        public string $slug,
        public string $type,
        public ?string $subject,
        public string $body,
        public ?array $variables,
        public bool $is_active,
    ) {}

    public static function fromModel(Template $template): static
    {
        return new self(
            name: $template->name,
            slug: $template->slug,
            type: $template->type,
            subject: $template->subject,
            body: $template->body,
            variables: $template->variables,
            is_active: $template->is_active,
        );
    }

    public static function fromArray(array $data): static
    {
        return new self(
            name: $data['name'],
            slug: $data['slug'],
            type: $data['type'] ?? 'email',
            subject: $data['subject'] ?? null,
            body: $data['body'],
            variables: $data['variables'] ?? null,
            is_active: $data['is_active'] ?? true,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,
            'subject' => $this->subject,
            'body' => $this->body,
            'variables' => $this->variables,
            'is_active' => $this->is_active,
        ];
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->name,
            slug: $request->slug,
            type: $request->type ?? 'email',
            subject: $request->subject,
            body: $request->body,
            variables: $request->variables,
            is_active: $request->is_active ?? true,
        );
    }
}
