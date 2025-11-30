# Automation Trigger Field Mapping for Condition Steps

## Overview

This document maps each automation trigger to the fields available for use in **condition steps**. Since each trigger is associated with a specific module (Contact, Lead, Task, Deal), the available fields depend on the triggerable entity.

---

## Field Mapping by Trigger

### 📇 Contact Triggers

#### 1. `contact_created` - Contact Created

**Triggerable Entity:** `Contact`

**Available Fields:**

| Field                 | Type    | Description              | Example Condition                    |
| --------------------- | ------- | ------------------------ | ------------------------------------ |
| `first_name`          | string  | Contact's first name     | `first_name` is not empty            |
| `last_name`           | string  | Contact's last name      | `last_name` equals "Smith"           |
| `email`               | string  | Email address            | `email` is not empty                 |
| `status`              | string  | Contact status           | `status` equals "Active"             |
| `job_title`           | string  | Job title                | `job_title` contains "Manager"       |
| `department`          | string  | Department               | `department` equals "Sales"          |
| `company_name`        | string  | Company name             | `company_name` is not empty          |
| `campaign_name`       | string  | Campaign source          | `campaign_name` equals "Summer 2024" |
| `website`             | string  | Company website          | `website` is not empty               |
| `industry`            | string  | Industry                 | `industry` equals "Technology"       |
| `company_size`        | string  | Company size             | `company_size` equals "50-100"       |
| `address`             | string  | Street address           | `address` is not empty               |
| `state`               | string  | State/Province           | `state` equals "California"          |
| `zip_code`            | string  | ZIP/Postal code          | `zip_code` starts with "90"          |
| `country_id`          | integer | Country ID               | `country_id` equals 1                |
| `city_id`             | integer | City ID                  | `city_id` is not null                |
| `source_id`           | integer | Lead source ID           | `source_id` equals 5                 |
| `user_id`             | integer | Assigned user ID         | `user_id` is not null                |
| `contact_method`      | string  | Preferred contact method | `contact_method` equals "email"      |
| `email_permission`    | boolean | Email permission         | `email_permission` equals true       |
| `phone_permission`    | boolean | Phone permission         | `phone_permission` equals true       |
| `whatsapp_permission` | boolean | WhatsApp permission      | `whatsapp_permission` equals true    |
| `tags`                | array   | Contact tags             | `tags` contains "VIP"                |
| `notes`               | text    | Contact notes            | `notes` is not empty                 |

**Relationship Fields (Nested Access):**

| Field             | Type   | Description        | Example Condition                     |
| ----------------- | ------ | ------------------ | ------------------------------------- |
| `source.name`     | string | Source name        | `source.name` equals "Website"        |
| `user.first_name` | string | Assigned user name | `user.first_name` equals "John"       |
| `city.name`       | string | City name          | `city.name` equals "Los Angeles"      |
| `country.name`    | string | Country name       | `country.name` equals "United States" |
| `phone.phone`     | string | Phone number       | `phone.phone` is not empty            |

---

#### 2. `contact_updated` - Contact Updated

**Triggerable Entity:** `Contact`

**Available Fields:** Same as `contact_created` (all Contact fields)

---

### 💼 Lead/Opportunity Triggers

#### 3. `opportunity_created` - Opportunity Created

**Triggerable Entity:** `Lead` (Opportunity)

**Available Fields:**

| Field                 | Type     | Description         | Example Condition                 |
| --------------------- | -------- | ------------------- | --------------------------------- |
| `status`              | enum     | Opportunity status  | `status` equals "Active"          |
| `deal_value`          | decimal  | Deal value/amount   | `deal_value` > 10000              |
| `win_probability`     | decimal  | Win probability %   | `win_probability` >= 75           |
| `expected_close_date` | date     | Expected close date | `expected_close_date` is not null |
| `assigned_to_id`      | integer  | Assigned user ID    | `assigned_to_id` is not null      |
| `stage_id`            | integer  | Current stage ID    | `stage_id` equals 3               |
| `contact_id`          | integer  | Related contact ID  | `contact_id` is not null          |
| `is_qualifying`       | boolean  | Is qualifying       | `is_qualifying` equals true       |
| `notes`               | text     | Opportunity notes   | `notes` is not empty              |
| `description`         | text     | Description         | `description` contains "urgent"   |
| `assigned_at`         | datetime | Assignment date     | `assigned_at` is not null         |
| `first_action_at`     | datetime | First action date   | `first_action_at` is not null     |
| `avg_action_time`     | integer  | Avg action time     | `avg_action_time` < 24            |

