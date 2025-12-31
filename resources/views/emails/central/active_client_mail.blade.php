@extends('emails.central.layout')

@section('title', 'Account Activation - Mazal CRM')
@section('sub-header', 'Account Activation Notice')

@section('styles')
    .hero-banner { background-color: #dcfce7; color: #166534; text-align: center; padding: 15px; font-weight: 600;
    font-size: 16px; border-top: 1px solid #bbf7d0; border-bottom: 1px solid #bbf7d0; }
    .greeting { font-size: 20px; font-weight: bold; margin-bottom: 20px; color: #111827; }
    .intro-text { color: #4b5563; margin-bottom: 30px; font-size: 15px; }

    .status-box { background-color: #f0fdf4; border-left: 4px solid #22c55e; padding: 25px; border-radius: 4px;
    margin-bottom: 30px; }
    .status-title { color: #15803d; font-weight: bold; font-size: 16px; margin-bottom: 10px; display: flex; align-items:
    center; gap: 8px; }
    .status-text { color: #4b5563; font-size: 14px; }

    .docs-section { text-align: center; margin-top: 40px; }
    .docs-link { display: inline-block; margin: 5px 0; color: #2563eb; text-decoration: none; font-size: 14px; }
    .docs-link:first-child { margin-bottom: 8px; font-weight: 500; display: block; color: #4b5563; text-decoration: none;
    cursor: default; }
    .docs-link:last-child:hover { text-decoration: underline; }

    .thank-you-note { margin-top: 30px; font-size: 14px; color: #6b7280; line-height: 1.5; }
    .thank-you-note a { color: #2563eb; text-decoration: none; }
@endsection

@section('hero-banner')
    <div class="hero-banner">
        ✓ Account Activated Successfully
    </div>
@endsection

@section('content')
    <div class="greeting">Hello {{ $name ?? 'John Smith' }},</div>

    <div class="intro-text">
        Great news! Your <strong>{{ $company_name ?? 'Acme Corporation' }}</strong> account on Mazal CRM has been activated.
        You now have full access to all features included in your subscription.
    </div>

    <!-- Status Box -->
    <div class="status-box">
        <div class="status-title">
            ✓ Account Status: Active
        </div>
        <div class="status-text">
            All services have been restored and your team can continue working without interruption.
        </div>
    </div>

    <!-- CTA -->
    <div class="cta-container">
        <a href="{{ $url ?? url('/') }}" class="cta-button">Access Your Account →</a>
    </div>

    <!-- Documentation - Overriding default help section -->
    @section('help-section')
        <div class="help-box" style="background-color: #eff6ff; border: none; border-left: 4px solid #3b82f6;">
            <div
                style="display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 8px; color: #1e40af; font-weight: 500;">
                📚 Explore our documentation
            </div>
            <a href="#" class="help-link">Visit Documentation Center →</a>
        </div>

        <div class="thank-you-note">
            Thank you for being a valued customer. If you have any questions, please don't hesitate to contact our support team
            at <a href="mailto:support@getmazal.com">support@getmazal.com</a>
        </div>
    @endsection

@endsection