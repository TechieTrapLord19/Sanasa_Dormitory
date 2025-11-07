@extends('layouts.logreg')

@section('title', 'Login')

@section('auth')
<div class="mb-4">
    <h2 class="text-center mb-3 fw-bold">Sign in to your account</h2>
    <p class="text-center text-muted">
        Or
        <a href="{{ route('register') }}" class="text-primary text-decoration-none fw-medium">
            create a new account
        </a>
    </p>
</div>
<form action="{{ route('login') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="email" class="form-label visually-hidden">Email address</label>
        <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" 
               placeholder="Email address" value="{{ old('email') }}" required autocomplete="email">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label for="password" class="form-label visually-hidden">Password</label>
        <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" 
               placeholder="Password" required autocomplete="current-password">
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="remember" name="remember">
        <label class="form-check-label" for="remember">
            Remember me
        </label>
    </div>
    <button type="submit" class="btn btn-primary w-100">Sign in</button>
</form>
@endsection
