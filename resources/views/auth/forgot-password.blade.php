@extends('layouts.logreg')

@section('title', 'Forgot Password')

@section('auth')
<style>
    .login-outer {
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .login-card {
        width: 100%;
        max-width: 420px;
        background: #ffffff;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 6px 20px rgba(2, 12, 46, 0.08);
        border: 1px solid #eef2ff;
    }
    .login-heading {
        text-align: center;
        margin-bottom: 0.5rem;
    }
    .form-label-visible {
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
        color: #0f172a;
    }
    .btn.login-btn {
        background-color: #022c6e;
        width: 100%;
        color: #ffffff;
    }
    .btn.login-btn:hover {
        background-color: #011f4b;
    }
    .error-message {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.875rem;
        margin-top: 0.375rem;
    }
    .error-message i {
        font-size: 1rem;
    }
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        color: #022c6e;
        font-size: 0.88rem;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.2s;
    }
    .back-link:hover {
        color: #011f4b;
        text-decoration: underline;
    }
    .subtitle {
        text-align: center;
        font-size: 0.88rem;
        color: #6b7280;
        margin-bottom: 1.5rem;
    }
    .status-success {
        background: #dcfce7;
        border: 1px solid #86efac;
        border-radius: 10px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-size: 0.88rem;
        color: #166534;
    }
    .status-success i {
        font-size: 1.15rem;
        flex-shrink: 0;
    }
</style>

<div class="login-outer">
    <div class="login-card">
        <h2 class="login-heading fw-bold">Forgot Password</h2>
        <p class="subtitle">Enter your email address and we'll send you a link to reset your password.</p>

        {{-- Success status --}}
        @if (session('status'))
        <div class="status-success">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('status') }}</span>
        </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" novalidate>
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label-visible">Email</label>
                <input id="email" name="email" type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                <div class="error-message" style="color: #dc3545;">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span>{{ $message }}</span>
                </div>
                @enderror
            </div>

            <button type="submit" class="btn login-btn mb-3">
                <i class="bi bi-envelope me-1"></i> Send Reset Link
            </button>

            <div class="text-center">
                <a href="{{ route('login') }}" class="back-link">
                    <i class="bi bi-arrow-left"></i> Back to Log in
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
