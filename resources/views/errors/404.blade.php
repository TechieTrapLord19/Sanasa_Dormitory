<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Sanasa Dormitory') }} - 404 Not Found</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .error-card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border-radius: 12px;
            padding: 2.5rem 2rem;
            box-shadow: 0 6px 20px rgba(2, 12, 46, 0.08);
            border: 1px solid #eef2ff;
            text-align: center;
        }
        .error-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #fffbeb;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
        }
        .error-icon i {
            font-size: 2.25rem;
            color: #f59e0b;
        }
        .error-code {
            font-size: 3.5rem;
            font-weight: 700;
            color: #f59e0b;
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        .error-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }
        .error-message {
            font-size: 0.9rem;
            color: #6b7280;
            margin-bottom: 1.5rem;
        }
        .btn-back {
            background-color: #022c6e;
            color: #ffffff;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 0.375rem;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            transition: background-color 0.2s;
        }
        .btn-back:hover {
            background-color: #011f4b;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="min-vh-100 d-flex">
        <!-- Left Column -->
        <div class="col-6 d-flex" style="background-color: #022c6e;">
            <div class="d-flex flex-column justify-content-center align-items-center h-100 text-white p-4 w-100">
                <img src="{{ asset('images/loginimage.png') }}" alt="Sanasa Dormitory Logo" style="max-width:200px;">
                <h1 class="mb-3">Welcome to Sanasa</h1>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-6 d-flex align-items-center justify-content-center p-4">
            <div class="error-card">
                <div class="error-icon">
                    <i class="bi bi-search"></i>
                </div>
                <div class="error-code">404</div>
                <div class="error-title">Page Not Found</div>
                <p class="error-message">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
                <a href="{{ url('/dashboard') }}" class="btn-back">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
