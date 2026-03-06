@extends('layouts.app')

@section('title', 'Two-Factor Authentication Setup')

@section('content')
<style>
    .tfa-setup-title {
        font-size: 2rem;
        font-weight: 700;
        color: #03255b;
        margin: 0;
    }
    .tfa-card {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        padding: 2rem;
        margin-bottom: 1.5rem;
    }
    .tfa-section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #03255b;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e9ecef;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }
    .tfa-section-title i {
        color: #022c6e;
    }
    .step-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background-color: #022c6e;
        color: white;
        font-size: 0.8rem;
        font-weight: 700;
        flex-shrink: 0;
    }
    .step-row {
        display: flex;
        align-items: flex-start;
        gap: 0.85rem;
        margin-bottom: 1.25rem;
    }
    .step-text {
        font-size: 0.95rem;
        color: #374151;
        padding-top: 0.2rem;
    }
    .qr-wrapper {
        display: flex;
        justify-content: center;
        margin: 1.5rem 0;
    }
    .qr-wrapper img {
        border: 6px solid #f1f5f9;
        border-radius: 12px;
        width: 200px;
        height: 200px;
    }
    .secret-key-box {
        background: #f8fafc;
        border: 1px dashed #94a3b8;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-family: monospace;
        font-size: 1.05rem;
        letter-spacing: 0.15rem;
        text-align: center;
        color: #022c6e;
        font-weight: 600;
        margin-bottom: 1rem;
        user-select: all;
    }
    .otp-input {
        font-size: 1.6rem;
        font-weight: 700;
        letter-spacing: 0.5rem;
        text-align: center;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        padding: 0.6rem 1rem;
        width: 100%;
        color: #022c6e;
    }
    .otp-input:focus {
        outline: none;
        border-color: #022c6e;
        box-shadow: 0 0 0 3px rgba(2, 44, 110, 0.1);
    }
    .btn-enable {
        background-color: #022c6e;
        color: white;
        border: none;
        padding: 0.65rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        width: 100%;
        transition: background-color 0.2s;
    }
    .btn-enable:hover {
        background-color: #011f4b;
        color: white;
    }
    .btn-disable {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 0.65rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        width: 100%;
        transition: background-color 0.2s;
    }
    .btn-disable:hover {
        background-color: #b02a37;
        color: white;
    }
    .status-badge-on {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: #dcfce7;
        color: #15803d;
        font-weight: 700;
        font-size: 0.85rem;
        padding: 0.3rem 0.75rem;
        border-radius: 50px;
    }
    .status-badge-off {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: #f3f4f6;
        color: #6b7280;
        font-weight: 700;
        font-size: 0.85rem;
        padding: 0.3rem 0.75rem;
        border-radius: 50px;
    }
    .app-list {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-top: 0.75rem;
    }
    .app-chip {
        background: #f1f5f9;
        border-radius: 50px;
        padding: 0.35rem 0.9rem;
        font-size: 0.85rem;
        color: #334155;
        font-weight: 500;
    }
</style>

