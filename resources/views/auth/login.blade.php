@extends('layouts.logreg')

@section('title', 'Login')

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
        margin-bottom: 1.25rem;
    }

    .login-btn {
        width: 100%;
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

    /* Ensure email input matches password input height */
    #email.form-control {
        padding: 0.375rem 0.75rem;
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

    .input-group .form-control.is-invalid:focus {
        border-color: #dc3545;
        box-shadow: none;
    }

    .input-group .form-control.is-invalid ~ .password-toggle {
        border-color: #dc3545;
    }

    .input-group.has-validation .form-control.is-invalid ~ .password-toggle {
        border-color: #dc3545;
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

    /* Remove the default Bootstrap validation icon inside input */
    .form-control.is-invalid {
        background-image: none;
        padding-right: 0.75rem;
    }

    .input-group .form-control.is-invalid {
        background-image: none;
    }

    /* Loading Overlay */
    .loading-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.95);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }

    .loading-overlay.active {
        display: flex;
        opacity: 1;
    }

    .spinner {
        width: 60px;
        height: 60px;
        border: 4px solid #e5e7eb;
        border-top: 4px solid #022c6e;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        opacity: 0;
        transform: scale(0.8);
        transition: opacity 0.3s ease-in-out 0.1s, transform 0.3s ease-in-out 0.1s;
    }

    .loading-overlay.active .spinner {
        opacity: 1;
        transform: scale(1);
    }

    @keyframes spin {
        0% { transform: rotate(0deg) scale(1); }
        100% { transform: rotate(360deg) scale(1); }
    }

    .loading-text {
        margin-top: 1.5rem;
        font-size: 1rem;
        color: #022c6e;
        font-weight: 600;
        opacity: 0;
        transform: translateY(10px);
        transition: opacity 0.3s ease-in-out 0.2s, transform 0.3s ease-in-out 0.2s;
    }

    .loading-overlay.active .loading-text {
        opacity: 1;
        transform: translateY(0);
    }

    /* Disable form while loading */
    .login-card.loading {
        opacity: 0.6;
        pointer-events: none;
        transition: opacity 0.3s ease-in-out;
    }

    /* Throttle / Locked alerts */
    .alert-throttle {
        background: #fff8e1;
        border: 1px solid #f59e0b;
        border-radius: 10px;
        padding: 1.1rem 1.25rem;
        margin-bottom: 1.25rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        text-align: center;
    }
    .alert-throttle .throttle-icon {
        font-size: 2rem;
        color: #d97706;
    }
    .alert-throttle .throttle-title {
        font-weight: 700;
        font-size: 0.95rem;
        color: #92400e;
    }
    .alert-throttle .throttle-msg {
        font-size: 0.83rem;
        color: #78350f;
    }
    .countdown-display {
        font-size: 1.9rem;
        font-weight: 800;
        color: #b45309;
        letter-spacing: 0.05em;
        font-variant-numeric: tabular-nums;
    }

    .alert-locked {
        background: #fef2f2;
        border: 1px solid #ef4444;
        border-radius: 10px;
        padding: 1.1rem 1.25rem;
        margin-bottom: 1.25rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        text-align: center;
    }
    .alert-locked .locked-icon {
        font-size: 2rem;
        color: #dc2626;
    }
    .alert-locked .locked-title {
        font-weight: 700;
        font-size: 0.95rem;
        color: #7f1d1d;
    }
    .alert-locked .locked-msg {
        font-size: 0.83rem;
        color: #991b1b;
    }
    .form-disabled {
        opacity: 0.45;
        pointer-events: none;
    }
</style>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
    <div class="loading-text">Signing in...</div>
</div>

