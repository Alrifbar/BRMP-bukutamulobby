<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Buku Tamu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('admin.theme-styles')
</head>
<body>
    <div class="layout">
        <!-- Sidebar kiri -->
        @include('admin.partials.sidebar')


        <!-- Konten utama -->
        <main class="main">
            <header class="main-header">
                <div class="main-header-title">
                    <h1>Dashboard</h1>
                    <p>Selamat datang di panel administrasi buku tamu digital</p>
                </div>
                <div class="main-header-date">
                    {{ now()->translatedFormat('d F Y, l') }}
                </div>
            </header>

            @if(session('success'))
                <div class="alert alert-success">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    ❌ {{ session('error') }}
                </div>
            @endif

            <!-- Stats Cards -->
            <div class="stats-row">
                <div class="stat-card">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                        <span class="label">Total Pengunjung</span>
                    </div>
                    <span class="value">{{ $totalPengunjung ?? 0 }}</span>
                    <span class="badge">+{{ $hariIni ?? 0 }} hari ini</span>
                </div>
                <div class="stat-card">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div class="stat-icon"><i class="fa-solid fa-calendar-days"></i></div>
                        <span class="label">Bulan Ini</span>
                    </div>
                    <span class="value">{{ $bulanIni ?? 0 }}</span>
                    <span class="badge">Pengunjung</span>
                </div>
                <div class="stat-card">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div class="stat-icon"><i class="fa-solid fa-chart-line"></i></div>
                        <span class="label">Tingkat Kunjungan</span>
                    </div>
                    <span class="value">{{ number_format($persentaseTarget ?? 0, 1) }}%</span>
                    <span class="badge">Target bulanan</span>
                </div>
                
                <!-- Grafik Lengkap Link Card -->
                <a href="{{ route('admin.grafik') }}" class="stat-card" style="text-decoration: none; background: linear-gradient(135deg, var(--custom-primary), var(--custom-secondary)); color: white;">
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom: 8px;">
                        <div class="stat-icon" style="background: rgba(255,255,255,0.2); color: white;"><i class="fa-solid fa-chart-pie"></i></div>
                        <span class="label" style="color: rgba(255,255,255,0.9);">Analisis</span>
                    </div>
                    <span class="value" style="font-size: 20px;">Grafik Lengkap</span>
                    <span class="badge" style="background: rgba(255,255,255,0.2); color: white;">Lihat Detail &rarr;</span>
                </a>
            </div>

            <!-- Two Column Layout for Main Content -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 24px;">
                
                <!-- Left Column: Popular Services Grid -->
                <div style="display: flex; flex-direction: column; gap: 24px;">
                    <section class="card">
                        <div class="card-header">
                            <div>
                                <div class="card-title">Layanan Populer</div>
                                <div class="card-subtitle">Layanan yang paling sering dikunjungi</div>
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px;">
                            @forelse($layananPopuler as $layanan)
                                <div style="padding: 12px; background: #f8fafc; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; border: 1px solid rgba(148,163,184,0.35);">
                                    <div style="display:flex; align-items:center;">
                                        <span class="tile-icon"><i class="fa-solid fa-fire"></i></span>
                                        <span style="font-size: 14px;">{{ $layanan['nama'] }}</span>
                                    </div>
                                    <span style="font-weight: 600; color: var(--custom-primary);">{{ $layanan['total'] }}</span>
                                </div>
                            @empty
                                <div style="text-align: center; padding: 12px; color: #6b7280; font-size: 14px;">Belum ada data kunjungan</div>
                            @endforelse
                        </div>
                    </section>
                </div>

                <!-- Right Column: Additional Info Cards -->
                <div style="display: flex; flex-direction: column; gap: 24px;">
                    <!-- Today's Summary -->
                    <section class="card">
                        <div class="card-header">
                            <div>
                                <div class="card-title">Ringkasan Hari Ini</div>
                                <div class="card-subtitle">Statistik kunjungan hari ini</div>
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                            <div style="text-align: center; padding: 16px; background: rgba(34, 197, 94, 0.1); border-radius: 12px;">
                                <div style="display:flex; align-items:center; justify-content:center; gap:8px;">
                                    <span class="tile-icon"><i class="fa-solid fa-sun"></i></span>
                                    <div style="font-size: 24px; font-weight: 700; color: var(--custom-primary);">{{ $hariIni ?? 0 }}</div>
                                </div>
                                <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">Kunjungan Hari Ini</div>
                            </div>
                            <div style="text-align: center; padding: 16px; background: rgba(59, 130, 246, 0.1); border-radius: 12px;">
                                <div style="display:flex; align-items:center; justify-content:center; gap:8px;">
                                    <span class="tile-icon"><i class="fa-solid fa-calendar-week"></i></span>
                                    <div style="font-size: 24px; font-weight: 700; color: var(--custom-secondary);">{{ $mingguIni ?? 0 }}</div>
                                </div>
                                <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">Kunjungan Minggu Ini</div>
                            </div>
                        </div>
                    </section>

                    

                    
                </div>
            </div>

            <!-- Recent Activity (Full width below) -->
            <section class="card recent-activity" style="margin-top: 24px;">
                <div class="card-header">
                    <div>
                        <div class="card-title">Aktivitas Terkini</div>
                        <div class="card-subtitle">5 kunjungan terakhir</div>
                    </div>
                </div>
                @if($pengunjungTerbaru && $pengunjungTerbaru->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Instansi</th>
                                    <th>Keperluan</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pengunjungTerbaru as $visitor)
                                    <tr>
                                        <td>{{ $visitor->nama }}</td>
                                        <td>{{ $visitor->instansi }}</td>
                                        <td>
                                            @if($visitor->keperluan_kategori == '1')
                                                Layanan Pengelolaan Hasil
                                            @elseif($visitor->keperluan_kategori == '2')
                                                Layanan Pemanfaatan Hasil
                                            @elseif($visitor->keperluan_kategori == '3')
                                                Layanan Perpustakaan
                                            @elseif($visitor->keperluan_kategori == '4')
                                                Layanan Magang
                                            @elseif($visitor->keperluan_kategori == '5')
                                                Layanan Informasi dan Dokumentasi
                                            @elseif($visitor->keperluan_kategori == '6')
                                                Layanan Publikasi Warta
                                            @elseif($visitor->keperluan_kategori == '7')
                                                Rapat/Pertemuan
                                            @elseif($visitor->keperluan_kategori == '8')
                                                {{ $visitor->keperluan_lainnya ?? 'Lainnya' }}
                                            @else
                                                {{ $visitor->keperluan_lainnya ?: 'Tidak diketahui' }}
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($visitor->tanggal_kunjungan)->format('d M Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p style="text-align: center; opacity: 0.7; padding: 40px;">Belum ada data kunjungan</p>
                @endif
            </section>
        </main>


    </div>
</body>
</html>
