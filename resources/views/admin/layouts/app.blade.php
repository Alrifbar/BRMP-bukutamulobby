<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Buku Tamu')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.className = 'theme-' + savedTheme;
        })();
    </script>
    @include('admin.theme-styles')
    @yield('styles')
    <style>
        .success-message {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            color: #166534;
            padding: 20px 24px;
            border-radius: 16px;
            margin-bottom: 24px;
            border: 1px solid #bbf7d0;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            animation: fadeInUp 0.5s ease-out;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="layout">
        @include('admin.partials.sidebar')

        <main class="main">
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
