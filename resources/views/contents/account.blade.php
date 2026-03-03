@extends('layouts.app')

@section('title', 'My Account')

@section('content')
<style>
    .account-header {
        background-color: white;
        margin-bottom: 2rem;
    }
    .account-title {
        font-size: 2rem;
        font-weight: 700;
        color: #03255b;
        margin: 0;
    }
    .account-card {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        padding: 2rem;
        margin-bottom: 1.5rem;
    }
    .account-section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #03255b;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e9ecef;
    }
    .account-section-icon {
        width: 36px;
        height: 36px;
        background-color: #f8f9fa;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        color: #03255b;
    }
    .profile-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background-color: #03255b;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 700;
    }
    .profile-info-label {
        font-weight: 600;
        color: #495057;
        font-size: 0.85rem;
        margin-bottom: 0.25rem;
    }
    .profile-info-value {
        color: #212529;
        font-size: 1rem;
    }
    .form-label-custom {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    .password-requirements {
        font-size: 0.8rem;
        margin-top: 0.5rem;
        list-style: none;
        padding-left: 0;
    }
    .password-requirements li {
        margin-bottom: 0.35rem;
        color: #6c757d;
        transition: color 0.2s;
    }
    .password-requirements li .bi {
        margin-right: 0.4rem;
        font-size: 0.75rem;
    }
    .password-requirements li.met {
        color: #198754;
    }
    .password-requirements li.met .bi::before {
        content: "\f26b"; /* bi-check-circle-fill */
    }
    .password-requirements li:not(.met) .bi::before {
        content: "\f22b"; /* bi-circle */
    }
</style>

<div class="account-header">
    <h1 class="account-title">
        <i class="bi bi-person-circle me-2"></i>My Account
    </h1>
    <p class="text-muted mt-1 mb-0">View your profile and manage your password</p>
</div>

{{-- Success/Error Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Profile Information --}}
<div class="account-card">
    <div class="d-flex align-items-center mb-3">
        <div class="account-section-icon">
            <i class="bi bi-person-fill"></i>
        </div>
        <h2 class="account-section-title mb-0" style="border-bottom: none; padding-bottom: 0;">Profile Information</h2>
    </div>
    <hr class="mt-0 mb-4">

    <div class="d-flex align-items-start gap-4">
        <div class="profile-avatar">
            {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
        </div>
        <div class="flex-grow-1">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="profile-info-label">Full Name</div>
                    <div class="profile-info-value">{{ $user->full_name }}</div>
                </div>
                <div class="col-md-6">
                    <div class="profile-info-label">Email Address</div>
                    <div class="profile-info-value">{{ $user->email }}</div>
                </div>
                <div class="col-md-6">
                    <div class="profile-info-label">Role</div>
                    <div class="profile-info-value">
                        <span class="badge {{ strtolower($user->role) === 'owner' ? 'bg-primary' : 'bg-info' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="profile-info-label">Two-Factor Authentication</div>
                    <div class="profile-info-value">
                        @if($user->two_factor_enabled)
                            <span class="badge bg-success"><i class="bi bi-shield-check me-1"></i>Enabled</span>
                        @else
                            <span class="badge bg-warning text-dark"><i class="bi bi-shield-exclamation me-1"></i>Disabled</span>
                            <a href="{{ route('two-factor.setup') }}" class="ms-2 small">Enable now</a>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="profile-info-label">Account Status</div>
                    <div class="profile-info-value">
                        <span class="badge {{ strtolower($user->status) === 'active' ? 'bg-success' : 'bg-secondary' }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="profile-info-label">Hired Since</div>
                    <div class="profile-info-value">{{ $user->created_at->format('F j, Y') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Change Password --}}
<div class="account-card">
    <div class="d-flex align-items-center mb-3">
        <div class="account-section-icon">
            <i class="bi bi-key-fill"></i>
        </div>
        <h2 class="account-section-title mb-0" style="border-bottom: none; padding-bottom: 0;">Change Password</h2>
    </div>
    <hr class="mt-0 mb-4">

    <form method="POST" action="{{ route('account.update-password') }}">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-md-6">
                <label for="current_password" class="form-label form-label-custom">Current Password</label>
                <div class="input-group">
                    <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                           id="current_password" name="current_password" required>
                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                @error('current_password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-md-6">
                <label for="password" class="form-label form-label-custom">New Password</label>
                <div class="input-group">
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                           id="password" name="password" required>
                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
                <ul class="password-requirements mt-2" id="pw-requirements">
                    <li id="pw-length"><i class="bi"></i>At least 12 characters</li>
                    <li id="pw-upper"><i class="bi"></i>Must include an uppercase letter</li>
                    <li id="pw-lower"><i class="bi"></i>Must include a lowercase letter</li>
                    <li id="pw-symbol"><i class="bi"></i>Must include at least one symbol</li>
                </ul>
            </div>
            <div class="col-md-6">
                <label for="password_confirmation" class="form-label form-label-custom">Confirm New Password</label>
                <div class="input-group">
                    <input type="password" class="form-control"
                           id="password_confirmation" name="password_confirmation" required>
                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password_confirmation">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Update Password
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var targetId = this.getAttribute('data-target');
            var input = document.getElementById(targetId);
            var icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });
    });

    // Real-time password requirement checks
    var pwInput = document.getElementById('password');
    if (pwInput) {
        pwInput.addEventListener('input', function() {
            var val = this.value;
            toggle('pw-length', val.length >= 12);
            toggle('pw-upper', /[A-Z]/.test(val));
            toggle('pw-lower', /[a-z]/.test(val));
            toggle('pw-symbol', /[^A-Za-z0-9]/.test(val));
        });
    }

    function toggle(id, met) {
        var el = document.getElementById(id);
        if (el) {
            if (met) { el.classList.add('met'); } else { el.classList.remove('met'); }
        }
    }
});
</script>
@endsection
