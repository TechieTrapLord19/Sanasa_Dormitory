<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Sanasa Dormitory')); ?> - <?php echo $__env->yieldContent('title'); ?></title>

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
    </style>
</head>
<body>
    <div class="min-vh-100 d-flex">
        <!-- First Column - Background Image or Dark Blue Color -->
        <div class="col-6 d-flex" style="background-color: #022c6e;">
            <div class="d-flex flex-column justify-content-center align-items-center h-100 text-white p-4 w-100">
                <img src="<?php echo e(asset('images/loginimage.png')); ?>" alt="Sanasa Dormitory Logo" class="" style="max-width:200px;">
                <h1 class="mb-3">Welcome to Sanasa</h1>
            </div>
        </div>

        <!-- Second Column - Login/Register Form -->
        <div class="col-6 d-flex align-items-center justify-content-center p-4">
            <div class="w-100" style="max-width: 400px;">
                <?php echo $__env->yieldContent('auth'); ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php /**PATH C:\Users\hmmth\sanasa_dormitory\resources\views/layouts/logreg.blade.php ENDPATH**/ ?>