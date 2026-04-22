<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Grafik Statistik - Admin Buku Tamu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('admin.theme-styles')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Chart.js DataLabels Plugin -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
</head>
<body>
    <div class="layout">
        <!-- Sidebar kiri -->
        @include('admin.partials.sidebar')

        <!-- Konten utama -->
        <main class="main">
            <header class="main-header">
                <div class="main-header-title">
                    <h1>Grafik Statistik</h1>
                    <p>Visualisasi data pengunjung Buku Tamu Digital</p>
                </div>
                <div class="main-header-date">
                    {{ now()->translatedFormat('d F Y, l') }}
                </div>
            </header>

            <div style="max-width: 1200px; margin: 0 auto; padding: 0 12px;">
            <!-- Filter Section -->
            <div class="card mb-4">
                <div class="card-header pb-0 border-0" style="display:flex; align-items:center; justify-content:space-between;">
                    <h5 class="card-title">Filter Data</h5>
                    <div style="display:flex; gap:8px;">
                        <button type="button" class="btn btn-outline-secondary" onclick="exportAllExcel()">Unduh Semua (Excel)</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="exportAllImagesCombined()">Unduh Semua (Gambar)</button>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <form id="filterForm" action="{{ route('admin.grafik') }}" method="GET" class="row g-3 align-items-end">
                        <!-- Year Filter -->
                        <div class="col-md-3">
                            <label for="year" class="form-label">Pilih Tahun</label>
                            <select name="year" id="year" class="form-select" onchange="setYearDates()">
                                @foreach($availableYears as $y)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-1 text-center align-self-center pt-4">
                            <span class="text-muted">ATAU</span>
                        </div>

                        <!-- Custom Date Range -->
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Dari Tanggal</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">Sampai Tanggal</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa-solid fa-filter me-2"></i> Tampilkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Script to handle year selection -->
            <script>
                function setYearDates() {
                    // Get selected year
                    const selectedYear = document.getElementById('year').value;
                    
                    // Set custom date inputs to the selected year range
                    document.getElementById('start_date').value = selectedYear + '-01-01';
                    document.getElementById('end_date').value = selectedYear + '-12-31';
                    
                    // Tidak auto-submit; pengguna harus klik tombol "Tampilkan"
                }
            </script>

            <div class="row mb-4" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                <div class="stat-card" style="background: linear-gradient(135deg, var(--custom-primary), var(--custom-secondary)); color: white;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div class="stat-icon" style="background: rgba(255,255,255,0.2); color: white;"><i class="fa-solid fa-users"></i></div>
                        <span class="label" style="color: rgba(255,255,255,0.9);">Total Filter</span>
                    </div>
                    <span class="value">{{ number_format($totalFilter) }}</span>
                    <span class="badge" style="background: rgba(255,255,255,0.2); color: white;">Pengunjung</span>
                </div>
                
                <div class="stat-card">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div class="stat-icon"><i class="fa-solid fa-sun"></i></div>
                        <span class="label">Hari Ini</span>
                    </div>
                    <span class="value">{{ number_format($hariIni) }}</span>
                </div>
                
                <div class="stat-card">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div class="stat-icon"><i class="fa-solid fa-calendar-days"></i></div>
                        <span class="label">Bulan Ini</span>
                    </div>
                    <span class="value">{{ number_format($bulanIni) }}</span>
                </div>
                
                <div class="stat-card">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div class="stat-icon"><i class="fa-solid fa-calendar"></i></div>
                        <span class="label">Tahun Ini</span>
                    </div>
                    <span class="value">{{ number_format($tahunIni) }}</span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 20px;">
                <!-- 1. Grafik Tren Bulanan (Tahun Terpilih) -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">1. Grafik Tren Bulanan ({{ $year }})</h5>
                        <div style="margin-top:8px;">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportChartImage('Grafik Tren', document.getElementById('grafikTren'))">Unduh Gambar</button>
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.grafik.export-chart-excel', ['type'=>'trend', 'start_date'=>$startDate, 'end_date'=>$endDate]) }}">Unduh Excel</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="position: relative; height: 360px;">
                            <canvas id="grafikTren"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 20px;">
                <!-- 2. Grafik Pengunjung Tahunan -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">2. Grafik Tahunan (5 Tahun Terakhir)</h5>
                        <div style="margin-top:8px;">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportChartImage('Grafik Tahunan', document.getElementById('grafikTahunan'))">Unduh Gambar</button>
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.grafik.export-chart-excel', ['type'=>'tahunan', 'start_date'=>$startDate, 'end_date'=>$endDate]) }}">Unduh Excel</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="position: relative; height: 320px;">
                            <canvas id="grafikTahunan"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Grafik Gender -->
            <div style="display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">3. Grafik Gender</h5>
                        <div style="margin-top:8px;">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportChartImage('Grafik Gender', document.getElementById('grafikGenderLine'))">Unduh Gambar</button>
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.grafik.export-chart-excel', ['type'=>'gender_line', 'start_date'=>$startDate, 'end_date'=>$endDate]) }}">Unduh Excel</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="margin-bottom:8px; display:flex; justify-content:flex-end; gap:12px; align-items:center;">
                            <span class="badge bg-light text-dark">Total L: {{ number_format(array_sum($genderLineDataL ?? [])) }}</span>
                            <span class="badge bg-light text-dark">Total P: {{ number_format(array_sum($genderLineDataP ?? [])) }}</span>
                            <span class="badge bg-light text-dark">Total: {{ number_format((array_sum($genderLineDataL ?? []) + array_sum($genderLineDataP ?? []))) }}</span>
                        </div>
                        <div style="position: relative; height: 320px;">
                            <canvas id="grafikGenderLine"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 20px;">
                <!-- 4. Grafik Keperluan -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">4. Grafik Kategori Keperluan</h5>
                        <div style="margin-top:8px;">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportChartImage('Grafik Keperluan', document.getElementById('grafikKeperluan'))">Unduh Gambar</button>
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.grafik.export-chart-excel', ['type'=>'keperluan', 'start_date'=>$startDate, 'end_date'=>$endDate]) }}">Unduh Excel</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="position: relative; height: 400px;">
                            <canvas id="grafikKeperluan"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <!-- 5. Grafik Usia -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">5. Grafik Usia</h5>
                        <div style="margin-top:8px;">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportChartImage('Grafik Usia', document.getElementById('grafikUsia'))">Unduh Gambar</button>
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.grafik.export-chart-excel', ['type'=>'usia', 'start_date'=>$startDate, 'end_date'=>$endDate]) }}">Unduh Excel</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="margin-bottom:8px; display:flex; justify-content:flex-end; gap:12px; align-items:center;">
                            <span class="badge bg-light text-dark">Total: {{ number_format(array_sum($dataUmur ?? [])) }}</span>
                        </div>
                        <div style="position: relative; height: 320px;">
                            <canvas id="grafikUsia"></canvas>
                        </div>
                    </div>
                </div>

                <!-- 6. Grafik Pendidikan -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">6. Grafik Pendidikan (Grafik Batang)</h5>
                        <div style="margin-top:8px;">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportChartImage('Grafik Pendidikan', document.getElementById('grafikPendidikan'))">Unduh Gambar</button>
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.grafik.export-chart-excel', ['type'=>'pendidikan', 'start_date'=>$startDate, 'end_date'=>$endDate]) }}">Unduh Excel</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="margin-bottom:8px; display:flex; justify-content:flex-end; gap:12px; align-items:center;">
                            <span class="badge bg-light text-dark">Total: {{ number_format(array_sum($dataPendidikanFormatted['data'] ?? [])) }}</span>
                        </div>
                        <div style="position: relative; height: 320px;">
                            <canvas id="grafikPendidikan"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- End Charts -->
            </div>
        </main>
    </div>

    <script>
        // Register the datalabels plugin globally
        Chart.register(ChartDataLabels);
        // Ensure datalabels are hidden by default on all charts
        Chart.defaults.plugins.datalabels.display = false;

        document.addEventListener('DOMContentLoaded', function() {
            // Common options
            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 25 // Extra space for data labels on top of bars
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    datalabels: {
                        display: false, // Hide by default on web view
                        anchor: 'end',
                        align: 'top',
                        offset: 4,
                        clip: false,
                        formatter: function(value) {
                            return value > 0 ? value : '';
                        },
                        font: {
                            weight: 'bold',
                            size: 11
                        },
                        color: '#4b5563'
                    }
                }
            };

            // 1. Grafik Tren Pengunjung (Dynamic)
            const ctxTren = document.getElementById('grafikTren').getContext('2d');
            new Chart(ctxTren, {
                type: 'line',
                data: {
                    labels: @json($trendLabels),
                    datasets: [{
                        label: 'Jumlah Pengunjung',
                        data: @json($trendData),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: Object.assign({}, commonOptions, {
                    plugins: {
                        datalabels: {
                            display: false
                        }
                    }
                })
            });

            // 4. Grafik Keperluan
            const ctxKeperluan = document.getElementById('grafikKeperluan').getContext('2d');
            new Chart(ctxKeperluan, {
                type: 'bar',
                data: {
                    labels: @json($labelsKeperluan ?? []),
                    datasets: [{
                        label: 'Jumlah Pengunjung',
                        data: @json($grafikKeperluan ?? []),
                        backgroundColor: '#8b5cf6',
                        borderColor: '#7c3aed',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: Object.assign({}, commonOptions, {
                        indexAxis: 'y', // Horizontal bar chart
                        scales: {
                            x: { 
                                beginAtZero: true,
                                grid: { color: 'rgba(255,255,255,0.05)' }
                            },
                            y: { 
                                grid: { display: false },
                                ticks: {
                                    callback: function(value) {
                                        const label = this.getLabelForValue(value);
                                        if (label.length > 30) {
                                            return label.match(/.{1,30}(\s|$)/g); // Wrap text at 30 chars
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    })
            });

            // 5. Grafik Usia
            const ctxUsia = document.getElementById('grafikUsia').getContext('2d');
            const umurLabels = ['0-17', '18-25', '26-35', '36-45', '46-55', '56-65', '65+'];
            const umurL = @json($dataUmurL ?? []);
            const umurP = @json($dataUmurP ?? []);
            
            new Chart(ctxUsia, {
                type: 'bar',
                data: {
                    labels: umurLabels,
                    datasets: [
                        {
                            label: 'Laki-laki',
                            data: umurL,
                            backgroundColor: '#10b981',
                            borderColor: '#059669',
                            borderWidth: 1,
                            borderRadius: 4
                        },
                        {
                            label: 'Perempuan',
                            data: umurP,
                            backgroundColor: '#ef4444',
                            borderColor: '#dc2626',
                            borderWidth: 1,
                            borderRadius: 4
                        }
                    ]
                },
                options: Object.assign({}, commonOptions, {
                    scales: {
                        x: {
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true
                        }
                    }
                })
            });

            // 5. Grafik Pendidikan
            const ctxPendidikan = document.getElementById('grafikPendidikan').getContext('2d');
            new Chart(ctxPendidikan, {
                type: 'bar',
                data: {
                    labels: @json($dataPendidikanFormatted['labels'] ?? ['Tidak Ada Data']),
                    datasets: [{
                        label: 'Jumlah',
                        data: @json($dataPendidikanFormatted['data'] ?? [0]),
                        backgroundColor: '#f59e0b',
                        borderColor: '#d97706',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: Object.assign({}, commonOptions, {
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true }
                    }
                })
            });

            // 3. Grafik Pengunjung Tahunan (5 Tahun Terakhir)
            const ctxTahunan = document.getElementById('grafikTahunan').getContext('2d');
            new Chart(ctxTahunan, {
                type: 'bar',
                data: {
                    labels: @json($labelsTahunan ?? []),
                    datasets: [{
                        label: 'Total Pengunjung',
                        data: @json($grafikTahunan ?? []),
                        backgroundColor: '#3b82f6',
                        borderColor: '#2563eb',
                        borderWidth: 2,
                        borderRadius: 4
                    }]
                },
                options: Object.assign({}, commonOptions, {
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true }
                    }
                })
            });

            // 6. Grafik Gender (Line)
            (function() {
                const ctx = document.getElementById('grafikGenderLine').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($genderLineLabels ?? []),
                        datasets: [
                            {
                                label: 'Laki-laki',
                                data: @json($genderLineDataL ?? []),
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.3
                            },
                            {
                                label: 'Perempuan',
                                data: @json($genderLineDataP ?? []),
                                borderColor: '#ef4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.3
                            }
                        ]
                    },
                    options: Object.assign({}, commonOptions, {
                        plugins: {
                            datalabels: {
                                display: false
                            }
                        }
                    })
                });
            })();
        });
        
        function exportAllExcel() {
            const url = new URL('{{ route('admin.grafik.export-all-excel') }}', window.location.origin);
            const s = document.getElementById('start_date').value;
            const e = document.getElementById('end_date').value;
            if (s) url.searchParams.set('start_date', s);
            if (e) url.searchParams.set('end_date', e);
            window.location.href = url.toString();
        }
        
        function exportChartImage(name, element) {
            try {
                const canvas = element;
                if (!canvas) {
                    alert('Grafik tidak ditemukan');
                    return;
                }
                
                const trendDataArr = @json($trendData ?? []);
                const tahunanDataArr = @json($grafikTahunan ?? []);
                const genderLArr = @json($genderLineDataL ?? []);
                const genderPArr = @json($genderLineDataP ?? []);
                const keperluanArr = @json($grafikKeperluan ?? []);
                const usiaArr = @json($dataUmur ?? []);
                const pendidikanArr = @json($dataPendidikanFormatted['data'] ?? []);
                const sum = (arr) => (Array.isArray(arr) ? arr.reduce((a, b) => a + (b || 0), 0) : 0);
                
                const id = canvas.id || '';
                let info = '';
                if (id === 'grafikGenderLine') {
                    const l = sum(genderLArr); const p = sum(genderPArr);
                    info = `Total L: ${l}   Total P: ${p}   Total: ${l + p}`;
                } else if (id === 'grafikTren') {
                    info = `Total: ${sum(trendDataArr)}`;
                } else if (id === 'grafikTahunan') {
                    info = `Total: ${sum(tahunanDataArr)}`;
                } else if (id === 'grafikKeperluan') {
                    info = `Total: ${sum(keperluanArr)}`;
                } else if (id === 'grafikUsia') {
                    info = `Total: ${sum(usiaArr)}`;
                } else if (id === 'grafikPendidikan') {
                    info = `Total: ${sum(pendidikanArr)}`;
                }
                
                const scaleFactor = 4;
                const padX = 120;
                const headerH = Math.max(140, 40 * scaleFactor);
                const padBottom = 80;
                const outW = canvas.width * scaleFactor + padX * 2;
                const outH = canvas.height * scaleFactor + headerH + padBottom;
                
                const tempCanvas = document.createElement('canvas');
                const tempCtx = tempCanvas.getContext('2d');
                tempCanvas.width = outW;
                tempCanvas.height = outH;
                
                // Temporarily enable datalabels for export
                const originalDisplay = Chart.defaults.plugins.datalabels.display;
                Chart.getChart(canvas).options.plugins.datalabels.display = true;
                Chart.getChart(canvas).update('none');

                tempCtx.fillStyle = '#ffffff';
                tempCtx.fillRect(0, 0, outW, outH);
                
                tempCtx.textAlign = 'left';
                tempCtx.fillStyle = '#111827';
                tempCtx.font = `bold ${28 * scaleFactor}px Arial`;
                tempCtx.fillText(name, padX, 32 * scaleFactor);
                if (info) {
                    tempCtx.textAlign = 'right';
                    tempCtx.fillStyle = '#1f2937';
                    tempCtx.font = `600 ${22 * scaleFactor}px Arial`;
                    tempCtx.fillText(info, outW - padX, 32 * scaleFactor);
                }
                
                tempCtx.drawImage(canvas, padX, headerH, canvas.width * scaleFactor, canvas.height * scaleFactor);
                
                // Restore original state
                Chart.getChart(canvas).options.plugins.datalabels.display = originalDisplay;
                Chart.getChart(canvas).update('none');

                const dataUrl = tempCanvas.toDataURL('image/png', 1.0);
                const a = document.createElement('a');
                a.href = dataUrl;
                a.download = name.replace(/\s+/g,'_') + '.png';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                
            } catch (error) {
                console.error('Error exporting image:', error);
                alert('Error mengunduh gambar: ' + error.message);
            }
        }
        
        function exportAllImagesCombined() {
            try {
                const canvases = [
                    document.getElementById('grafikTren'),
                    document.getElementById('grafikTahunan'),
                    document.getElementById('grafikGenderLine'),
                    document.getElementById('grafikKeperluan'),
                    document.getElementById('grafikUsia'),
                    document.getElementById('grafikPendidikan')
                ].filter(Boolean);
                
                if (canvases.length === 0) {
                    alert('Grafik belum siap untuk diunduh');
                    return;
                }
                
                const dashboardTotals = {
                    totalFilter: {{ $totalFilter }},
                    hariIni: {{ $hariIni }},
                    bulanIni: {{ $bulanIni }},
                    tahunIni: {{ $tahunIni }}
                };
                
                const genderTotals = {
                    L: {{ array_sum($genderLineDataL ?? []) }},
                    P: {{ array_sum($genderLineDataP ?? []) }},
                    total: {{ array_sum($genderLineDataL ?? []) + array_sum($genderLineDataP ?? []) }}
                };
                
                const scale = 2;
                const baseWidth = 1240;
                const baseHeight = 1754;
                const canvas = document.createElement('canvas');
                canvas.width = baseWidth * scale;
                canvas.height = baseHeight * scale;
                const ctx = canvas.getContext('2d');
                ctx.scale(scale, scale);
                
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, baseWidth, baseHeight);
                
                const period = (document.getElementById('start_date').value || '') + ' - ' + (document.getElementById('end_date').value || '');
                ctx.fillStyle = '#111827';
                ctx.font = 'bold 28px Arial';
                ctx.textAlign = 'center';
                ctx.fillText('Laporan Grafik Pengunjung', baseWidth / 2, 60);
                ctx.font = '16px Arial';
                ctx.fillStyle = '#6b7280';
                ctx.fillText(period, baseWidth / 2, 88);
                
                const statY = 120;
                const statW = (baseWidth - 120) / 4;
                const statH = 80;
                const statX0 = 60;
                const stats = [
                    { label: 'Total Filter', value: dashboardTotals.totalFilter, badge: '' },
                    { label: 'Hari Ini', value: dashboardTotals.hariIni, badge: '' },
                    { label: 'Bulan Ini', value: dashboardTotals.bulanIni, badge: '' },
                    { label: 'Tahun Ini', value: dashboardTotals.tahunIni, badge: '' },
                ];
                
                stats.forEach((s, idx) => {
                    const x = statX0 + idx * statW;
                    // Background + border
                    ctx.fillStyle = '#f3f4f6';
                    ctx.fillRect(x, statY, statW - 15, statH);
                    ctx.strokeStyle = '#d1d5db';
                    ctx.lineWidth = 1;
                    ctx.strokeRect(x, statY, statW - 15, statH);
                    // Label
                    ctx.textAlign = 'left';
                    ctx.fillStyle = '#374151';
                    ctx.font = '600 16px Arial';
                    ctx.fillText(s.label, x + 16, statY + 26);
                    // Value
                    ctx.fillStyle = '#111827';
                    ctx.font = 'bold 30px Arial';
                    ctx.fillText(String(s.value || 0), x + 16, statY + 56);
                    // Badge
                    if (s.badge) {
                        const badgeText = s.badge;
                        ctx.font = '12px Arial';
                        const pad = 8;
                        const tw = ctx.measureText(badgeText).width;
                        const bx = x + 16;
                        const by = statY + 66;
                        ctx.fillStyle = '#e5e7eb';
                        ctx.fillRect(bx, by - 16, tw + pad * 2, 20);
                        ctx.fillStyle = '#374151';
                        ctx.fillText(badgeText, bx + pad, by);
                    }
                });
                
                
                const marginX = 60;
                const marginTop = statY + statH + 60;
                const colGap = 32;
                const rowGap = 24;
                const cellWidth = (baseWidth - marginX * 2 - colGap) / 2;
                const availableHeight = baseHeight - marginTop - rowGap * 2 - 40;
                const cellHeight = Math.floor(availableHeight / 3);
                
                // Helper function to sum array
                const sum = (arr) => (Array.isArray(arr) ? arr.reduce((a, b) => a + (b || 0), 0) : 0);
                
                // Data untuk ringkasan per diagram
                const trendDataArr = @json($trendData ?? []);
                const tahunanDataArr = @json($grafikTahunan ?? []);
                const genderLArr = @json($genderLineDataL ?? []);
                const genderPArr = @json($genderLineDataP ?? []);
                const keperluanArr = @json($grafikKeperluan ?? []);
                const usiaLArr = @json($dataUmurL ?? []);
                const usiaPArr = @json($dataUmurP ?? []);
                const pendidikanArr = @json($dataPendidikanFormatted['data'] ?? []);

                // Track original states to restore later
                const originalStates = [];

                canvases.forEach((srcCanvas, i) => {
                    // Enable datalabels for export
                    const chart = Chart.getChart(srcCanvas);
                    originalStates.push({ chart: chart, display: chart.options.plugins.datalabels.display });
                    chart.options.plugins.datalabels.display = true;
                    chart.update('none');

                    const col = i % 2;
                    const row = Math.floor(i / 2);
                    const x = marginX + col * (cellWidth + colGap);
                    const y = marginTop + row * (cellHeight + rowGap);
                    ctx.fillStyle = '#f9fafb';
                    ctx.fillRect(x - 6, y - 6, cellWidth + 12, cellHeight + 12);
                    ctx.strokeStyle = '#e5e7eb';
                    ctx.strokeRect(x - 6, y - 6, cellWidth + 12, cellHeight + 12);
                    const w = cellWidth;
                    const h = cellHeight;
                    ctx.drawImage(srcCanvas, x, y, w, h);
                    
                    // Tambahkan ringkasan total per diagram di pojok kanan atas
                    let info = '';
                    if (i === 0) { // Tren Bulanan
                        info = `Total: ${sum(trendDataArr)}`;
                    } else if (i === 1) { // Tahunan
                        info = `Total: ${sum(tahunanDataArr)}`;
                    } else if (i === 2) { // Gender
                        const l = sum(genderLArr); const p = sum(genderPArr);
                        info = `Total L: ${l}   Total P: ${p}   Total: ${l + p}`;
                    } else if (i === 3) { // Keperluan
                        info = `Total: ${sum(keperluanArr)}`;
                    } else if (i === 4) { // Usia
                        info = `Total: ${sum(usiaLArr) + sum(usiaPArr)}`;
                    } else if (i === 5) { // Pendidikan
                        info = `Total: ${sum(pendidikanArr)}`;
                    }
                    if (info) {
                        ctx.textAlign = 'right';
                        ctx.fillStyle = '#1f2937';
                        ctx.font = '600 13px Arial';
                        ctx.fillText(info, x + w - 10, y + 20);
                    }
                });

                // Restore original states
                originalStates.forEach(state => {
                    state.chart.options.plugins.datalabels.display = state.display;
                    state.chart.update('none');
                });
                
                ctx.textAlign = 'center';
                ctx.fillStyle = '#6b7280';
                ctx.font = '12px Arial';
                const now = new Date();
                const ts = `${now.getDate().toString().padStart(2,'0')}/${(now.getMonth()+1).toString().padStart(2,'0')}/${now.getFullYear()} ${now.getHours().toString().padStart(2,'0')}:${now.getMinutes().toString().padStart(2,'0')}`;
                ctx.fillText(`Dicetak: ${ts}`, baseWidth / 2, baseHeight - 20);
                
                const dataUrl = canvas.toDataURL('image/png', 1.0);
                const a = document.createElement('a');
                a.href = dataUrl;
                a.download = 'Semua_Grafik.png';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                
            } catch (error) {
                console.error('Error exporting combined images:', error);
                alert('Error mengunduh semua grafik: ' + error.message);
            }
        }
    </script>
</body>
</html>