<div class="login-outer">
    <div class="login-card" id="loginCard">
        <h2 class="login-heading fw-bold">Log in</h2>

        {{-- Password reset success --}}
        @if (session('status'))
        <div style="background: #dcfce7; border: 1px solid #86efac; border-radius: 10px; padding: 0.85rem 1rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; font-size: 0.88rem; color: #166534;">
            <i class="bi bi-check-circle-fill" style="font-size: 1.1rem; flex-shrink: 0;"></i>
            <span>{{ session('status') }}</span>
        </div>
        @endif

        <div id="idleLogoutAlert" style="display:none;" class="alert-throttle mb-3">
            <div class="throttle-icon"><i class="bi bi-clock-history"></i></div>
            <div class="throttle-title">Session Expired</div>
            <div class="throttle-msg">You were automatically logged out due to inactivity. Please log in again.</div>
        </div>

        {{-- Account locked permanently --}}
        @if(session('account_locked'))
        <div class="alert-locked">
            <div class="locked-icon"><i class="bi bi-shield-lock-fill"></i></div>
            <div class="locked-title">Account Locked</div>
            <div class="locked-msg">
                Your account has been locked due to too many failed login attempts.<br>
                Please contact the administrator to resolve this issue.
            </div>
        </div>
        @endif

        {{-- Temporary 5-minute cooldown with countdown timer --}}
        @if(session('throttle_seconds'))
        <div class="alert-throttle" id="throttleAlert">
            <div class="throttle-icon"><i class="bi bi-clock-history"></i></div>
            <div class="throttle-title">Too Many Failed Attempts</div>
            <div class="countdown-display" id="countdownDisplay">{{ gmdate('i:s', session('throttle_seconds')) }}</div>
            <div class="throttle-msg">Please wait before trying again. If you believe this is a mistake, contact your administrator.</div>
        </div>
        @endif

        <div id="loginFormWrap" @if(session('throttle_seconds') || session('account_locked')) class="form-disabled" @endif>
        <form action="{{ route('login') }}" method="POST" id="loginForm" novalidate>
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label-visible">Email</label>
                <input id="email" name="email" type="email" class="form-control @if($errors->has('email')) is-invalid @endif"
                    value="{{ old('email') }}" required autocomplete="email" autofocus>
                <div class="invalid-feedback error-message" id="emailFormatError" style="display: none;">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span>Please enter a valid email address (e.g., user@example.com)</span>
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label-visible">Password</label>
                <div class="input-group has-validation">
                    <input id="password" name="password" type="password" class="form-control @if($errors->has('email')) is-invalid @endif"
                        required autocomplete="current-password" aria-describedby="passwordToggle">
                    <button id="passwordToggle" type="button" class="password-toggle" tabindex="-1" aria-pressed="false" aria-label="Show password" title="Show password">
                        <i class="bi bi-eye-fill"></i>
                    </button>
                </div>
            </div>

            @if($errors->has('email'))
            <div class="mb-3 error-message" style="color: #dc3545; font-size: 0.875rem;">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>{{ $errors->first('email') }}</span>
            </div>
            @endif

            <div class="mb-3 d-flex justify-content-center">
                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
            </div>
            @if($errors->has('g-recaptcha-response'))
            <div class="mb-3 error-message" style="color: #dc3545; font-size: 0.875rem;">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>{{ $errors->first('g-recaptcha-response') }}</span>
            </div>
            @endif

            <button type="submit" class="btn login-btn">Log in</button>

            <div class="text-center mt-3">
                <a href="{{ route('password.request') }}" style="color: #022c6e; font-size: 0.88rem; font-weight: 600; text-decoration: none;">
                    Forgot your password?
                </a>
            </div>
        </form>
        </div>{{-- end loginFormWrap --}}
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('passwordToggle');
        const pass = document.getElementById('password');
        const loginForm = document.getElementById('loginForm');
        const loadingOverlay = document.getElementById('loadingOverlay');
        const loginCard = document.getElementById('loginCard');

        // ── Countdown Timer ────────────────────────────────────────────────
        const countdownEl = document.getElementById('countdownDisplay');
        const formWrap    = document.getElementById('loginFormWrap');

        @if(session('throttle_seconds'))
        let remaining = {{ session('throttle_seconds') }};

        function formatTime(secs) {
            const m = String(Math.floor(secs / 60)).padStart(2, '0');
            const s = String(secs % 60).padStart(2, '0');
            return m + ':' + s;
        }

        const countdownInterval = setInterval(function () {
            remaining--;
            if (remaining <= 0) {
                clearInterval(countdownInterval);
                // Re-enable form and hide alert
                if (formWrap) formWrap.classList.remove('form-disabled');
                const throttleAlert = document.getElementById('throttleAlert');
                if (throttleAlert) throttleAlert.style.display = 'none';
                if (countdownEl) countdownEl.textContent = '0:00';
            } else {
                if (countdownEl) countdownEl.textContent = formatTime(remaining);
            }
        }, 1000);
        @endif
        // ──────────────────────────────────────────────────────────────────

        // Show loading overlay on form submit
        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                // Block submission during countdown or account lock
                if (formWrap && formWrap.classList.contains('form-disabled')) {
                    e.preventDefault();
                    return;
                }

                const emailInput = document.getElementById('email');
                const passwordInput = document.getElementById('password');
                let hasError = false;

                // Clear previous empty-field warnings
                document.querySelectorAll('.empty-field-msg').forEach(el => el.remove());

                // Validate email
                if (emailInput.value.trim() === '') {
                    emailInput.classList.add('is-invalid');
                    const msg = document.createElement('div');
                    msg.className = 'empty-field-msg error-message';
                    msg.style.cssText = 'color:#dc3545;font-size:0.875rem;margin-top:0.25rem;';
                    msg.innerHTML = '<i class="bi bi-exclamation-circle-fill"></i> <span>Email is required.</span>';
                    emailInput.parentNode.appendChild(msg);
                    hasError = true;
                }

                // Validate password
                if (passwordInput.value.trim() === '') {
                    passwordInput.classList.add('is-invalid');
                    const msg = document.createElement('div');
                    msg.className = 'empty-field-msg error-message';
                    msg.style.cssText = 'color:#dc3545;font-size:0.875rem;margin-top:0.25rem;';
                    msg.innerHTML = '<i class="bi bi-exclamation-circle-fill"></i> <span>Password is required.</span>';
                    passwordInput.closest('.mb-3').appendChild(msg);
                    hasError = true;
                }

                if (hasError) {
                    e.preventDefault();
                    return;
                }

                // Show loading overlay
                loadingOverlay.classList.add('active');
                loginCard.classList.add('loading');

                // Trigger reflow to ensure transition works
                void loadingOverlay.offsetWidth;
            });
        }

        if (!toggle || !pass) return;

        toggle.addEventListener('click', function () {
            const isPassword = pass.getAttribute('type') === 'password';
            pass.setAttribute('type', isPassword ? 'text' : 'password');
            toggle.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
            toggle.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
            toggle.setAttribute('title', isPassword ? 'Hide password' : 'Show password');
            toggle.innerHTML = isPassword ? '<i class="bi bi-eye-slash-fill"></i>' : '<i class="bi bi-eye-fill"></i>';
        });

        // Remove error styling and messages when user types
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const emailFormatError = document.getElementById('emailFormatError');

        // Email validation function
        function validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function clearValidationError(input) {
            if (input.value.trim() !== '') {
                // Clear red border from BOTH inputs whenever either is typed in
                if (emailInput) emailInput.classList.remove('is-invalid');
                if (passwordInput) passwordInput.classList.remove('is-invalid');
                // Hide the combined error message below password
                const combinedError = document.querySelector('.error-message[style]');
                if (combinedError) combinedError.style.display = 'none';
                // Reset toggle border
                const passwordToggle = document.getElementById('passwordToggle');
                if (passwordToggle) passwordToggle.style.borderColor = '#dee2e6';
            }
        }

        if (emailInput) {
            emailInput.addEventListener('input', function() {
                clearValidationError(this);
                // Hide format error when typing
                if (emailFormatError) {
                    emailFormatError.style.display = 'none';
                }
            });

            // Validate email format on blur (when user leaves the field)
            emailInput.addEventListener('blur', function() {
                const email = this.value.trim();
                if (email !== '' && !validateEmail(email)) {
                    this.classList.add('is-invalid');
                    if (emailFormatError) {
                        emailFormatError.style.display = 'flex';
                    }
                }
            });
        }

        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                clearValidationError(this);
            });
        }
    });

    // Show idle logout notice if redirected after AFK auto-logout
    (function () {
        var alertEl = document.getElementById('idleLogoutAlert');
        if (alertEl && localStorage.getItem('idle_logout') === '1') {
            alertEl.style.display = 'block';
            localStorage.removeItem('idle_logout');
        }
    })();
</script>

@endsection
