<?php

use App\Http\Controllers\Web\FacebookController;
use Illuminate\Support\Facades\Mail;

Route::get('test-route', function () {
    return 'test route';
});

Route::get('/test-email', function () {
    Mail::raw('This is a test email from Laravel + SendGrid!', function ($message) {
        $message->to('mohamedengnasser20@gmail.com')
            ->subject('SendGrid Test');
    });

    return 'Email sent!';
});

Route::get('/', function () {
    return '<h1>Hello on pixi CRM</h1>';
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

// Facebook routes moved to tenant routes

