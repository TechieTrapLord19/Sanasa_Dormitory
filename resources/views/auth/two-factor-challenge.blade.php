@extends('layouts.logreg')

@section('title', '2FA Verification')

@section('auth')
<style>
    .tfa-outer {
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .tfa-card {
        width: 100%;
        max-width: 420px;
        background: #ffffff;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 6px 20px rgba(2, 12, 46, 0.08);
        border: 1px solid #eef2ff;
        text-align: center;
    }

    .tfa-icon {
        font-size: 3rem;
        color: #022c6e;
        margin-bottom: 1rem;
    }

    .tfa-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.4rem;
    }

    .tfa-subtitle {
        font-size: 0.9rem;
        color: #64748b;
        margin-bottom: 1.5rem;
    }

    .otp-input {
        font-size: 2rem;
        font-weight: 700;
        letter-spacing: 0.6rem;
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

    .btn-verify {
        background-color: #022c6e;
        color: #ffffff;
        width: 100%;
        padding: 0.65rem;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        margin-top: 1rem;
        transition: background-color 0.2s;
    }

    .btn-verify:hover {
        background-color: #011f4b;
        color: #ffffff;
    }

    .back-link {
        display: block;
        margin-top: 1.25rem;
        font-size: 0.85rem;
        color: #64748b;
        text-decoration: none;
    }

    .back-link:hover {
        color: #022c6e;
        text-decoration: underline;
    }

    .error-box {
        background: #fef2f2;
        border: 1px solid #ef4444;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 0.87rem;
        color: #991b1b;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>

<div class="tfa-outer">
    <div class="tfa-card">
        <div class="tfa-icon">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        <div class="tfa-title">Two-Factor Authentication</div>
        <div class="tfa-subtitle">
            Open your authenticator app and enter the 6-digit code shown for this account.
        </div>

        @if ($errors->any())
        <div class="error-box">
            <i class="bi bi-exclamation-circle-fill"></i>
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('two-factor.verify') }}">
            @csrf
            <label for="code" class="visually-hidden">Authentication Code</label>
            <input
                type="text"
                id="code"
                name="code"
                class="otp-input @error('code') is-invalid @enderror"
                inputmode="numeric"
                pattern="[0-9]{6}"
                maxlength="6"
                autocomplete="one-time-code"
                autofocus
                placeholder="000000"
            >
            <button type="submit" class="btn btn-verify">
                Verify
            </button>
        </form>

        <a href="{{ route('login') }}" class="back-link">
            <i class="bi bi-arrow-left"></i> Back to login
        </a>
    </div>
</div>
@endsection
