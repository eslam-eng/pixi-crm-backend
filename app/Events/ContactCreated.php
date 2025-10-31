<?php

namespace App\Events;

use App\Models\Tenant\Contact;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContactCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Contact $contact;

    /**
     * Create a new event instance.
     */
    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }
}
