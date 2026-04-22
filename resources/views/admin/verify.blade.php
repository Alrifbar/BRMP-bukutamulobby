<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Admin - Buku Tamu</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            align-items: center;
            justify-content: center;
        }

        .verify-container {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1), 0 8px 20px rgba(33, 193, 107, 0.1);
            border: 1px solid rgba(33, 193, 107, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .verify-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #21c16b 0%, #0a9b4e 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 36px;
            color: white;
            animation: pulse 2s infinite ease-in-out;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .verify-title {
            font-size: 24px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 12px;
        }

        .verify-subtitle {
            color: #718096;
            font-size: 16px;
            margin-bottom: 32px;
            line-height: 1.5;
        }

        .verify-buttons {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }

        .btn {
            flex: 1;
            padding: 14px 24px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-yes {
            background: linear-gradient(135deg, #21c16b 0%, #0a9b4e 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(33, 193, 107, 0.3);
        }

        .btn-yes:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(33, 193, 107, 0.4);
        }

        .btn-no {
            background: #f7fafc;
            color: #4a5568;
            border: 2px solid #e2e8f0;
        }

        .btn-no:hover {
            background: #edf2f7;
            border-color: #cbd5e0;
        }

        .verify-info {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 16px;
            color: #22543d;
            font-size: 14px;
            line-height: 1.5;
        }

        .verify-info strong {
            color: #16a34a;
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            padding: 16px 24px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(245, 158, 11, 0.3);
            display: none;
            align-items: center;
            gap: 12px;
            z-index: 1000;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .notification.show {
            display: flex;
        }

        .notification-icon {
            font-size: 20px;
        }

        .notification-text {
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="notification" id="exitNotification">
        <div class="notification-icon">🚪</div>
        <div class="notification-text">Anda sekarang keluar dari dashboard admin</div>
    </div>

    <div class="verify-container">
        <div class="verify-icon">🔐</div>
        <h1 class="verify-title">Verifikasi Admin</h1>
        <p class="verify-subtitle">Apakah Anda admin yang sah?</p>
        
        <div class="verify-buttons">
            <button class="btn btn-yes" onclick="proceedAsAdmin()">Yes</button>
            <button class="btn btn-no" onclick="goBack()">No</button>
        </div>

        <div class="verify-info">
            <strong>Penting:</strong> Jika Anda adalah admin, silakan masukkan password login untuk melanjutkan ke dashboard.
        </div>
    </div>

    <script>
        // Tampilkan notifikasi keluar dari dashboard jika ada parameter exit
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('exit') === 'true') {
                showExitNotification();
            }
        });

        function showExitNotification() {
            const notification = document.getElementById('exitNotification');
            notification.classList.add('show');
            
            // Sembunyikan notifikasi setelah 5 detik
            setTimeout(() => {
                notification.classList.remove('show');
            }, 5000);
        }

        function proceedAsAdmin() {
            // Redirect ke halaman login
            window.location.href = '{{ route("admin.login") }}';
        }

        function goBack() {
            // Redirect ke halaman utama atau halaman sebelumnya
            window.location.href = '{{ url("/") }}';
        }
    </script>
</body>
</html>
