<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $emailSettings['subject'] ?? 'Form Submission' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: #007bff;
            color: white;
            padding: 20px;
            border-radius: 5px 5px 0 0;
            margin: -20px -20px 20px -20px;
        }

        .field {
            margin-bottom: 15px;
            padding: 10px;
            border-left: 4px solid #007bff;
            background: #f8f9fa;
        }

        .field-label {
            font-weight: bold;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }

        .field-value {
            color: #666;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>{{ $emailSettings['subject'] ?? 'New Form Submission' }}</h1>
        </div>

        <p><strong>Form:</strong> {{ $submission->form->title }}</p>
        <p><strong>Submitted at:</strong> {{ $submission->created_at->format('M d, Y \a\t h:i A') }}</p>

        @if($submission->ip_address)
        <p><strong>IP Address:</strong> {{ $submission->ip_address }}</p>
        @endif

        <h3>Submission Details:</h3>

        @foreach($submission->data as $key => $value)
        <div class="field">
            <span class="field-label">{{ ucwords(str_replace('_', ' ', $key)) }}:</span>
            <div class="field-value">
                @if(is_array($value))
                {{ implode(', ', $value) }}
                @elseif(is_string($value) && str_starts_with($value, 'storage/'))
                <a href="{{ asset($value) }}">View Attachment</a>
                @else
                {{ $value }}
                @endif
            </div>
        </div>
        @endforeach

        <div class="footer">
            <p>This email was automatically generated from a form submission on {{ config('app.name') }}.</p>
            <p>Submission ID: {{ $submission->id }}</p>
        </div>
    </div>
</body>

</html>