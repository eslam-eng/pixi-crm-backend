@extends('emails.central.layout')

@section('title', 'Verify Your Email')

@section('styles')
    .hero-banner { background-color: #dbeafe; color: #1e40af; text-align: center; padding: 15px; font-weight: 600;
    font-size: 16px; border-top: 1px solid #bfdbfe; border-bottom: 1px solid #bfdbfe; }
    .greeting { font-size: 20px; font-weight: bold; margin-bottom: 20px; color: #111827; }
    .intro-text { color: #4b5563; margin-bottom: 30px; font-size: 15px; }

    .code-box { background-color: #f3f4f6; border: 2px dashed #9ca3af; padding: 30px; border-radius: 8px;
    margin-bottom: 30px; text-align: center; }
    .verification-code { font-size: 32px; font-weight: bold; color: #1f2937; letter-spacing: 5px; font-family: monospace; }

    .instruction-text { font-size: 14px; color: #6b7280; margin-top: 10px; }

    /* Blue Button Override */
    .cta-button {
    background-color: #2563eb !important;
    }
    .cta-button:hover {
    background-color: #1d4ed8 !important;
    }
@endsection

@section('hero-banner')
    <div class="hero-banner">
        🛡️ Verify Your Email Address
    </div>
@endsection

@section('content')
    <div class="greeting">Hello,</div>

    <div class="intro-text">
        Thank you for starting your registration with <strong>Mazal CRM</strong>. To complete your sign-up, please use the
        following verification code.
    </div>

    <!-- Code Display -->
    <div class="code-box">
        <div class="verification-code">{{ $code }}</div>
        <div class="instruction-text">This code will expire in 10 minutes.</div>
    </div>

    <div class="intro-text">
        If you did not request this code, please ignore this email.
    </div>
@endsection