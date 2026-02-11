@extends('emails.central.layout')

@section('title', __('Password Reset Request'))

@section('content')
    <div style="text-align: left;">
        <h2 style="color: #111827; font-size: 20px; font-weight: 700; margin-bottom: 16px;">
            {{ __('Hello!') }}
        </h2>

        <p style="color: #4b5563; font-size: 16px; margin-bottom: 24px;">
            {{ __('You are receiving this email because we received a password reset request for your account.') }}
        </p>

        <div class="cta-container">
            <a href="{{ $url }}" class="cta-button">
                {{ __('Reset Password') }}
            </a>
        </div>

        <p style="color: #4b5563; font-size: 16px; margin-bottom: 16px;">
            {{ __('This password reset link will expire in 60 minutes.') }}
        </p>

        <p style="color: #4b5563; font-size: 16px; margin-bottom: 24px;">
            {{ __('If you did not request a password reset, no further action is required.') }}
        </p>

        <p style="color: #4b5563; font-size: 16px; margin-bottom: 8px;">
            {{ __('Regards,') }}<br>
            <strong>{{ config('app.name') }}</strong>
        </p>

        <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 30px 0;">

        <p style="color: #6b7280; font-size: 12px;">
            {{ __("If you're having trouble clicking the \"Reset Password\" button, copy and paste the URL below into your web browser:") }}
            <br>
            <a href="{{ $url }}" style="color: #2563eb; word-break: break-all;">{{ $url }}</a>
        </p>
    </div>
@endsection