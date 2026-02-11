@extends('emails.central.layout')

@section('title', __('Setup Your Password'))

@section('content')
    <div style="text-align: left;">
        <h2 style="color: #111827; font-size: 20px; font-weight: 700; margin-bottom: 16px;">
            {{ __('Hello!') }}
        </h2>

        <p style="color: #4b5563; font-size: 16px; margin-bottom: 24px;">
            {{ __('Welcome to Mazal CRM! You have been registered. Please click the button below to set up your password and access your account.') }}
        </p>

        <div class="cta-container">
            <a href="{{ $url }}" class="cta-button">
                {{ __('Setup Password') }}
            </a>
        </div>

        <p style="color: #4b5563; font-size: 16px; margin-bottom: 24px;">
            {{ __('If you did not create an account, no further action is required.') }}
        </p>

        <p style="color: #4b5563; font-size: 16px; margin-bottom: 8px;">
            {{ __('Regards,') }}<br>
            <strong>{{ config('app.name') }}</strong>
        </p>

        <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 30px 0;">

        <p style="color: #6b7280; font-size: 12px;">
            {{ __("If you're having trouble clicking the \"Setup Password\" button, copy and paste the URL below into your web browser:") }}
            <br>
            <a href="{{ $url }}" style="color: #2563eb; word-break: break-all;">{{ $url }}</a>
        </p>
    </div>
@endsection