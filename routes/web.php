<?php
Route::get('test-route', function () {
    return 'test route';
});

Route::get('/mailtrap-test', function () {
    $to = request('to', env('MAILTRAP_TEST_TO', env('MAIL_FROM_ADDRESS', 'test@example.com')));

    \Illuminate\Support\Facades\Mail::raw(
        'This is a test email from ' . config('app.name') . ' sent at ' . now()->toDateTimeString(),
        function ($message) use ($to) {
            $message->to($to)->subject('Mailtrap SMTP Test');
        }
    );

    return 'Mail sent to ' . $to;
});
