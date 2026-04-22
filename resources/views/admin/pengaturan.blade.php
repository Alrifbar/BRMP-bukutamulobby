<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengaturan - Admin Buku Tamu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    @include('admin.theme-styles')
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .settings-container {
            max-width: 1200px;
            margin: 0 auto;
            animation: fadeInUp 0.6s ease-out;
        }

        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 32px;
            margin-bottom: 32px;
        }

        .card {
            background: #ffffff;
            border-radius: 24px;
            padding: 32px;
            box-shadow: 
                0 8px 32px rgba(15, 23, 42, 0.08),
                0 1px 3px rgba(15, 23, 42, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--custom-primary), var(--custom-secondary));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 
                0 20px 60px rgba(15, 23, 42, 0.12),
                0 8px 24px rgba(34, 197, 94, 0.15);
            border-color: rgba(34, 197, 94, 0.2);
        }

        .card:hover::before {
            opacity: 1;
        }

        .card-header {
            margin-bottom: 28px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f1f5f9;
            position: relative;
        }

        .card-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--custom-text);
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }

        .card-title i {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--custom-primary), var(--custom-secondary));
            color: white;
            border-radius: 12px;
            font-size: 16px;
        }

        .card-description {
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
            padding-left: 52px;
        }

        .form-group {
            margin-bottom: 24px;
            animation: slideIn 0.5s ease-out;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 16px;
            font-size: 15px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #fafbfc;
            color: #1f2937;
        }

        .form-control:focus {
            border-color: var(--custom-primary);
            background: #ffffff;
            outline: none;
            box-shadow: 
                0 0 0 4px rgba(34, 197, 94, 0.1),
                0 8px 24px rgba(34, 197, 94, 0.15);
            transform: translateY(-2px);
        }

        input[type="color"] {
            -webkit-appearance: none;
            border: none;
            width: 100%;
            height: 60px;
            padding: 0;
            border-radius: 16px;
            overflow: hidden;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        input[type="color"]:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }

        input[type="color"]::-webkit-color-swatch-wrapper {
            padding: 0;
        }

        input[type="color"]::-webkit-color-swatch {
            border: none;
            border-radius: 16px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--custom-primary) 0%, var(--custom-secondary) 100%);
            color: white;
            border: none;
            padding: 18px 32px;
            border-radius: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
            font-size: 16px;
            box-shadow: 0 8px 24px rgba(34, 197, 94, 0.25);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(34, 197, 94, 0.35);
        }
        
        .help-text {
            display: block;
            font-size: 12px;
            color: #6b7280;
            margin-top: 6px;
            line-height: 1.5;
        }
        
        .info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            margin: 20px 0;
        }
        
        .info-box h4 {
            color: #374151;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        /* Dark Mode Styling */
        html.theme-dark .settings-container {
            color: var(--custom-text);
        }
        
        html.theme-dark .card {
            background: #121212 !important;
            border: 1px solid #2A2A2A !important;
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.4),
                0 1px 3px rgba(0, 0, 0, 0.3) !important;
        }
        
        html.theme-dark .card:hover {
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.6),
                0 8px 24px rgba(34, 197, 94, 0.3) !important;
            border-color: rgba(34, 197, 94, 0.4) !important;
        }
        
        html.theme-dark .card-header {
            border-bottom: 2px solid #2A2A2A !important;
        }
        
        html.theme-dark .card-title {
            color: var(--custom-text) !important;
        }
        
        html.theme-dark .card-title i {
            background: linear-gradient(135deg, var(--custom-primary), var(--custom-secondary));
            color: white;
        }
        
        html.theme-dark .card-description {
            color: rgba(255, 255, 255, 0.7) !important;
        }
        
        html.theme-dark .form-label {
            color: var(--custom-text) !important;
        }
        
        html.theme-dark .form-control {
            background: #0F0F0F !important;
            border: 2px solid #2A2A2A !important;
            color: var(--custom-text) !important;
        }
        
        html.theme-dark .form-control:focus {
            background: #151515 !important;
            border-color: var(--custom-primary) !important;
            box-shadow: 
                0 0 0 4px rgba(34, 197, 94, 0.2),
                0 8px 24px rgba(34, 197, 94, 0.3) !important;
        }
        
        html.theme-dark input[type="color"] {
            background: #0F0F0F !important;
            border: 2px solid #2A2A2A !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4) !important;
        }
        
        html.theme-dark input[type="color"]:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.6) !important;
        }
        
        html.theme-dark .help-text {
            color: rgba(255, 255, 255, 0.6) !important;
        }
        
        html.theme-dark .info-box {
            background: #1A1A1A !important;
            border: 1px solid #2A2A2A !important;
        }
        
        html.theme-dark .info-box h4 {
            color: var(--custom-text) !important;
        }
        
        html.theme-dark .info-box p {
            color: rgba(255, 255, 255, 0.7) !important;
        }
        
        html.theme-dark .btn-primary {
            background: linear-gradient(135deg, var(--custom-primary), var(--custom-secondary)) !important;
            color: white !important;
            box-shadow: 0 8px 24px rgba(34, 197, 94, 0.4) !important;
        }
        
        html.theme-dark .btn-primary:hover {
            box-shadow: 0 12px 32px rgba(34, 197, 94, 0.5) !important;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(34, 197, 94, 0.35);
        }

        .btn-primary:active {
            transform: translateY(-1px);
        }

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
            animation: slideIn 0.5s ease-out;
        }

        .info-box {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 20px;
            margin-top: 24px;
        }

        .info-box h4 {
            margin: 0 0 12px 0;
            font-size: 15px;
            font-weight: 600;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-box ul {
            margin: 0;
            padding-left: 20px;
            font-size: 13px;
            color: #6b7280;
            line-height: 1.8;
        }

        .info-box li {
            margin-bottom: 6px;
        }

        .help-text {
            color: #64748b;
            font-size: 12px;
            margin-top: 6px;
            display: block;
            line-height: 1.5;
        }

        .logo-preview {
            margin-top: 16px;
            text-align: center;
        }

        .logo-preview img {
            max-width: 140px;
            max-height: 90px;
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            box-shadow: 0 6px 16px rgba(0,0,0,0.1);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .settings-grid {
                grid-template-columns: 1fr;
                gap: 24px;
            }
            
            .card {
                padding: 24px;
                border-radius: 20px;
            }
            
            .card-title {
                font-size: 18px;
            }
            
            .card-description {
                padding-left: 0;
                margin-top: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="layout">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')


        <!-- Main Content -->
        <main class="main">
            <header class="main-header">
                <div class="main-header-title">
                    <h1>Pengaturan</h1>
                    <p>Kustomisasi tampilan dan konten website</p>
                </div>
                <div class="main-header-date">
                    {{ now()->translatedFormat('d F Y, l') }}
                </div>
            </header>

            @if(session('success'))
                <div class="success-message">
                    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                </div>
            @endif

            <div class="settings-container">
                <div class="settings-grid">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="bi bi-database-down"></i>
                                Backup Data
                            </h3>
                            <p class="card-description">Download backup database (SQL) untuk menyimpan data saat ini.</p>
                        </div>
                        <div class="form-group" style="display:flex; flex-direction:column; gap:12px;">
                            <form action="{{ route('admin.schedule-sql-download') }}" method="POST" style="width:auto; align-self:flex-start;">
                                @csrf
                                <button type="submit" class="btn-primary" style="width:auto;">
                                    <i class="bi bi-download"></i>
                                    Unduh SQL
                                </button>
                            </form>
                            <div class="info-box" style="margin:0;">
                                <h4><i class="bi bi-clock-history"></i> Backup Otomatis</h4>
                                <form action="{{ route('admin.backup.update-settings') }}" method="POST" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:12px; align-items:end;">
                                    @csrf
                                    @method('POST')
                                    <div class="form-group" style="margin:0;">
                                        <label class="form-label">Jangka Waktu Otomatis</label>
                                        @php 
                                            $interval = \App\Models\Setting::getValue('auto_backup_interval', 'hourly'); 
                                            $customVal = \App\Models\Setting::getValue('auto_backup_custom_value');
                                            $customUnit = \App\Models\Setting::getValue('auto_backup_custom_unit', 'minutes');
                                        @endphp
                                        <select id="auto_interval" name="auto_backup_interval" class="form-control">
                                            <option value="disabled" {{ $interval==='disabled' ? 'selected' : '' }}>Nonaktif</option>
                                            <option value="hourly" {{ $interval==='hourly' ? 'selected' : '' }}>Per 1 Jam</option>
                                            <option value="daily" {{ $interval==='daily' ? 'selected' : '' }}>Harian</option>
                                            <option value="weekly" {{ $interval==='weekly' ? 'selected' : '' }}>Mingguan</option>
                                            <option value="monthly" {{ $interval==='monthly' ? 'selected' : '' }}>Bulanan</option>
                                            <option value="custom" {{ $interval==='custom' ? 'selected' : '' }}>Custom</option>
                                        </select>
                                        <div id="customInterval" style="display: {{ $interval==='custom' ? 'flex' : 'none' }}; gap:8px; margin-top:8px; align-items:center;">
                                            <span style="font-size:12px; color:#64748b;">Setiap</span>
                                            <input type="number" min="1" max="1440" name="auto_backup_custom_value" class="form-control" style="max-width:120px;" value="{{ $customVal }}">
                                            <select name="auto_backup_custom_unit" class="form-control" style="max-width:160px;">
                                                <option value="minutes" {{ $customUnit==='minutes' ? 'selected' : '' }}>Menit</option>
                                                <option value="hours" {{ $customUnit==='hours' ? 'selected' : '' }}>Jam</option>
                                                <option value="days" {{ $customUnit==='days' ? 'selected' : '' }}>Hari</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group" style="margin:0;">
                                        <label class="form-label">Lokasi Penyimpanan</label>
                                        <input type="text" name="backup_sql_dir" class="form-control" value="{{ \App\Models\Setting::getValue('backup_sql_dir') ?: 'C:\\xampp\\htdocs\\buku-tamu\\backup.sql' }}" placeholder="Contoh: C:\xampp\htdocs\buku-tamu\backup.sql">
                                        <span class="help-text">Folder atau path file .sql tujuan penyimpanan backup.</span>
                                        <span class="help-text">File dibuat di: {{ \App\Models\Setting::getValue('backup_sql_dir') ?: storage_path('app/backups/sql') }}</span>
                                    </div>
                                    <button type="submit" class="btn-primary" style="width:auto;">
                                        <i class="bi bi-save"></i> Simpan Pengaturan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="bi bi-arrow-counterclockwise"></i>
                                Restore Data
                            </h3>
                            <p class="card-description">Upload file SQL untuk mengembalikan data. Peringatan: Data saat ini akan ditimpa.</p>
                        </div>
                        <form action="{{ route('admin.restore-sql') }}" method="POST" enctype="multipart/form-data" style="display:flex; flex-direction:column; gap:16px;">
                            @csrf
                            <input type="file" name="sql_file" accept=".sql" class="form-control">
                            <button type="submit" class="btn-primary" style="width:auto; background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);">
                                <i class="bi bi-upload"></i>
                                Restore SQL
                            </button>
                        </form>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="bi bi-trash-fill"></i>
                                Pembersihan Foto Otomatis
                            </h3>
                            <p class="card-description">Hapus foto selfie pengunjung yang sudah berumur lebih dari 1 tahun secara otomatis.</p>
                        </div>
                        <form action="{{ route('admin.backup.update-settings') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Status Hapus Foto Otomatis</label>
                                @php $autoDelete = \App\Models\Setting::getValue('auto_delete_old_photos', 'disabled'); @endphp
                                <select name="auto_delete_old_photos" class="form-control">
                                    <option value="disabled" {{ $autoDelete === 'disabled' ? 'selected' : '' }}>Nonaktif</option>
                                    <option value="enabled" {{ $autoDelete === 'enabled' ? 'selected' : '' }}>Aktif (Hapus foto > 1 Tahun)</option>
                                </select>
                                <span class="help-text">Jika aktif, sistem akan menghapus file foto fisik untuk menghemat ruang, namun data teks pengunjung tetap ada.</span>
                            </div>
                            <button type="submit" class="btn-primary">
                                <i class="bi bi-save"></i>
                                Simpan Pengaturan Foto
                            </button>
                        </form>
                    </div>
                    @if(session('auto_backup_ready') || session('scheduled_download_ready'))
                        <script>
                            setTimeout(function(){ window.location.href='{{ route('admin.download-sql', ['existing' => 1]) }}'; }, 300);
                        </script>
                    @endif
                    
                    
                    <!-- Logo Settings -->
                    <div class="card" style="display:none;">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="bi bi-image"></i>
                                Pengaturan Logo
                            </h3>
                            <p class="card-description">Upload 1 logo untuk digunakan di semua halaman (aplikasi & login)</p>
                        </div>
                        <form action="{{ route('admin.pengaturan.logo.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label class="form-label">Logo Universal</label>
                                <input type="file" name="app_logo" class="form-control" accept="image/*" onchange="previewLogo(event, 'logo_preview')">
                                <span class="help-text">Format: JPG, PNG. Maksimal: 2MB. Logo ini akan digunakan di seluruh aplikasi.</span>
                                
                                @if($settings->app_logo)
                                    <div class="logo-preview">
                                        <p style="margin: 0 0 8px 0; font-size: 12px; color: #64748b;">Logo Saat Ini:</p>
                                        <img src="{{ asset('storage/logos/' . $settings->app_logo) }}" alt="Logo">
                                    </div>
                                @endif
                                
                                <div id="logo_preview"></div>
                            </div>

                            <div class="info-box">
                                <h4><i class="bi bi-info-circle-fill"></i> Info Penggunaan Logo</h4>
                                <ul>
                                    <li>Logo ini akan otomatis digunakan di halaman login</li>
                                    <li>Logo ini akan muncul di dashboard admin</li>
                                    <li>Logo ini akan tampil di seluruh halaman aplikasi</li>
                                    <li>Tidak perlu upload logo terpisah untuk setiap halaman</li>
                                </ul>
                            </div>

                            <button type="submit" class="btn-primary">
                                <i class="bi bi-upload"></i>
                                Upload Logo
                            </button>
                        </form>
                    </div>

                    <!-- Buku Tamu Page Text Settings -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="bi bi-book"></i>
                                Teks Halaman Buku Tamu
                            </h3>
                            <p class="card-description">Edit teks yang muncul di halaman buku tamu (http://127.0.0.1:8001/buku-tamu)</p>
                        </div>
                        <form action="{{ route('admin.pengaturan.buku-tamu.text.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label class="form-label">Judul Halaman Buku Tamu</label>
                                <input type="text" name="welcome_text" class="form-control" value="{{ \App\Models\Setting::getValue('welcome_text', 'Buku Tamu Digital') }}" placeholder="Contoh: Buku Tamu Digital">
                                <span class="help-text">Judul utama yang muncul di halaman buku tamu</span>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Subjudul Halaman Buku Tamu</label>
                                <input type="text" name="subwelcome_text" class="form-control" value="{{ \App\Models\Setting::getValue('subwelcome_text', 'Silakan isi form di bawah ini') }}" placeholder="Contoh: Silakan login untuk mengakses sistem buku tamu digital">
                                <span class="help-text">Teks deskripsi di bawah judul halaman buku tamu</span>
                            </div>

                            <div class="info-box">
                                <h4><i class="bi bi-eye-fill"></i> Preview Lokasi</h4>
                                <p style="margin: 0; font-size: 12px; color: #6b7280; line-height: 1.6;">
                                    Teks ini akan muncul di:<br>
                                    <strong>URL:</strong> http://127.0.0.1:8001/buku-tamu<br>
                                    <strong>Lokasi:</strong> Bagian atas form buku tamu (di bawah logo)
                                </p>
                            </div>

                            <button type="submit" class="btn-primary">
                                <i class="bi bi-save"></i>
                                Simpan Teks Buku Tamu
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Second Row - Admin Login & Theme Settings -->
                <div class="settings-grid">
                    <!-- Admin Login Page Text Settings -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="bi bi-box-arrow-in-right"></i>
                                Teks Halaman Login Admin
                            </h3>
                            <p class="card-description">Edit teks yang muncul di halaman login admin</p>
                        </div>
                        <form action="{{ route('admin.pengaturan.login.text.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label class="form-label">Judul Login Admin</label>
                                <input type="text" name="admin_login_title" class="form-control" value="{{ \App\Models\Setting::getValue('admin_login_title', 'Hello, welcome!') }}" placeholder="Contoh: Selamat Datang Admin">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Subjudul Login Admin</label>
                                <input type="text" name="admin_login_subtitle" class="form-control" value="{{ \App\Models\Setting::getValue('admin_login_subtitle', 'Silakan login untuk mengakses sistem buku tamu digital') }}" placeholder="Contoh: Masukkan kredensial admin untuk melanjutkan">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Warna Judul</label>
                                <input type="color" name="login_title_color" class="form-control" value="{{ \App\Models\Setting::getValue('login_title_color', '#667eea') }}">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Warna Subjudul</label>
                                <input type="color" name="login_subtitle_color" class="form-control" value="{{ \App\Models\Setting::getValue('login_subtitle_color', '#64748b') }}">
                            </div>

                            <div class="info-box">
                                <h4><i class="bi bi-image"></i> Background Halaman Login</h4>
                                <p style="margin: 0; font-size: 12px; color: #6b7280; line-height: 1.6;">
                                    Background login saat ini dikunci ke gambar: <strong>images/bc depan.jpg</strong><br>
                                    Untuk mengganti gambar, silakan ganti file tersebut di folder <code>public/images/</code>.
                                </p>
                            </div>

                            <button type="submit" class="btn-primary">
                                <i class="bi bi-save"></i>
                                Simpan Pengaturan Login
                            </button>
                        </form>
                    </div>

                    <!-- Theme Color Settings -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="bi bi-palette-fill"></i>
                                Tema Warna Website
                            </h3>
                            <p class="card-description">Kustomisasi warna tema untuk seluruh website</p>
                        </div>
                        <form action="{{ route('admin.pengaturan.theme.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label class="form-label">Warna Utama</label>
                                <input type="color" name="primary_color" class="form-control" value="{{ \App\Models\Setting::getValue('primary_color', '#22c55e') }}">
                                <span class="help-text">Warna utama tema website (sidebar, buttons, accents)</span>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Warna Sekunder</label>
                                <input type="color" name="secondary_color" class="form-control" value="{{ \App\Models\Setting::getValue('secondary_color', '#3b82f6') }}">
                                <span class="help-text">Warna sekunder untuk hover states dan gradients</span>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Warna Aksen</label>
                                <input type="color" name="accent_color" class="form-control" value="{{ \App\Models\Setting::getValue('accent_color', '#f59e0b') }}">
                                <span class="help-text">Warna aksen untuk highlights dan notifications</span>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Warna Background</label>
                                <input type="color" name="background_color" class="form-control" value="{{ \App\Models\Setting::getValue('background_color', '#ffffff') }}">
                                <span class="help-text">Warna background utama halaman</span>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Warna Teks Utama</label>
                                <input type="color" name="text_color" class="form-control" value="{{ \App\Models\Setting::getValue('text_color', '#1b1b18') }}">
                                <span class="help-text">Warna teks utama untuk headings dan content</span>
                            </div>

                            <div class="info-box">
                                <h4><i class="bi bi-info-circle-fill"></i> Info Tema Warna</h4>
                                <p style="margin: 0; font-size: 12px; color: #6b7280; line-height: 1.6;">
                                    Perubahan warna akan diterapkan ke:<br>
                                    • Sidebar admin<br>
                                    • Buttons dan links<br>
                                    • Card headers<br>
                                    • Form elements<br>
                                    • Seluruh UI components
                                </p>
                            </div>

                            <button type="submit" class="btn-primary">
                                <i class="bi bi-brush-fill"></i>
                                Simpan Tema Warna
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Preview functions
        function previewLogo(event, previewId) {
            const file = event.target.files[0];
            const preview = document.getElementById(previewId);
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview" style="max-width: 120px; max-height: 80px; border-radius: 8px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">`;
                };
                reader.readAsDataURL(file);
            }
        }

        function previewLoginBackground(event, previewId) {
            const file = event.target.files[0];
            const preview = document.getElementById(previewId);
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Background Preview" style="max-width: 200px; max-height: 120px; border-radius: 8px; border: 2px solid #e5e7eb; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">`;
                };
                reader.readAsDataURL(file);
            }
        }
        // Toggle custom interval
        (function() {
            const sel = document.getElementById('auto_interval');
            if (sel) {
                const box = document.getElementById('customInterval');
                const onChange = () => {
                    if (!box) return;
                    box.style.display = sel.value === 'custom' ? 'flex' : 'none';
                };
                sel.addEventListener('change', onChange);
            }
        })();
    </script>
</body>
</html>
                </form>
            </div>
