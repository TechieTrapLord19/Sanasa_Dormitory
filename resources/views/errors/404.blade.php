<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Page Not Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; font-family: 'Inter', sans-serif; }
        .error-card { text-align: center; max-width: 480px; padding: 3rem 2rem; }
        .error-code { font-size: 6rem; font-weight: 700; color: #ffc107; line-height: 1; }
        .error-icon { font-size: 4rem; color: #ffc107; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-icon"><i class="bi bi-search"></i></div>
        <div class="error-code">404</div>
        <h2 class="mt-2 mb-3">Page Not Found</h2>
        <p class="text-muted mb-4">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
        <a href="{{ url('/dashboard') }}" class="btn btn-primary px-4">
            <i class="bi bi-house-door me-1"></i> Back to Dashboard
        </a>
    </div>
</body>
</html>
