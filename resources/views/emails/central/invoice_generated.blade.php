@extends('emails.central.layout')

@section('title', 'New Invoice Generated')

@section('styles')
    .hero-banner { background-color: #dbeafe; color: #1e40af; text-align: center; padding: 15px; font-weight: 600;
    font-size: 16px; border-top: 1px solid #bfdbfe; border-bottom: 1px solid #bfdbfe; }
    .greeting { font-size: 20px; font-weight: bold; margin-bottom: 20px; color: #111827; }
    .intro-text { color: #4b5563; margin-bottom: 30px; font-size: 15px; }

    .highlight-box { background-color: #f0f9ff; border-left: 4px solid #3b82f6; padding: 25px; border-radius: 4px;
    margin-bottom: 30px; }
    .highlight-title { color: #1d4ed8; font-weight: bold; font-size: 16px; margin-bottom: 15px; display: flex; align-items:
    center; gap: 8px; }

    .detail-row { display: flex; margin-bottom: 12px; font-size: 14px; align-items: flex-start; }
    .detail-label { width: 140px; color: #6b7280; flex-shrink: 0; }
    .detail-value { font-weight: 600; color: #1f2937; }

    .amount-due { color: #2563eb; font-weight: 700; font-size: 15px; }

    .cta-container { text-align: center; margin-top: 30px; margin-bottom: 20px; }
    .cta-button { background-color: #2563eb; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px;
    font-weight: 600; font-size: 15px; display: inline-block; transition: background-color 0.2s; }
    .cta-button:hover { background-color: #1d4ed8; }

    @media only screen and (max-width: 600px) {
    .detail-row { flex-direction: column; margin-bottom: 15px; }
    .detail-label { width: 100%; margin-bottom: 2px; }
    }
@endsection

@section('hero-banner')
    <div class="hero-banner">
        📄 New Invoice Generated
    </div>
@endsection

@section('content')
    <div class="greeting">Hello {{ $name ?? 'John Smith' }},</div>

    <div class="intro-text">
        A new invoice has been generated for <strong>{{ $company_name ?? 'Acme Corporation' }}</strong>. Please find the
        details
        below.
    </div>

    <!-- Invoice Details Card -->
    <div class="highlight-box">
        <div class="highlight-title">
            🧾 Invoice Details
        </div>
        <div class="detail-row">
            <span class="detail-label">Invoice Number:</span>
            <span class="detail-value">{{ $invoice_number ?? 'INV-2024-001234' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Invoice Date:</span>
            <span class="detail-value">{{ $invoice_date ?? now()->subDays(15)->format('F j, Y') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Due Date:</span>
            <span class="detail-value">{{ $due_date ?? now()->format('F j, Y') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Amount Due:</span>
            <span class="detail-value amount-due">{{ $amount_due ?? '$299.00' }}</span>
        </div>
    </div>

    <!-- CTA -->
    <div class="cta-container">
        <a href="{{ $invoice_url ?? '#' }}" class="cta-button">View Invoice →</a>
    </div>

    <div class="intro-text" style="text-align: center; font-size: 13px; margin-top: 30px;">
        If you have any questions providing payment, please contact us at <a href="mailto:support@getmazal.com"
            style="color: #2563eb; text-decoration: underline;">support@getmazal.com</a>
    </div>
@endsection