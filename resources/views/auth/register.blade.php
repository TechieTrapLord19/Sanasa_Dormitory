@extends('layouts.logreg')

@section('title', 'Register')

@section('auth')
<style>
    .button {
        width: 100%;
        padding: 10px;
        background-color: #022c6e;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
    }

    .button:hover {
        background-color: #04478f;
    }
</style>
<div class="mb-4">
    <h2 class="text-center mb-3 fw-bold">Create your account</h2>

</div>
<form action="{{ route('register') }}" method="POST">
    @csrf
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="first_name" class="form-label">First Name</label>
            <input id="first_name" name="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror"
                   placeholder="First Name" value="{{ old('first_name') }}" required>
            @error('first_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="middle_name" class="form-label">Middle Name</label>
            <input id="middle_name" name="middle_name" type="text" class="form-control @error('middle_name') is-invalid @enderror"
                   placeholder="Middle Name (Optional)" value="{{ old('middle_name') }}">
            @error('middle_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="mb-3">
        <label for="last_name" class="form-label">Last Name</label>
        <input id="last_name" name="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror"
               placeholder="Last Name" value="{{ old('last_name') }}" required>
        @error('last_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror"
               placeholder="Email address" value="{{ old('email') }}" required autocomplete="email">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label for="role" class="form-label">Role</label>
        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
            <option value="">Select a role</option>
            <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>Owner</option>
            <option value="caretaker" {{ old('role') == 'caretaker' ? 'selected' : '' }}>Caretaker</option>
        </select>
        @error('role')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror"
               placeholder="Password" required autocomplete="new-password">
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control"
               placeholder="Confirm Password" required autocomplete="new-password">
    </div>
    <button type="submit" class="button">Create account</button>
</form>
@endsection
