# Fire Workflow System Documentation

## Overview

The Fire Workflow system automatically executes automation workflows when specific triggers occur in the system. This implementation supports six main triggers:

- `contact_created` - Fires whenever a new contact is created
- `contact_updated` - Fires whenever an existing contact is updated with field changes
- `contact_lead_qualified` - Fires whenever a contact's lead becomes qualified
- `opportunity_created` - Fires whenever a new opportunity (lead) is created
- `opportunity_stage_changed` - Fires whenever an opportunity's stage changes
- `contact_tag_added` - Fires whenever a new tag is added to a contact

## System Architecture

### Core Components

1. **AutomationStepsImplement Model** - Stores individual workflow step implementations for each triggered workflow
2. **AutomationDelay Model** - Manages delayed workflow steps that need to be executed later
3. **AutomationWorkflowFireService** - Handles firing workflows when triggers occur
4. **AutomationWorkflowExecutorService** - Executes individual workflow steps (conditions, actions, delays)
5. **ContactCreated Event & Listener** - Event-driven system for contact creation
6. **ProcessAutomationDelays Command** - Cron job command to process delayed steps

### Database Tables

#### automation_steps_implements
- Stores workflow step implementations for each triggered workflow
- Contains same columns as `automation_workflow_steps` plus:
  - `step_order` - Order of execution
  - `implemented` - Whether the step has been executed
  - `triggerable_type` & `triggerable_id` - Polymorphic relationship to the entity that triggered the workflow
  - `step_data` - JSON data containing step configuration
  - `context_data` - JSON data containing context for execution

#### automation_delays
- Stores delayed workflow steps for cron job processing
- Contains:
  - `duration` & `unit` - Delay duration and unit (minutes, hours, days)
  - `execute_at` - When the step should be executed
  - `processed` - Whether the delay has been processed
  - `context_data` - Context data for delayed execution

## Setup Instructions

### 1. Run Migrations

```bash
php artisan migrate
```

### 2. Set Up Cron Job

Add the following to your crontab to process delayed automation steps every 5 minutes:

```bash
* * * * * cd /path/to/your/project && php artisan automation:process-delays >> /dev/null 2>&1
```

Or for more frequent processing (every minute):

```bash
* * * * * cd /path/to/your/project && php artisan automation:process-delays >> /dev/null 2>&1
```

### 3. Configure Queue (Optional)

For better performance, configure a queue system to handle the event processing:

```bash
# In .env file
QUEUE_CONNECTION=database
# or
QUEUE_CONNECTION=redis
```

Then run the queue worker:

```bash
php artisan queue:work
```

## How It Works

### 1. Contact Creation Flow

1. **Contact Created** - When a contact is created via `ContactService::store()`
2. **Event Dispatched** - `ContactCreated` event is dispatched
3. **Listener Triggered** - `FireContactCreatedWorkflows` listener processes the event
4. **Workflows Fired** - `AutomationWorkflowFireService` finds active workflows for `contact_created` trigger
5. **Steps Implemented** - Workflow steps are copied to `automation_steps_implements` table
6. **Execution** - Steps are executed immediately or scheduled for later execution

### 2. Contact Update Flow

1. **Contact Updated** - When a contact is updated via `ContactService::update()`
2. **Change Detection** - System detects which fields have changed
3. **Event Dispatched** - `ContactUpdated` event is dispatched with changed fields data
4. **Listener Triggered** - `FireContactUpdatedWorkflows` listener processes the event
5. **Workflows Fired** - `AutomationWorkflowFireService` finds active workflows for `contact_updated` trigger
6. **Steps Implemented** - Workflow steps are copied to `automation_steps_implements` table
7. **Execution** - Steps are executed immediately or scheduled for later execution

### 3. Contact Lead Qualification Flow

1. **Lead Updated** - When a lead is updated via `LeadService::update()`
2. **Qualification Check** - System checks if lead became qualified based on:
   - Status change to 'active'
   - Stage change to Stage 2 or higher
   - Deal value > 0 and status is 'active'
3. **Event Dispatched** - `ContactLeadQualified` event is dispatched with qualification data
4. **Listener Triggered** - `FireContactLeadQualifiedWorkflows` listener processes the event
5. **Workflows Fired** - `AutomationWorkflowFireService` finds active workflows for `contact_lead_qualified` trigger
6. **Steps Implemented** - Workflow steps are copied to `automation_steps_implements` table
7. **Execution** - Steps are executed immediately or scheduled for later execution

