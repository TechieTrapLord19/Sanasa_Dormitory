<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sanasa Dormitory') }} - @yield('title')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body>
    <div class="min-vh-100 d-flex">
        <!-- First Column - Background Image or Dark Blue Color -->
        <div class="col-6 d-flex" style="background-color: #022c6e;">
            <div class="d-flex flex-column justify-content-center align-items-center h-100 text-white p-4 w-100">
                <img src="{{ asset('images/loginimage.png') }}" alt="Sanasa Dormitory Logo" class="" style="max-width:200px;">
                <h1 class="mb-3">Welcome to Sanasa</h1>
            </div>
        </div>

        <!-- Second Column - Login/Register Form -->
        <div class="col-6 d-flex align-items-center justify-content-center p-4">
            <div class="w-100" style="max-width: 400px;">
                @yield('auth')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