**Relationship Fields (Nested Access):**

| Field                  | Type   | Description   | Example Condition                       |
| ---------------------- | ------ | ------------- | --------------------------------------- |
| `contact.email`        | string | Contact email | `contact.email` is not empty            |
| `contact.first_name`   | string | Contact name  | `contact.first_name` equals "John"      |
| `contact.company_name` | string | Company name  | `contact.company_name` is not empty     |
| `contact.source.name`  | string | Lead source   | `contact.source.name` equals "Referral" |
| `user.first_name`      | string | Assigned user | `user.first_name` equals "Sarah"        |
| `stage.name`           | string | Stage name    | `stage.name` equals "Proposal"          |
| `city.name`            | string | City name     | `city.name` equals "New York"           |

**Possible Status Values:**

- `Active`
- `Won`
- `Lost`
- `Abandoned`

---

#### 4. `opportunity_lead_qualified` - Opportunity Qualified

**Triggerable Entity:** `Lead`

**Available Fields:** Same as `opportunity_created`

---

#### 5. `opportunity_stage_changed` - Stage Changed

**Triggerable Entity:** `Lead`

**Available Fields:** Same as `opportunity_created`

**Common Conditions:**

- Check if moved to specific stage
- Check deal value after stage change
- Check if assigned user changed

---

#### 6. `opportunity_no_action_for_x_time` - No Action for X Time

**Triggerable Entity:** `Lead`

**Available Fields:** Same as `opportunity_created`

**Useful Fields:**

- `first_action_at` - Check when last action occurred
- `avg_action_time` - Check average response time
- `status` - Ensure still Active

---

#### 7. `opportunity_high_value` - High Value Opportunity

**Triggerable Entity:** `Lead`

**Available Fields:** Same as `opportunity_created`

**Common Conditions:**

- `deal_value` > threshold
- `win_probability` >= certain %
- `stage.name` equals specific stage

---

#### 8. `opportunity_won` - Opportunity Won

**Triggerable Entity:** `Lead`

**Available Fields:** Same as `opportunity_created`

**Common Conditions:**

- `deal_value` > threshold (for high-value wins)
- `contact.source.name` equals specific source
- `assigned_to_id` equals specific user

---

#### 9. `opportunity_lost` - Opportunity Lost

**Triggerable Entity:** `Lead`

**Available Fields:** Same as `opportunity_created`

**Additional Fields:**

| Field         | Type   | Description | Example Condition            |
| ------------- | ------ | ----------- | ---------------------------- |
| `reason.name` | string | Loss reason | `reason.name` equals "Price" |

---

### 💰 Deal Triggers

#### 10. `deal_created` - Deal Created

**Triggerable Entity:** `Deal`

**Available Fields:**

| Field                 | Type    | Description     | Example Condition                   |
| --------------------- | ------- | --------------- | ----------------------------------- |
| `deal_name`           | string  | Deal name       | `deal_name` is not empty            |
| `lead_id`             | integer | Related lead ID | `lead_id` is not null               |
| `sale_date`           | date    | Sale date       | `sale_date` is not null             |
| `discount_type`       | string  | Discount type   | `discount_type` equals "percentage" |
| `discount_value`      | decimal | Discount value  | `discount_value` > 0                |
| `tax_rate`            | decimal | Tax rate        | `tax_rate` > 0                      |
| `payment_status`      | string  | Payment status  | `payment_status` equals "Pending"   |
| `payment_method_id`   | integer | Payment method  | `payment_method_id` is not null     |
| `total_amount`        | decimal | Total amount    | `total_amount` > 5000               |
| `partial_amount_paid` | decimal | Amount paid     | `partial_amount_paid` > 0           |
| `amount_due`          | decimal | Amount due      | `amount_due` > 0                    |
| `approval_status`     | string  | Approval status | `approval_status` equals "Approved" |
| `assigned_to_id`      | integer | Assigned user   | `assigned_to_id` is not null        |
| `created_by_id`       | integer | Creator user    | `created_by_id` is not null         |
| `notes`               | text    | Deal notes      | `notes` is not empty                |

