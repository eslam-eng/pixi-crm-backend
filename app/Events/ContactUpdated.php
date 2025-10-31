<?php

namespace App\Events;

use App\Models\Tenant\Contact;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContactUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Contact $contact;
    public array $changedFields;

    /**
     * Create a new event instance.
     */
    public function __construct(Contact $contact, array $changedFields = [])
    {
        $this->contact = $contact;
        $this->changedFields = $changedFields;
    }
}
