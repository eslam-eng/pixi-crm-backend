{{ $emailSettings['subject'] ?? 'New Form Submission' }}

Form: {{ $submission->form->title }}
Submitted at: {{ $submission->created_at->format('M d, Y \a\t h:i A') }}
@if($submission->ip_address)
IP Address: {{ $submission->ip_address }}
@endif

Submission Details:
@foreach($submission->data as $key => $value)
{{ ucwords(str_replace('_', ' ', $key)) }}: @if(is_array($value)){{ implode(', ', $value) }}@else{{ $value }}@endif

@endforeach

---
This email was automatically generated from a form submission.
Submission ID: {{ $submission->id }}