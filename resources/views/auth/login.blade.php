@extends('layouts.logreg')

@section('title', 'Login')

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
    <h2 class="text-center mb-3 fw-bold">Sign in!</h2>

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
    <button type="submit" class="button">Sign in</button>
</form>
@endsection
