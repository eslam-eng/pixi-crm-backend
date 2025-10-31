<?php

namespace App\Events\Contacts;

use App\Models\Tenant\Contact;
use App\Models\Tenant\Lead;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContactLeadQualified
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Contact $contact;
    public Lead $lead;
    public array $qualificationData;

    /**
     * Create a new event instance.
     */
    public function __construct(Contact $contact, Lead $lead, array $qualificationData = [])
    {
        $this->contact = $contact;
        $this->lead = $lead;
        $this->qualificationData = $qualificationData;
    }
}