### 4. Opportunity Creation Flow

1. **Opportunity Created** - When an opportunity (lead) is created via `LeadService::store()`
2. **Configuration Applied** - System applies configuration requirements:
   - **Required Fields**: amount (deal_value), country
   - **Default Pipeline**: Sales
3. **Creation Data Prepared** - System prepares creation context data (deal value, status, stage, etc.)
4. **Event Dispatched** - `OpportunityCreated` event is dispatched with creation data and configuration
5. **Listener Triggered** - `FireOpportunityCreatedWorkflows` listener processes the event
6. **Workflows Fired** - `AutomationWorkflowFireService` finds active workflows for `opportunity_created` trigger
7. **Steps Implemented** - Workflow steps are copied to `automation_steps_implements` table
8. **Execution** - Steps are executed immediately or scheduled for later execution

### 5. Opportunity Stage Change Flow

1. **Opportunity Updated** - When an opportunity is updated via `LeadService::update()`
2. **Stage Detection** - System detects if the stage_id has changed
3. **Event Dispatched** - `OpportunityStageChanged` event is dispatched with stage change data
4. **Listener Triggered** - `FireOpportunityStageChangedWorkflows` listener processes the event
5. **Workflows Fired** - `AutomationWorkflowFireService` finds active workflows for `opportunity_stage_changed` trigger
6. **Steps Implemented** - Workflow steps are copied to `automation_steps_implements` table
7. **Execution** - Steps are executed immediately or scheduled for later execution

### 6. Contact Tag Addition Flow

1. **Contact Updated** - When a contact is updated via `ContactService::update()`
2. **Tag Detection** - System detects if new tags were added to the contact
3. **Event Dispatched** - `ContactTagAdded` event is dispatched for each new tag
4. **Listener Triggered** - `FireContactTagAddedWorkflows` listener processes the event
5. **Workflows Fired** - `AutomationWorkflowFireService` finds active workflows for `contact_tag_added` trigger
6. **Steps Implemented** - Workflow steps are copied to `automation_steps_implements` table
7. **Execution** - Steps are executed immediately or scheduled for later execution

### 7. Step Execution Flow

#### Immediate Steps (Conditions & Actions)
1. **Condition Steps** - Evaluated immediately against the contact data
2. **Action Steps** - Executed immediately if conditions pass
3. **Marked as Implemented** - Steps are marked as completed

#### Delayed Steps
1. **Delay Record Created** - `AutomationDelay` record created with `execute_at` timestamp
2. **Cron Processing** - `ProcessAutomationDelays` command runs periodically
3. **Ready Steps Executed** - Steps with `execute_at <= now()` are executed
4. **Workflow Continuation** - System automatically continues with next steps after delay
5. **Marked as Processed** - Delay records are marked as processed

#### Delay Continuation Logic

When a delay step is completed, the system automatically:

1. **Finds Next Steps** - Locates all pending steps with `step_order > completed_delay_step_order`
2. **Executes Immediately** - Runs condition and action steps in order
3. **Schedules New Delays** - Creates new delay records for subsequent delay steps
4. **Stops at Delays** - Pauses execution chain when encountering new delay steps
5. **Logs Progress** - Records continuation activity for debugging

### 8. Trigger Configurations

Each trigger can have specific configuration requirements:

#### opportunity_created Configuration
- **Required Fields**: 
  - `amount` (deal_value) - Must be numeric and >= 0
  - `country` - Must be a valid string
- **Default Pipeline**: Sales
- **Validation Rules**: Built-in validation for required fields

### 9. Step Types

#### Condition Steps
- Evaluate contact data against specified criteria
- Support operations: equals, not_equals, contains, greater_than, etc.
- Can access nested fields (e.g., `user.name`, `source.title`)

#### Action Steps
- Perform actions on the contact or system
- Supported actions: send_email, assign_user, update_status, add_tag, create_task, send_notification
- Custom actions can be added to `AutomationWorkflowExecutorService`

#### Delay Steps
- Pause workflow execution for specified duration
- Support units: minutes, hours, days
- Processed by cron job for accurate timing

## Usage Examples

### Creating a Workflow

