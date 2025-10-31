<?php

namespace App\Events\Contacts;

use App\Models\Tenant\Contact;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContactTagAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Contact $contact;
    public string $tag;
    public array $tagData;

    /**
     * Create a new event instance.
     */
    public function __construct(Contact $contact, string $tag, array $tagData = [])
    {
        $this->contact = $contact;
        $this->tag = $tag;
        $this->tagData = $tagData;
    }
}
