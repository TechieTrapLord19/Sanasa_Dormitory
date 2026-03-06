@extends('layouts.logreg')

@section('title', 'Reset Password')

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
    .subtitle {
        text-align: center;
        font-size: 0.88rem;
        color: #6b7280;
        margin-bottom: 1.5rem;
    }
    .password-toggle {
        background: transparent;
        border: none;
        color: #6b7280;
        cursor: pointer;
        font-size: 1.1rem;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0 0.375rem 0.375rem 0;
        padding: 0 0.75rem;
    }
    .password-toggle:hover {
        color: #022c6e;
    }
    .input-group {
        position: relative;
        display: flex;
    }
    .input-group .form-control {
        border-right: none;
        border-radius: 0.375rem 0 0 0.375rem !important;
        flex: 1;
    }
    .input-group .password-toggle {
        border: 1px solid #dee2e6;
        border-left: none;
        border-radius: 0 0.375rem 0.375rem 0;
        align-self: stretch;
    }
    .input-group .form-control:focus {
        border-color: #86b7fe;
        box-shadow: none;
        z-index: 3;
    }
    .input-group .form-control:focus ~ .password-toggle {
        border-color: #86b7fe;
    }
    .input-group:focus-within .password-toggle {
        border-color: #86b7fe;
    }
    .input-group .form-control.is-invalid {
        border-right: none;
        border-color: #dc3545;
    }
    .input-group .form-control.is-invalid ~ .password-toggle {
        border-color: #dc3545;
    }
    .form-control.is-invalid {
        background-image: none;
        padding-right: 0.75rem;
    }

    /* Real-time password checklist */
    .pw-checklist {
        list-style: none;
        padding: 0;
        margin: 0.5rem 0 0 0;
        font-size: 0.82rem;
    }
    .pw-checklist li {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.15rem 0;
        color: #9ca3af;
    }
    .pw-checklist li::before {
        content: '\f287';
        font-family: 'bootstrap-icons';
        font-size: 0.7rem;
    }
    .pw-checklist li.met {
        color: #16a34a;
    }
    .pw-checklist li.met::before {
        content: '\f26b';
    }
</style>

<div class="login-outer">
    <div class="login-card">
        <h2 class="login-heading fw-bold">Reset Password</h2>
        <p class="subtitle">Choose a new password for your account.</p>

        <form method="POST" action="{{ route('password.update') }}" novalidate>
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
                <label for="email" class="form-label-visible">Email</label>
                <input id="email" name="email" type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $email) }}" required autocomplete="email" readonly
                       style="background-color: #f8fafc;">
                @error('email')
                <div class="error-message" style="color: #dc3545;">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span>{{ $message }}</span>
                </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label-visible">New Password</label>
                <div class="input-group has-validation">
                    <input id="password" name="password" type="password"
                           class="form-control @error('password') is-invalid @enderror"
                           required autocomplete="new-password" autofocus>
                    <button type="button" class="password-toggle" tabindex="-1"
                            aria-pressed="false" aria-label="Show password" data-target="password">
                        <i class="bi bi-eye-fill"></i>
                    </button>
                </div>
                @error('password')
                <div class="error-message" style="color: #dc3545;">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span>{{ $message }}</span>
                </div>
                @enderror
                <ul class="pw-checklist">
                    <li id="pw-length">At least 12 characters</li>
                    <li id="pw-upper">At least one uppercase letter</li>
                    <li id="pw-lower">At least one lowercase letter</li>
                    <li id="pw-symbol">At least one symbol</li>
                </ul>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label-visible">Confirm New Password</label>
                <div class="input-group">
                    <input id="password_confirmation" name="password_confirmation" type="password"
                           class="form-control" required autocomplete="new-password">
                    <button type="button" class="password-toggle" tabindex="-1"
                            aria-pressed="false" aria-label="Show password" data-target="password_confirmation">
                        <i class="bi bi-eye-fill"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn login-btn">
                <i class="bi bi-shield-check me-1"></i> Reset Password
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Password visibility toggles
    document.querySelectorAll('.password-toggle').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var targetId = this.getAttribute('data-target');
            var input = document.getElementById(targetId);
            var icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
                this.setAttribute('aria-pressed', 'true');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
                this.setAttribute('aria-pressed', 'false');
            }
        });
    });

    // Real-time password checklist
    var pwInput = document.getElementById('password');
    if (pwInput) {
        pwInput.addEventListener('input', function () {
            var v = this.value;
            toggle('pw-length', v.length >= 12);
            toggle('pw-upper', /[A-Z]/.test(v));
            toggle('pw-lower', /[a-z]/.test(v));
            toggle('pw-symbol', /[^A-Za-z0-9]/.test(v));
        });
    }
    function toggle(id, met) {
        var el = document.getElementById(id);
        if (el) { met ? el.classList.add('met') : el.classList.remove('met'); }
    }
});
</script>
@endsection