<div class="container-fluid py-4">
    <div class="mb-4">
        <h1 class="tfa-setup-title">
            <i class="bi bi-shield-lock me-2"></i> Two-Factor Authentication
        </h1>
        <p class="text-muted mt-1">Protect your account with an additional layer of security.</p>
    </div>

    {{-- Forced 2FA setup notice --}}
    @if (! $enabled)
    <div class="alert d-flex align-items-start gap-2 mb-4" role="alert"
         style="background-color: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; color: #664d03;">
        <i class="bi bi-exclamation-triangle-fill mt-1" style="font-size: 1.2rem;"></i>
        <div>
            <strong>Two-Factor Authentication is required.</strong><br>
            You must enable 2FA before you can access the system. Please scan the QR code below and enter the verification code to continue.
        </div>
    </div>
    @endif

    {{-- Success message --}}
    @if (session('success'))
    <div class="alert alert-success d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="bi bi-check-circle-fill"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- Current Status --}}
    <div class="tfa-card">
        <div class="tfa-section-title">
            <i class="bi bi-info-circle-fill"></i> Current Status
        </div>
        <div class="d-flex align-items-center gap-3">
            @if ($enabled)
                <span class="status-badge-on">
                    <i class="bi bi-check-circle-fill"></i> 2FA Enabled
                </span>
                <span class="text-muted" style="font-size:0.88rem;">Your account is protected with an authenticator app.</span>
            @else
                <span class="status-badge-off">
                    <i class="bi bi-x-circle-fill"></i> 2FA Disabled
                </span>
                <span class="text-muted" style="font-size:0.88rem;">Your account uses only password authentication.</span>
            @endif
        </div>
    </div>

    @if (! $enabled)
    {{-- Setup section --}}
    <div class="tfa-card">
        <div class="tfa-section-title">
            <i class="bi bi-qr-code-scan"></i> Set Up 2FA
        </div>

        {{-- Step 1 --}}
        <div class="step-row">
            <span class="step-badge">1</span>
            <div class="step-text">
                Install an authenticator app on your phone:
                <div class="app-list">
                    <span class="app-chip"><i class="bi bi-google"></i> Google Authenticator</span>
                    <span class="app-chip">Authy</span>
                    <span class="app-chip">Microsoft Authenticator</span>
                </div>
            </div>
        </div>

        {{-- Step 2 --}}
        <div class="step-row">
            <span class="step-badge">2</span>
            <div class="step-text">
                Scan this QR code with your authenticator app:
            </div>
        </div>
        <div class="qr-wrapper">
            <img src="data:image/svg+xml;base64,{{ $qrCodeSvg }}" alt="2FA QR Code">
        </div>

        {{-- Manual key fallback --}}
        <p class="text-center text-muted mb-1" style="font-size:0.83rem;">Can't scan? Enter this key manually:</p>
        <div class="secret-key-box">{{ $secret }}</div>

        {{-- Step 3 --}}
        <div class="step-row">
            <span class="step-badge">3</span>
            <div class="step-text">Enter the 6-digit code from your app to confirm setup:</div>
        </div>

        <form method="POST" action="{{ route('two-factor.enable') }}">
            @csrf
            <label for="code" class="visually-hidden">Verification Code</label>
            <input
                type="text"
                id="code"
                name="code"
                class="otp-input @error('code') is-invalid @enderror"
                inputmode="numeric"
                pattern="[0-9]{6}"
                maxlength="6"
                autocomplete="one-time-code"
                placeholder="000000"
                autofocus
            >
            @error('code')
                <div class="text-danger mt-2 text-center" style="font-size:0.87rem;">
                    <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                </div>
            @enderror
            <button type="submit" class="btn btn-enable mt-3">
                <i class="bi bi-shield-check me-1"></i> Enable 2FA
            </button>
        </form>
    </div>

    @else
    {{-- Disable section --}}
    <div class="tfa-card">
        <div class="tfa-section-title">
            <i class="bi bi-shield-x"></i> Disable 2FA
        </div>
        <p class="text-muted" style="font-size:0.92rem;">
            Enter the current code from your authenticator app to confirm you want to disable 2FA.
            After disabling, your account will only be protected by password.
        </p>
        <form method="POST" action="{{ route('two-factor.disable') }}">
            @csrf
            @method('DELETE')
            <label for="code" class="visually-hidden">Verification Code</label>
            <input
                type="text"
                id="code"
                name="code"
                class="otp-input @error('code') is-invalid @enderror"
                inputmode="numeric"
                pattern="[0-9]{6}"
                maxlength="6"
                autocomplete="one-time-code"
                placeholder="000000"
            >
            @error('code')
                <div class="text-danger mt-2 text-center" style="font-size:0.87rem;">
                    <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                </div>
            @enderror
            <button type="submit" class="btn btn-disable mt-3">
                <i class="bi bi-shield-x me-1"></i> Disable 2FA
            </button>
        </form>
    </div>
    @endif
</div>
@endsection