**Relationship Fields:**

| Field                       | Type   | Description   | Example Condition                        |
| --------------------------- | ------ | ------------- | ---------------------------------------- |
| `lead.status`               | enum   | Lead status   | `lead.status` equals "Won"               |
| `lead.contact.email`        | string | Contact email | `lead.contact.email` is not empty        |
| `lead.contact.company_name` | string | Company name  | `lead.contact.company_name` is not empty |
| `assigned_to.first_name`    | string | Assigned user | `assigned_to.first_name` equals "John"   |
| `created_by.first_name`     | string | Creator name  | `created_by.first_name` is not empty     |

---

#### 11. `deal_updated` - Deal Updated

**Triggerable Entity:** `Deal`

**Available Fields:** Same as `deal_created`

---

#### 12. `deal_overdue_payment` - Deal Overdue Payment

**Triggerable Entity:** `Deal`

**Available Fields:** Same as `deal_created`

**Common Conditions:**

- `amount_due` > threshold
- `payment_status` equals "Overdue"
- `total_amount` > certain value

---

### ✅ Task Triggers

#### 13. `task_created` - Task Created

**Triggerable Entity:** `Task`

**Available Fields:**

| Field              | Type    | Description      | Example Condition               |
| ------------------ | ------- | ---------------- | ------------------------------- |
| `title`            | string  | Task title       | `title` contains "Follow-up"    |
| `description`      | text    | Task description | `description` is not empty      |
| `status`           | string  | Task status      | `status` equals "Pending"       |
| `priority_id`      | integer | Priority ID      | `priority_id` equals 1          |
| `task_type_id`     | integer | Task type ID     | `task_type_id` equals 2         |
| `due_date`         | date    | Due date         | `due_date` is not null          |
| `due_time`         | time    | Due time         | `due_time` is not null          |
| `assigned_to_id`   | integer | Assigned user    | `assigned_to_id` is not null    |
| `lead_id`          | integer | Related lead     | `lead_id` is not null           |
| `tags`             | array   | Task tags        | `tags` contains "urgent"        |
| `additional_notes` | text    | Additional notes | `additional_notes` is not empty |
| `escalation_sent`  | boolean | Escalation sent  | `escalation_sent` equals false  |

**Relationship Fields:**

| Field                   | Type    | Description   | Example Condition                     |
| ----------------------- | ------- | ------------- | ------------------------------------- |
| `lead.status`           | enum    | Lead status   | `lead.status` equals "Active"         |
| `lead.deal_value`       | decimal | Deal value    | `lead.deal_value` > 10000             |
| `lead.contact.email`    | string  | Contact email | `lead.contact.email` is not empty     |
| `assignedTo.first_name` | string  | Assigned user | `assignedTo.first_name` equals "John" |
| `priority.name`         | string  | Priority name | `priority.name` equals "High"         |
| `taskType.name`         | string  | Task type     | `taskType.name` equals "Call"         |

**Possible Status Values:**

- `Pending`
- `In Progress`
- `Completed`
- `Cancelled`

---

#### 14. `task_completed` - Task Completed

**Triggerable Entity:** `Task`

**Available Fields:** Same as `task_created`

**Common Conditions:**

- `lead.status` equals "Active" (to trigger next action)
- `priority.name` equals "High"
- `task_type_id` equals specific type

---

#### 15. `task_overdue` - Task Overdue

**Triggerable Entity:** `Task`

**Available Fields:** Same as `task_created`

**Common Conditions:**

- `priority_id` equals 1 (High priority)
- `lead.deal_value` > threshold
- `escalation_sent` equals false

---

### 📅 Calendar/Event Triggers

#### 16. `calendar_event_created` - Event Created

**Triggerable Entity:** Varies (likely `CalendarEvent` or related entity)

**Note:** Fields depend on your CalendarEvent model structure.

---

#### 17. `calendar_event_cancelled` - Event Cancelled

**Triggerable Entity:** Varies

---

#### 18. `calendar_attendee_rsvp` - Attendee RSVP

**Triggerable Entity:** Varies

---

### 📝 Form Triggers

#### 19. `form_submitted` - Form Submitted

**Triggerable Entity:** `FormSubmission`

**Available Fields:** Depends on form structure (typically stored in `data` JSON field)

**Common Approach:**

