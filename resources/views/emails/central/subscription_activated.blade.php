@extends('emails.central.layout')

@section('title', 'Subscription Activated')

@section('styles')
    .hero-banner { background-color: #dcfce7; color: #166534; text-align: center; padding: 15px; font-weight: 600;
    font-size: 16px; border-top: 1px solid #bbf7d0; border-bottom: 1px solid #bbf7d0; }
    .greeting { font-size: 20px; font-weight: bold; margin-bottom: 20px; color: #111827; }
    .intro-text { color: #4b5563; margin-bottom: 30px; font-size: 15px; }

    .highlight-box { background-color: #f0fdf4; border-left: 4px solid #22c55e; padding: 25px; border-radius: 4px;
    margin-bottom: 30px; }
    .highlight-title { color: #15803d; font-weight: bold; font-size: 16px; margin-bottom: 15px; display: flex; align-items:
    center; gap: 8px; }

    .detail-row { display: flex; margin-bottom: 12px; font-size: 14px; align-items: flex-start; }
    .detail-label { width: 140px; color: #6b7280; flex-shrink: 0; }
    .detail-value { font-weight: 600; color: #1f2937; }

    /* Green Button Override */
    .cta-button {
    background-color: #22c55e !important;
    }
    .cta-button:hover {
    background-color: #16a34a !important;
    }

    @media only screen and (max-width: 600px) {
    .detail-row { flex-direction: column; margin-bottom: 15px; }
    .detail-label { width: 100%; margin-bottom: 2px; }
    }
@endsection

@section('hero-banner')
    <div class="hero-banner">
        🎉 Subscription Activated!
    </div>
@endsection

@section('content')
    <div class="greeting">Hello {{ $name ?? 'John Smith' }},</div>

    <div class="intro-text">
        Great news! Your <strong>{{ $plan_name ?? 'Professional Plan' }}</strong> subscription for
        <strong>{{ $company_name ?? 'Acme Corporation' }}</strong> is now
        active. You have full access to all features included in your plan.
    </div>

    <!-- Subscription Details Card -->
    <div class="highlight-box">
        <div class="highlight-title">
            📦 Subscription Details
        </div>
        <div class="detail-row">
            <span class="detail-label">Plan:</span>
            <span class="detail-value">{{ $plan_name ?? 'Professional Plan' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Start Date:</span>
            <span class="detail-value">{{ $start_date ?? now()->format('F j, Y') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Billing Cycle:</span>
            <span class="detail-value">{{ $billing_cycle ?? 'Monthly' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Amount:</span>
            <span class="detail-value">{{ $amount ?? '$99/month' }}</span>
        </div>
    </div>

    <!-- CTA -->
    <div class="cta-container">
        <a href="{{ $action_url ?? url('/') }}" class="cta-button">Get Started →</a>
    </div>
@endsection