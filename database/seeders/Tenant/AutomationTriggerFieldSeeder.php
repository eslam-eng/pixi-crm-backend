<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\AutomationTrigger;
use App\Models\Tenant\AutomationTriggerField;
use Illuminate\Database\Seeder;

class AutomationTriggerFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedContactFields();
        $this->seedOpportunityFields();
        $this->seedDealFields();
        $this->seedTaskFields();
    }

    private function seedContactFields()
    {
        $triggers = AutomationTrigger::whereIn('key', ['contact_created', 'contact_updated'])->get();

        $fields = [
            // Direct fields
            ['field_name' => 'first_name', 'field_type' => 'string', 'field_label' => 'First Name', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Contact\'s first name', 'example_value' => 'John', 'order' => 1],
            ['field_name' => 'last_name', 'field_type' => 'string', 'field_label' => 'Last Name', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Contact\'s last name', 'example_value' => 'Smith', 'order' => 2],
            ['field_name' => 'email', 'field_type' => 'string', 'field_label' => 'Email', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Email address', 'example_value' => 'john@example.com', 'order' => 3],
            ['field_name' => 'status', 'field_type' => 'enum', 'field_label' => 'Status', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Contact status', 'example_value' => 'Active', 'order' => 4],
            ['field_name' => 'job_title', 'field_type' => 'string', 'field_label' => 'Job Title', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Job title', 'example_value' => 'Manager', 'order' => 5],
            ['field_name' => 'department', 'field_type' => 'enum', 'field_label' => 'Department', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Department', 'example_value' => 'Sales', 'order' => 6],
            ['field_name' => 'company_name', 'field_type' => 'string', 'field_label' => 'Company Name', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Company name', 'example_value' => 'Acme Corp', 'order' => 7],
            ['field_name' => 'campaign_name', 'field_type' => 'string', 'field_label' => 'Campaign Name', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Campaign source', 'example_value' => 'Summer 2024', 'order' => 8],
            ['field_name' => 'website', 'field_type' => 'string', 'field_label' => 'Website', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Company website', 'example_value' => 'https://example.com', 'order' => 9],
            ['field_name' => 'industry', 'field_type' => 'string', 'field_label' => 'Industry', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Industry', 'example_value' => 'Technology', 'order' => 10],
            ['field_name' => 'company_size', 'field_type' => 'string', 'field_label' => 'Company Size', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Company size', 'example_value' => '50-100', 'order' => 11],
            ['field_name' => 'address', 'field_type' => 'string', 'field_label' => 'Address', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Street address', 'example_value' => '123 Main St', 'order' => 12],
            ['field_name' => 'state', 'field_type' => 'string', 'field_label' => 'State', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'State/Province', 'example_value' => 'California', 'order' => 13],
            ['field_name' => 'zip_code', 'field_type' => 'string', 'field_label' => 'ZIP Code', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'ZIP/Postal code', 'example_value' => '90210', 'order' => 14],
            ['field_name' => 'country_id', 'field_type' => 'enum', 'field_label' => 'Country ID', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Country ID', 'example_value' => '1', 'order' => 15],
            ['field_name' => 'city_id', 'field_type' => 'enum', 'field_label' => 'City ID', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'City ID', 'example_value' => '1', 'order' => 16],
            ['field_name' => 'source_id', 'field_type' => 'enum', 'field_label' => 'Source ID', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Lead source ID', 'example_value' => '5', 'order' => 17],
            ['field_name' => 'user_id', 'field_type' => 'enum', 'field_label' => 'Assigned User ID', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Assigned user ID', 'example_value' => '1', 'order' => 18],
            ['field_name' => 'contact_method', 'field_type' => 'enum', 'field_label' => 'Contact Method', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Preferred contact method', 'example_value' => 'email', 'order' => 19],
            ['field_name' => 'email_permission', 'field_type' => 'boolean', 'field_label' => 'Email Permission', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Email permission', 'example_value' => 'true', 'order' => 20],
            ['field_name' => 'phone_permission', 'field_type' => 'boolean', 'field_label' => 'Phone Permission', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Phone permission', 'example_value' => 'true', 'order' => 21],
            ['field_name' => 'whatsapp_permission', 'field_type' => 'boolean', 'field_label' => 'WhatsApp Permission', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'WhatsApp permission', 'example_value' => 'true', 'order' => 22],
            ['field_name' => 'notes', 'field_type' => 'text', 'field_label' => 'Notes', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Contact notes', 'example_value' => 'Important client', 'order' => 23],

            // Relationship fields
            ['field_name' => 'source.name', 'field_type' => 'enum', 'field_label' => 'Source Name', 'field_category' => 'relationship', 'is_relationship' => true, 'description' => 'Lead source name', 'example_value' => 'Website', 'order' => 24],
            ['field_name' => 'user.first_name', 'field_type' => 'string', 'field_label' => 'Assigned User Name', 'field_category' => 'relationship', 'is_relationship' => true, 'description' => 'Assigned user first name', 'example_value' => 'John', 'order' => 25],
            ['field_name' => 'city.name', 'field_type' => 'enum', 'field_label' => 'City Name', 'field_category' => 'relationship', 'is_relationship' => true, 'description' => 'City name', 'example_value' => 'Los Angeles', 'order' => 26],
            ['field_name' => 'country.name', 'field_type' => 'enum', 'field_label' => 'Country Name', 'field_category' => 'relationship', 'is_relationship' => true, 'description' => 'Country name', 'example_value' => 'United States', 'order' => 27],
        ];

        foreach ($triggers as $trigger) {
            foreach ($fields as $field) {
                AutomationTriggerField::create(array_merge($field, ['automation_trigger_id' => $trigger->id]));
            }
        }
    }

    private function seedOpportunityFields()
    {
        $triggers = AutomationTrigger::whereIn('key', [
            'opportunity_created',
            'opportunity_lead_qualified',
            'opportunity_stage_changed',
            'opportunity_no_action_for_x_time',
            'opportunity_high_value',
            'opportunity_won',
            'opportunity_lost'
        ])->get();

        $fields = [
            // Direct fields
            ['field_name' => 'status', 'field_type' => 'enum', 'field_label' => 'Status', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Opportunity status', 'example_value' => 'Active', 'order' => 1],
            ['field_name' => 'deal_value', 'field_type' => 'decimal', 'field_label' => 'Deal Value', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Deal value/amount', 'example_value' => '10000.00', 'order' => 2],
            ['field_name' => 'win_probability', 'field_type' => 'decimal', 'field_label' => 'Win Probability', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Win probability percentage', 'example_value' => '75', 'order' => 3],
            ['field_name' => 'expected_close_date', 'field_type' => 'date', 'field_label' => 'Expected Close Date', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Expected close date', 'example_value' => '2024-12-31', 'order' => 4],
            ['field_name' => 'assigned_to_id', 'field_type' => 'enum', 'field_label' => 'Assigned User ID', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Assigned user ID', 'example_value' => '1', 'order' => 5],
            ['field_name' => 'stage_id', 'field_type' => 'enum', 'field_label' => 'Stage ID', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Current stage ID', 'example_value' => '3', 'order' => 6],
            ['field_name' => 'contact_id', 'field_type' => 'enum', 'field_label' => 'Contact ID', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Related contact ID', 'example_value' => '1', 'order' => 7],
            ['field_name' => 'is_qualifying', 'field_type' => 'boolean', 'field_label' => 'Is Qualifying', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Is qualifying', 'example_value' => 'true', 'order' => 8],
            ['field_name' => 'notes', 'field_type' => 'text', 'field_label' => 'Notes', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Opportunity notes', 'example_value' => 'High priority deal', 'order' => 9],
            ['field_name' => 'description', 'field_type' => 'text', 'field_label' => 'Description', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Description', 'example_value' => 'Enterprise deal', 'order' => 10],

            // Relationship fields
            ['field_name' => 'contact.email', 'field_type' => 'string', 'field_label' => 'Contact Email', 'field_category' => 'relationship', 'is_relationship' => true, 'description' => 'Contact email address', 'example_value' => 'john@example.com', 'order' => 11],
            ['field_name' => 'contact.first_name', 'field_type' => 'string', 'field_label' => 'Contact First Name', 'field_category' => 'relationship', 'is_relationship' => true, 'description' => 'Contact first name', 'example_value' => 'John', 'order' => 12],
            ['field_name' => 'contact.company_name', 'field_type' => 'string', 'field_label' => 'Company Name', 'field_category' => 'relationship', 'is_relationship' => true, 'description' => 'Company name', 'example_value' => 'Acme Corp', 'order' => 13],
            ['field_name' => 'contact.source.name', 'field_type' => 'enum', 'field_label' => 'Lead Source', 'field_category' => 'relationship', 'is_relationship' => true, 'description' => 'Lead source name', 'example_value' => 'Referral', 'order' => 14],
            ['field_name' => 'user.first_name', 'field_type' => 'string', 'field_label' => 'Assigned User Name', 'field_category' => 'relationship', 'is_relationship' => true, 'description' => 'Assigned user name', 'example_value' => 'Sarah', 'order' => 15],
            ['field_name' => 'stage.name', 'field_type' => 'enum', 'field_label' => 'Stage Name', 'field_category' => 'relationship', 'is_relationship' => true, 'description' => 'Stage name', 'example_value' => 'Proposal', 'order' => 16],
        ];

        foreach ($triggers as $trigger) {
            foreach ($fields as $field) {
                AutomationTriggerField::create(array_merge($field, ['automation_trigger_id' => $trigger->id]));
            }
        }
    }

    private function seedDealFields()
    {
        $triggers = AutomationTrigger::whereIn('key', ['deal_created', 'deal_updated', 'deal_overdue_payment'])->get();

        $fields = [
            // Direct fields
            ['field_name' => 'deal_name', 'field_type' => 'string', 'field_label' => 'Deal Name', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Deal name', 'example_value' => 'Enterprise Contract', 'order' => 1],
            ['field_name' => 'total_amount', 'field_type' => 'decimal', 'field_label' => 'Total Amount', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Total amount', 'example_value' => '5000.00', 'order' => 2],
            ['field_name' => 'partial_amount_paid', 'field_type' => 'decimal', 'field_label' => 'Amount Paid', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Amount paid', 'example_value' => '2000.00', 'order' => 3],
            ['field_name' => 'amount_due', 'field_type' => 'decimal', 'field_label' => 'Amount Due', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Amount due', 'example_value' => '3000.00', 'order' => 4],
            ['field_name' => 'payment_status', 'field_type' => 'enum', 'field_label' => 'Payment Status', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Payment status', 'example_value' => 'Pending', 'order' => 5],
            ['field_name' => 'approval_status', 'field_type' => 'enum', 'field_label' => 'Approval Status', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Approval status', 'example_value' => 'Approved', 'order' => 6],
            ['field_name' => 'sale_date', 'field_type' => 'date', 'field_label' => 'Sale Date', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Sale date', 'example_value' => '2024-01-15', 'order' => 7],
            ['field_name' => 'discount_type', 'field_type' => 'enum', 'field_label' => 'Discount Type', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Discount type', 'example_value' => 'percentage', 'order' => 8],
            ['field_name' => 'discount_value', 'field_type' => 'decimal', 'field_label' => 'Discount Value', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Discount value', 'example_value' => '10', 'order' => 9],
            ['field_name' => 'tax_rate', 'field_type' => 'decimal', 'field_label' => 'Tax Rate', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Tax rate', 'example_value' => '8.5', 'order' => 10],
            ['field_name' => 'lead_id', 'field_type' => 'enum', 'field_label' => 'Lead ID', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Related lead ID', 'example_value' => '1', 'order' => 11],
            ['field_name' => 'assigned_to_id', 'field_type' => 'enum', 'field_label' => 'Assigned User ID', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Assigned user ID', 'example_value' => '1', 'order' => 12],
            ['field_name' => 'notes', 'field_type' => 'text', 'field_label' => 'Notes', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Deal notes', 'example_value' => 'Important deal', 'order' => 13],

            // Relationship fields
            ['field_name' => 'lead.status', 'field_type' => 'enum', 'field_label' => 'Lead Status', 'field_category' => 'relationship', 'is_relationship' => true, 'description' => 'Lead status', 'example_value' => 'Won', 'order' => 14],
            ['field_name' => 'lead.contact.email', 'field_type' => 'string', 'field_label' => 'Contact Email', 'field_category' => 'relationship', 'is_relationship' => true, 'description' => 'Contact email', 'example_value' => 'john@example.com', 'order' => 15],
            ['field_name' => 'lead.contact.company_name', 'field_type' => 'string', 'field_label' => 'Company Name', 'field_category' => 'relationship', 'is_relationship' => true, 'description' => 'Company name', 'example_value' => 'Acme Corp', 'order' => 16],
            ['field_name' => 'assigned_to.first_name', 'field_type' => 'string', 'field_label' => 'Assigned User Name', 'field_category' => 'relationship', 'is_relationship' => true, 'description' => 'Assigned user name', 'example_value' => 'John', 'order' => 17],
        ];

        foreach ($triggers as $trigger) {
            foreach ($fields as $field) {
                AutomationTriggerField::create(array_merge($field, ['automation_trigger_id' => $trigger->id]));
            }
        }
    }

    private function seedTaskFields()
    {
        $triggers = AutomationTrigger::whereIn('key', ['task_created', 'task_completed', 'task_overdue'])->get();

        $fields = [
            // Direct fields
            ['field_name' => 'title', 'field_type' => 'string', 'field_label' => 'Title', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Task title', 'example_value' => 'Follow-up call', 'order' => 1],
            ['field_name' => 'description', 'field_type' => 'text', 'field_label' => 'Description', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Task description', 'example_value' => 'Call the client', 'order' => 2],
            ['field_name' => 'status', 'field_type' => 'enum', 'field_label' => 'Status', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Task status', 'example_value' => 'Pending', 'order' => 3],
            ['field_name' => 'priority_id', 'field_type' => 'enum', 'field_label' => 'Priority ID', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Priority ID', 'example_value' => '1', 'order' => 4],
            ['field_name' => 'task_type_id', 'field_type' => 'enum', 'field_label' => 'Task Type ID', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Task type ID', 'example_value' => '2', 'order' => 5],
            ['field_name' => 'due_date', 'field_type' => 'date', 'field_label' => 'Due Date', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Due date', 'example_value' => '2024-12-31', 'order' => 6],
            ['field_name' => 'due_time', 'field_type' => 'string', 'field_label' => 'Due Time', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Due time', 'example_value' => '14:00', 'order' => 7],
            ['field_name' => 'assigned_to_id', 'field_type' => 'enum', 'field_label' => 'Assigned User ID', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Assigned user ID', 'example_value' => '1', 'order' => 8],
            ['field_name' => 'lead_id', 'field_type' => 'enum', 'field_label' => 'Lead ID', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Related lead ID', 'example_value' => '1', 'order' => 9],
            ['field_name' => 'additional_notes', 'field_type' => 'text', 'field_label' => 'Additional Notes', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Additional notes', 'example_value' => 'Important task', 'order' => 10],
            ['field_name' => 'escalation_sent', 'field_type' => 'boolean', 'field_label' => 'Escalation Sent', 'field_category' => 'direct', 'is_relationship' => false, 'description' => 'Escalation sent', 'example_value' => 'false', 'order' => 11],

            // Relationship fields
            ['field_name' => 'lead.status', 'field_type' => 'enum', 'field_label' => 'Lead Status', 'field_category' => 'relationship', 'is_relationship' => true, 'description' => 'Lead status', 'example_value' => 'Active', 'order' => 12],
            ['field_name' => 'lead.deal_value', 'field_type' => 'decimal', 'field_label' => 'Deal Value', 'field_category' => 'relationship', 'is_relationship' => true, 'description' => 'Deal value', 'example_value' => '10000.00', 'order' => 13],
            ['field_name' => 'lead.contact.email', 'field_type' => 'string', 'field_label' => 'Contact Email', 'field_category' => 'relationship', 'is_relationship' => true, 'description' => 'Contact email', 'example_value' => 'john@example.com', 'order' => 14],
            ['field_name' => 'assignedTo.first_name', 'field_type' => 'string', 'field_label' => 'Assigned User Name', 'field_category' => 'relationship', 'is_relationship' => true, 'description' => 'Assigned user name', 'example_value' => 'John', 'order' => 15],
            ['field_name' => 'priority.name', 'field_type' => 'enum', 'field_label' => 'Priority Name', 'field_category' => 'relationship', 'is_relationship' => true, 'description' => 'Priority name', 'example_value' => 'High', 'order' => 16],
            ['field_name' => 'taskType.name', 'field_type' => 'enum', 'field_label' => 'Task Type Name', 'field_category' => 'relationship', 'is_relationship' => true, 'description' => 'Task type name', 'example_value' => 'Call', 'order' => 17],
        ];

        foreach ($triggers as $trigger) {
            foreach ($fields as $field) {
                AutomationTriggerField::create(array_merge($field, ['automation_trigger_id' => $trigger->id]));
            }
        }
    }
}