- Access form data using dot notation: `data.email`, `data.phone`, etc.

---

#### 20. `form_field_mapping_error` - Field Mapping Error

**Triggerable Entity:** `FormSubmission`

**Available Fields:** Same as `form_submitted`

---

## Condition Operations Reference

### String Operations

- `equals` - Exact match
- `not_equals` - Not equal
- `contains` - Contains substring
- `not_contains` - Does not contain
- `starts_with` - Starts with
- `ends_with` - Ends with
- `is_empty` - Empty string
- `is_not_empty` - Not empty

### Numeric Operations

- `equals` / `=` - Equal to
- `not_equals` / `!=` - Not equal to
- `greater_than` / `>` - Greater than
- `greater_than_or_equal` / `>=` - Greater than or equal
- `less_than` / `<` - Less than
- `less_than_or_equal` / `<=` - Less than or equal
- `between` - Between two values

### Boolean Operations

- `equals` - true/false
- `is_null` - Is null
- `is_not_null` - Is not null

### Array Operations

- `in` - Value in array
- `not_in` - Value not in array
- `contains` - Array contains value

---

## Example Workflows by Trigger

### Contact Created → Qualification Workflow

**Trigger:** `contact_created`

**Steps:**

1. **Condition**: `email` is not empty
2. **Action**: Send welcome email
3. **Condition**: `source.name` equals "Website"
4. **Action**: Tag as "Web Lead"
5. **Condition**: `company_size` equals "100+"
6. **Action**: Assign to enterprise sales team

---

### Opportunity Created → High-Value Deal Flow

**Trigger:** `opportunity_created`

**Steps:**

1. **Condition**: `deal_value` > 10000
2. **Action**: Notify manager
3. **Condition**: `contact.source.name` equals "Referral"
4. **Action**: Give bonus points
5. **Delay**: Wait 2 days
6. **Condition**: `status` equals "Active"
7. **Action**: Escalate to VP

---

### Task Overdue → Escalation

**Trigger:** `task_overdue`

**Steps:**

1. **Condition**: `priority.name` equals "High"
2. **Action**: Notify manager
3. **Condition**: `lead.deal_value` > 5000
4. **Action**: Escalate to senior sales
5. **Condition**: `escalation_sent` equals false
6. **Action**: Send escalation email

---

### Deal Created → Onboarding

**Trigger:** `deal_created`

**Steps:**

1. **Condition**: `total_amount` > 5000
2. **Action**: Create onboarding task
3. **Condition**: `payment_status` equals "Paid"
4. **Action**: Send welcome package
5. **Condition**: `lead.contact.industry` equals "Technology"
6. **Action**: Assign to tech specialist

---

## Implementation Notes

### Accessing Nested Fields

Use dot notation to access relationship fields:

```php
// Direct field
'field' => 'email'

// Nested relationship
'field' => 'contact.email'

// Deep nesting
'field' => 'lead.contact.source.name'
```

### Field Value Extraction

The `getFieldValue()` method in `AutomationWorkflowExecutorService` handles nested access:

```php
private function getFieldValue(Model $triggerable, string $field): mixed
{
    if (str_contains($field, '.')) {
        $parts = explode('.', $field);
        $value = $triggerable;

        foreach ($parts as $part) {
            if ($value && is_object($value)) {
                $value = $value->{$part} ?? null;
            } else {
                return null;
            }
        }

        return $value;
    }

    return $triggerable->{$field} ?? null;
}
```

---

## Summary

**Total Triggers:** 20

**Trigger Categories:**

- **Contact:** 2 triggers (contact_created, contact_updated)
- **Opportunity/Lead:** 7 triggers (created, qualified, stage_changed, no_action, high_value, won, lost)
- **Deal:** 3 triggers (created, updated, overdue_payment)
- **Task:** 3 triggers (created, completed, overdue)
- **Calendar:** 3 triggers (event_created, event_cancelled, attendee_rsvp)
- **Form:** 2 triggers (form_submitted, field_mapping_error)

**Key Modules:**

- **Contact** - 25+ direct fields + relationship fields
- **Lead** - 13+ direct fields + relationship fields
- **Task** - 12+ direct fields + relationship fields
- **Deal** - 14+ direct fields + relationship fields

This mapping enables powerful conditional workflows based on the specific context of each trigger!
