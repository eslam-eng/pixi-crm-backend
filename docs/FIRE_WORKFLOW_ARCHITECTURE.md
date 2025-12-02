# Fire Workflow System - Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                              FIRE WORKFLOW SYSTEM                              │
└─────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────┐    ┌──────────────────┐    ┌─────────────────────────────────┐
│   Contact       │───▶│  ContactCreated  │───▶│  FireContactCreatedWorkflows   │
│   Created       │    │     Event        │    │        Listener                 │
└─────────────────┘    └──────────────────┘    └─────────────────────────────────┘
                                                           │
                                                           ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│                    AutomationWorkflowFireService                               │
│  • Find active workflows for 'contact_created' trigger                         │
│  • Create step implementations in automation_steps_implements table            │
│  • Handle delay steps by creating AutomationDelay records                     │
└─────────────────────────────────────────────────────────────────────────────────┘
                                                           │
                                                           ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│                    AutomationWorkflowExecutorService                           │
│  • Execute condition steps (immediate evaluation)                              │
│  • Execute action steps (immediate execution)                                 │
│  • Handle delay steps (schedule for later)                                    │
└─────────────────────────────────────────────────────────────────────────────────┘
                                                           │
                                                           ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│                              DATABASE TABLES                                  │
│                                                                                 │
│  automation_steps_implements          automation_delays                          │
│  ├─ id                               ├─ id                                     │
│  ├─ automation_workflow_id           ├─ automation_steps_implement_id         │
│  ├─ automation_workflow_step_id      ├─ duration                               │
│  ├─ triggerable_type                 ├─ unit                                   │
│  ├─ triggerable_id                   ├─ execute_at                            │
│  ├─ type                             ├─ processed                              │
│  ├─ step_order                       ├─ processed_at                          │
│  ├─ implemented                      └─ context_data                           │
│  ├─ step_data                                                                  │
│  ├─ context_data                                                               │
│  └─ implemented_at                                                             │
└─────────────────────────────────────────────────────────────────────────────────┘
                                                           │
                                                           ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│                              CRON JOB SYSTEM                                  │
│                                                                                 │
│  ProcessAutomationDelays Command                                               │
│  ├─ Runs every 5 minutes (configurable)                                        │
│  ├─ Finds delays with execute_at <= now()                                     │
│  ├─ Executes pending steps                                                     │
│  └─ Marks delays as processed                                                 │
└─────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────────┐
│                              STEP TYPES                                        │
│                                                                                 │
│  CONDITION STEPS                    ACTION STEPS                    DELAY STEPS │
│  ├─ Evaluate contact data          ├─ Send email                   ├─ Pause execution │
│  ├─ Support multiple operations    ├─ Assign user                 ├─ Configurable duration │
│  ├─ Nested field access           ├─ Update status                ├─ Processed by cron │
│  └─ Immediate execution           ├─ Add tags                     └─ Accurate timing │
│                                   ├─ Create tasks                              │
│                                   ├─ Send notifications                       │
│                                   └─ Custom actions                           │
└─────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────────┐
│                              WORKFLOW EXECUTION FLOW                          │
│                                                                                 │
│  1. Contact Created                                                             │
│     ↓                                                                            │
│  2. ContactCreated Event Dispatched                                            │
│     ↓                                                                            │
│  3. FireContactCreatedWorkflows Listener                                       │
│     ↓                                                                            │
│  4. AutomationWorkflowFireService                                               │
│     ├─ Find active workflows                                                    │
│     ├─ Create step implementations                                              │
│     └─ Create delay records (if needed)                                        │
│     ↓                                                                            │
│  5. AutomationWorkflowExecutorService                                           │
│     ├─ Execute condition steps (immediate)                                      │
│     ├─ Execute action steps (immediate)                                         │
│     └─ Schedule delay steps (cron)                                              │
│     ↓                                                                            │
│  6. Cron Job Processing                                                         │
│     ├─ Find ready delays                                                        │
│     ├─ Execute delayed steps                                                    │
│     └─ Mark as processed                                                        │
└─────────────────────────────────────────────────────────────────────────────────┘
