<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Hệ thống Quản trị</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .auth-card { width: 400px; border: none; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .card-header { background-color: #343a40; color: white; border-radius: 10px 10px 0 0 !important; text-align: center; padding: 20px; }
        .btn-admin { background-color: #343a40; color: white; border: none; }
        .btn-admin:hover { background-color: #23272b; color: white; }
    </style>
</head>
<body>
    <div class="auth-card card">
        <div class="card-header">
            <h4 class="mb-0">ADMIN PANEL</h4>
        </div>
        <div class="card-body p-4">
            @yield('content')
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>