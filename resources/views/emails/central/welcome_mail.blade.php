@extends('emails.central.layout')

@section('title', 'Welcome to Mazal CRM')

@section('styles')
    .hero-banner { background-color: #e0e7ff; color: #1e40af; text-align: center; padding: 15px; font-weight: 600;
    font-size: 16px; border-top: 1px solid #dbeafe; border-bottom: 1px solid #dbeafe; }
    .greeting { font-size: 20px; font-weight: bold; margin-bottom: 20px; color: #111827; }
    .intro-text { color: #4b5563; margin-bottom: 30px; font-size: 15px; }

    .highlight-box { background-color: #eff6ff; border-left: 4px solid #2563eb; padding: 25px; border-radius: 4px;
    margin-bottom: 30px; }
    .highlight-title { color: #1e40af; font-weight: bold; font-size: 16px; margin-bottom: 15px; display: flex; align-items:
    center; gap: 8px; }

    .detail-row { display: flex; margin-bottom: 12px; font-size: 14px; align-items: flex-start; }
    .detail-label { width: 140px; color: #6b7280; flex-shrink: 0; }
    .detail-value { font-weight: 600; color: #1f2937; }

    .steps-section { margin-bottom: 40px; }
    .steps-title { font-weight: bold; font-size: 16px; margin-bottom: 20px; color: #111827; display: flex; align-items:
    center; gap: 8px; }
    .step-item { display: flex; align-items: baseline; margin-bottom: 18px; color: #4b5563; font-size: 15px; }
    .step-number { background-color: #2563eb; color: white; width: 26px; height: 26px; border-radius: 50%; display: flex;
    align-items: center; justify-content: center; font-size: 13px; font-weight: bold; margin-right: 15px; flex-shrink: 0; }

    @media only screen and (max-width: 600px) {
    .detail-row { flex-direction: column; margin-bottom: 15px; }
    .detail-label { width: 100%; margin-bottom: 2px; }
    }
@endsection

@section('hero-banner')
    <div class="hero-banner">
        🎉 Welcome Aboard!
    </div>
@endsection

@section('content')
    <div class="greeting">Hi {{ $name ?? 'John Smith' }} 👋</div>

    <div class="intro-text">
        Welcome to <strong>Mazal CRM</strong>! We're thrilled to have
        <strong>{{ $company_name ?? 'Acme Corporation' }}</strong> join our community of successful businesses. Your account
        has been set up and is ready to use.
    </div>

    <!-- Account Details Card -->
    <div class="highlight-box">
        <div class="highlight-title">
            📋 Your Account Details
        </div>
        <div class="detail-row">
            <span class="detail-label">Company:</span>
            <span class="detail-value">{{ $company_name ?? 'Acme Corporation' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Plan:</span>
            <span class="detail-value">{{ $plan_name ?? 'Professional Plan' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Team Members:</span>
            <span class="detail-value">{{ $team_size ?? 'Up to 25 users' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Storage:</span>
            <span class="detail-value">{{ $storage ?? '50 GB' }}</span>
        </div>
    </div>

    <!-- CTA -->
    <div class="cta-container">
        <a href="{{ $url ?? url('/') }}" class="cta-button">Get Started Now →</a>
    </div>

    <!-- Quick Start Steps -->
    <div class="steps-section">
        <div class="steps-title">
            🚀 Quick Start Guide
        </div>
        <div class="step-item">
            <div class="step-number">1</div>
            <div><strong>Log in</strong> to your dashboard using the credentials sent separately</div>
        </div>
        <div class="step-item">
            <div class="step-number">2</div>
            <div><strong>Invite your team</strong> members to collaborate on your account</div>
        </div>
        <div class="step-item">
            <div class="step-number">3</div>
            <div><strong>Import your data</strong> or start adding contacts and opportunities</div>
        </div>
        <div class="step-item">
            <div class="step-number">4</div>
            <div><strong>Customize</strong> your pipelines, workflows, and settings</div>
        </div>
    </div>
@endsection