1. **Create Automation Trigger** (if not exists):
```php
AutomationTrigger::create([
    'key' => 'contact_created',
    'name' => ['en' => 'Contact Created'],
    'description' => 'Triggered when a new contact is created',
    'is_active' => true
]);
```

2. **Create Automation Actions**:
```php
AutomationAction::create([
    'key' => 'send_welcome_email',
    'name' => ['en' => 'Send Welcome Email'],
    'description' => 'Send welcome email to new contact',
    'is_active' => true
]);
```

3. **Create Workflow with Steps**:
```php
$workflow = AutomationWorkflow::create([
    'name' => 'New Contact Welcome Flow',
    'description' => 'Welcome new contacts with email and assignment',
    'automation_trigger_id' => $trigger->id,
    'is_active' => true
]);

// Add condition step
AutomationWorkflowStep::create([
    'automation_workflow_id' => $workflow->id,
    'type' => 'condition',
    'order' => 1
]);

AutomationWorkflowStepCondition::create([
    'automation_workflow_step_id' => $step->id,
    'field' => 'email',
    'operation' => 'is_not_empty',
    'value' => ''
]);

// Add action step
AutomationWorkflowStep::create([
    'automation_workflow_id' => $workflow->id,
    'type' => 'action',
    'order' => 2
]);

AutomationWorkflowStepAction::create([
    'automation_workflow_step_id' => $step->id,
    'automation_action_id' => $action->id
]);

// Add delay step
AutomationWorkflowStep::create([
    'automation_workflow_id' => $workflow->id,
    'type' => 'delay',
    'order' => 3
]);

AutomationWorkflowStepDelay::create([
    'automation_workflow_step_id' => $step->id,
    'duration' => 30,
    'unit' => 'minutes'
]);
```

### Monitoring Workflow Execution

```php
// Get pending steps for a contact
$pendingSteps = AutomationStepsImplement::getPendingStepsForEntity($contact);

// Get all pending steps
$allPendingSteps = AutomationStepsImplement::getAllPendingSteps();

// Get delays ready to execute
$readyDelays = AutomationDelay::getReadyToExecute();

// Check workflow execution status
$workflowRuns = AutomationWorkflow::where('id', $workflowId)->value('total_runs');
```

## API Endpoints

The system integrates with existing automation workflow API endpoints:

- `GET /api/automation-workflows` - List all workflows
- `POST /api/automation-workflows` - Create new workflow
- `GET /api/automation-workflows/{id}` - Get workflow details
- `PUT /api/automation-workflows/{id}` - Update workflow
- `DELETE /api/automation-workflows/{id}` - Delete workflow

## Troubleshooting

### Common Issues

1. **Workflows Not Firing**
   - Check if `contact_created` trigger exists and is active
   - Verify workflow is active
   - Check event listener registration in `EventServiceProvider`

2. **Delayed Steps Not Executing**
   - Verify cron job is running
   - Check `automation_delays` table for pending delays
   - Run command manually: `php artisan automation:process-delays`

3. **Steps Not Implementing**
   - Check database constraints and foreign keys
   - Verify step data is properly formatted
   - Check logs for execution errors

### Logging

The system logs all workflow execution activities:

```bash
tail -f storage/logs/laravel.log | grep "automation"
```

### Manual Testing

```bash
# Test delayed step processing
php artisan automation:process-delays

# Check pending steps
php artisan tinker
>>> App\Models\Tenant\AutomationStepsImplement::pending()->count()
>>> App\Models\Tenant\AutomationDelay::pending()->count()
```

## Performance Considerations

1. **Queue Processing** - Use queues for better performance with high contact creation volumes
2. **Database Indexing** - Ensure proper indexes on `automation_steps_implements` and `automation_delays` tables
3. **Cron Frequency** - Adjust cron frequency based on delay requirements (minimum 1 minute)
4. **Batch Processing** - Consider batch processing for high-volume scenarios

## Future Enhancements

1. **Additional Triggers** - Support for other entity creation/update triggers
2. **Advanced Conditions** - More complex condition logic
3. **Custom Actions** - Plugin system for custom actions
4. **Workflow Templates** - Pre-built workflow templates
5. **Analytics** - Workflow execution analytics and reporting
6. **Webhooks** - External webhook support for actions
7. **Conditional Branching** - Support for if/else workflow branches
