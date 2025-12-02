<?php

namespace App\Enums;

enum AutomationTriggersEnum: string
{
    // Contact Triggers
    case CONTACT_CREATED = 'contact_created';
    case CONTACT_UPDATED = 'contact_updated';

    // Opportunity/Lead Triggers
    case OPPORTUNITY_CREATED = 'opportunity_created';
    case OPPORTUNITY_LEAD_QUALIFIED = 'opportunity_lead_qualified';
    case OPPORTUNITY_STAGE_CHANGED = 'opportunity_stage_changed';
    case OPPORTUNITY_NO_ACTION_FOR_X_TIME = 'opportunity_no_action_for_x_time';
    case OPPORTUNITY_HIGH_VALUE = 'opportunity_high_value';
    case OPPORTUNITY_WON = 'opportunity_won';
    case OPPORTUNITY_LOST = 'opportunity_lost';

    // Deal Triggers
    case DEAL_CREATED = 'deal_created';
    case DEAL_UPDATED = 'deal_updated';
    case DEAL_OVERDUE_PAYMENT = 'deal_overdue_payment';

    // Task Triggers
    case TASK_CREATED = 'task_created';
    case TASK_COMPLETED = 'task_completed';
    case TASK_OVERDUE = 'task_overdue';

    // Calendar Triggers
    case CALENDAR_EVENT_CREATED = 'calendar_event_created';
    case CALENDAR_EVENT_CANCELLED = 'calendar_event_cancelled';
    case CALENDAR_ATTENDEE_RSVP = 'calendar_attendee_rsvp';

    // Form Triggers
    case FORM_SUBMITTED = 'form_submitted';
    case FORM_FIELD_MAPPING_ERROR = 'form_field_mapping_error';

    /**
     * Get all trigger keys
     */
    public static function keys(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get trigger label
     */
    public function label(): string
    {
        return match ($this) {
            self::CONTACT_CREATED => 'Contact Created',
            self::CONTACT_UPDATED => 'Contact Updated',
            self::OPPORTUNITY_CREATED => 'Opportunity Created',
            self::OPPORTUNITY_LEAD_QUALIFIED => 'Opportunity Qualified',
            self::OPPORTUNITY_STAGE_CHANGED => 'Stage Changed',
            self::OPPORTUNITY_NO_ACTION_FOR_X_TIME => 'No Action for X Time',
            self::OPPORTUNITY_HIGH_VALUE => 'High Value Opportunity',
            self::OPPORTUNITY_WON => 'Opportunity Won',
            self::OPPORTUNITY_LOST => 'Opportunity Lost',
            self::DEAL_CREATED => 'Deal Created',
            self::DEAL_UPDATED => 'Deal Updated',
            self::DEAL_OVERDUE_PAYMENT => 'Deal Overdue Payment',
            self::TASK_CREATED => 'Task Created',
            self::TASK_COMPLETED => 'Task Completed',
            self::TASK_OVERDUE => 'Task Overdue',
            self::CALENDAR_EVENT_CREATED => 'Event Created',
            self::CALENDAR_EVENT_CANCELLED => 'Event Cancelled',
            self::CALENDAR_ATTENDEE_RSVP => 'Attendee RSVP',
            self::FORM_SUBMITTED => 'Form Submitted',
            self::FORM_FIELD_MAPPING_ERROR => 'Field Mapping Error',
        };
    }

    /**
     * Get trigger icon
     */
    public function icon(): string
    {
        return match ($this) {
            self::CONTACT_CREATED => 'user-plus',
            self::CONTACT_UPDATED => 'user',
            self::OPPORTUNITY_CREATED => 'trending-up',
            self::OPPORTUNITY_LEAD_QUALIFIED => 'target',
            self::OPPORTUNITY_STAGE_CHANGED => 'arrow-right',
            self::OPPORTUNITY_NO_ACTION_FOR_X_TIME => 'clock',
            self::OPPORTUNITY_HIGH_VALUE => 'dollar-sign',
            self::OPPORTUNITY_WON => 'check-circle',
            self::OPPORTUNITY_LOST => 'x-circle',
            self::DEAL_CREATED => 'file-text',
            self::DEAL_UPDATED => 'edit',
            self::DEAL_OVERDUE_PAYMENT => 'alert-triangle',
            self::TASK_CREATED => 'check-square',
            self::TASK_COMPLETED => 'check',
            self::TASK_OVERDUE => 'clock',
            self::CALENDAR_EVENT_CREATED => 'calendar',
            self::CALENDAR_EVENT_CANCELLED => 'x',
            self::CALENDAR_ATTENDEE_RSVP => 'users',
            self::FORM_SUBMITTED => 'file-text',
            self::FORM_FIELD_MAPPING_ERROR => 'alert-circle',
        };
    }

    /**
     * Get trigger description
     */
    public function description(): string
    {
        return match ($this) {
            self::CONTACT_CREATED => 'New contact added manually or via import',
            self::CONTACT_UPDATED => 'Key fields changed (country, phone, email, etc.)',
            self::OPPORTUNITY_CREATED => 'New opportunity added (from form submission)',
            self::OPPORTUNITY_LEAD_QUALIFIED => 'Opportunity status changed to Qualified',
            self::OPPORTUNITY_STAGE_CHANGED => 'Opportunity moved between stages',
            self::OPPORTUNITY_NO_ACTION_FOR_X_TIME => 'No task/comment within the time',
            self::OPPORTUNITY_HIGH_VALUE => 'Amount crosses threshold',
            self::OPPORTUNITY_WON => 'Marked as Won',
            self::OPPORTUNITY_LOST => 'Marked as Lost',
            self::DEAL_CREATED => 'New deal/contract created post-win',
            self::DEAL_UPDATED => 'Deal amount/terms changed',
            self::DEAL_OVERDUE_PAYMENT => 'Payment not received by due date',
            self::TASK_CREATED => 'New task created (call/email/follow-up)',
            self::TASK_COMPLETED => 'Task marked as done',
            self::TASK_OVERDUE => 'Due date passed without completion',
            self::CALENDAR_EVENT_CREATED => 'Client books a meeting/demo',
            self::CALENDAR_EVENT_CANCELLED => 'Client cancels meeting',
            self::CALENDAR_ATTENDEE_RSVP => 'Client accepts/declines meeting',
            self::FORM_SUBMITTED => 'New lead from Meta/Website/Typeform',
            self::FORM_FIELD_MAPPING_ERROR => 'Form data incomplete or unmapped',
        };
    }

    /**
     * Get triggerable model class
     */
    public function triggerableModel(): string
    {
        return match ($this) {
            self::CONTACT_CREATED, self::CONTACT_UPDATED => \App\Models\Tenant\Contact::class,
            self::OPPORTUNITY_CREATED, self::OPPORTUNITY_LEAD_QUALIFIED,
            self::OPPORTUNITY_STAGE_CHANGED, self::OPPORTUNITY_NO_ACTION_FOR_X_TIME,
            self::OPPORTUNITY_HIGH_VALUE, self::OPPORTUNITY_WON,
            self::OPPORTUNITY_LOST => \App\Models\Tenant\Lead::class,
            self::DEAL_CREATED, self::DEAL_UPDATED,
            self::DEAL_OVERDUE_PAYMENT => \App\Models\Tenant\Deal::class,
            self::TASK_CREATED, self::TASK_COMPLETED,
            self::TASK_OVERDUE => \App\Models\Tenant\Task::class,
            self::FORM_SUBMITTED, self::FORM_FIELD_MAPPING_ERROR => \App\Models\Tenant\FormSubmission::class,
            default => null,
        };
    }
}
