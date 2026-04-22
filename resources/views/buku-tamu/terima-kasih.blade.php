<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Terima Kasih - {{ \App\Models\Setting::getValue('site_title', 'Buku Tamu Digital') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('user-theme')
</head>
<body>
    <div class="form-container">
        <div class="success-container">
            <div class="success-icon">✅</div>
            <h1 class="success-title">Data Berhasil Disimpan!</h1>
            <p class="success-message">Terima kasih telah mengisi formulir buku tamu. Data Anda telah tersimpan dengan aman.</p>
            
            <div class="btn-group">
                <a href="{{ route('buku-tamu.create') }}" class="btn btn-primary">
                    📝 Isi Form Lagi
                </a>
                <a href="{{ url('/') }}" class="btn btn-secondary">
                    🏠 Kembali
                </a>
            </div>
        </div>
        
        <div class="form-footer">
            {{ \App\Models\Setting::getValue('footer_text', '© 2024 Buku Tamu Digital. All rights reserved.') }}
        </div>
    </div>
    
    <script>
        // Auto redirect after 5 seconds
        setTimeout(() => {
            window.location.href = '{{ route('buku-tamu.create') }}';
        }, 5000);
    </script>
</body>
</html>
