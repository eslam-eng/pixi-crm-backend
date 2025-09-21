<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Task Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .task-details {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .priority-high {
            border-left: 4px solid #dc3545;
        }
        .priority-medium {
            border-left: 4px solid #ffc107;
        }
        .priority-low {
            border-left: 4px solid #28a745;
        }
        .priority-high .priority-label { color: #dc3545; }
        .priority-medium .priority-label { color: #ffc107; }
        .priority-low .priority-label { color: #28a745; }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 15px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 14px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>
            @if($reminder->time_unit === 'on_time')
                ⏰ Task Due Now
            @else
                🔔 Task Reminder
            @endif
        </h1>
        <p>Hello {{ $userName }}!</p>
    </div>

    @php
        $priority = optional($task->priority);
        $priorityName = strtolower($priority->name ?? 'medium');
        $priorityColor = $priority->color ?? '#6c757d';
    @endphp
    <div class="task-details priority-{{ $priorityName }}">
        <h2>{{ $task->title }}</h2>
        
        @if($task->description)
            <p><strong>Description:</strong> {{ $task->description }}</p>
        @endif

        <div style="margin: 15px 0;">
            <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}</p>
            <p><strong>Due Time:</strong> {{ $task->due_time }}</p>
        </div>

        @if($task->priority)
            <p><strong>Priority:</strong> 
                <span class="priority-label">
                    {{ $priority->name }}
                </span>
            </p>
        @endif

        @if($reminder->time_unit === 'on_time')
            <p style="color: #dc3545; font-weight: bold;">
                ⚠️ This task is due now!
            </p>
        @else
            <p style="color: #ffc107; font-weight: bold;">
                ⏰ This task is due in {{ $reminder->time_value }} {{ $reminder->time_unit }}.
            </p>
        @endif

        @if($task->additional_notes)
            <div style="margin-top: 15px; padding: 10px; background-color: #f8f9fa; border-radius: 4px;">
                <strong>Additional Notes:</strong><br>
                {{ $task->additional_notes }}
            </div>
        @endif

        <a href="{{ url('/tasks/' . $task->id) }}" class="btn">View Task</a>
    </div>

    <div class="footer">
        <p>This is an automated reminder from your task management system.</p>
        <p>If you have any questions, please contact your administrator.</p>
    </div>
</body>
</html>