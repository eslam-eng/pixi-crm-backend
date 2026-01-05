@extends('emails.central.layout')

@section('title', 'Subscription Renewed')

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

    @media only screen and (max-width: 600px) {
    .detail-row { flex-direction: column; margin-bottom: 15px; }
    .detail-label { width: 100%; margin-bottom: 2px; }
    }
@endsection

@section('hero-banner')
    <div class="hero-banner">
        ✅ Subscription Renewed
    </div>
@endsection

@section('content')
    <div class="greeting">Hello {{ $name ?? 'John Smith' }},</div>

    <div class="intro-text">
        Your <strong>{{ $plan_name ?? 'Professional Plan' }}</strong> subscription for
        <strong>{{ $company_name ?? 'Acme Corporation' }}</strong> has been successfully
        renewed. Thank you for your continued trust in Mazal CRM.
    </div>

    <!-- Renewal Details Card -->
    <div class="highlight-box">
        <div class="highlight-title">
            🔄 Renewal Details
        </div>
        <div class="detail-row">
            <span class="detail-label">Plan:</span>
            <span class="detail-value">{{ $plan_name ?? 'Professional Plan' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Renewal Date:</span>
            <span class="detail-value">{{ $renewal_date ?? now()->format('F j, Y') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Amount Charged:</span>
            <span class="detail-value">{{ $amount ?? '$99.00' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Next Billing:</span>
            <span class="detail-value">{{ $next_billing_date ?? now()->addMonth()->format('F j, Y') }}</span>
        </div>
    </div>

    <div class="intro-text">
        If you have any questions, please contact us at <a href="mailto:support@getmazal.com"
            style="color: #2563eb; text-decoration: underline;">support@getmazal.com</a>
    </div>
@endsection