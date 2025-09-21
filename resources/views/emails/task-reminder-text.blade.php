@if($reminder->time_unit === 'on_time')
TASK DUE NOW
@else
TASK REMINDER
@endif

Hello {{ $userName }}!

@if($reminder->time_unit === 'on_time')
⚠️ Your task is due now!
@else
⏰ Your task is due in {{ $reminder->time_value }} {{ $reminder->time_unit }}.
@endif

TASK DETAILS:
================
Title: {{ $task->title }}

@if($task->description)
Description: {{ $task->description }}
@endif

Due Date: {{ $task->due_date->format('M d, Y') }}
Due Time: {{ $task->due_time }}

@if($task->priority)
Priority: {{ $task->priority->name }}
@endif

@if($task->additional_notes)
Additional Notes: {{ $task->additional_notes }}
@endif

View Task: {{ url('/tasks/' . $task->id) }}

---
This is an automated reminder from your task management system.
If you have any questions, please contact your administrator.