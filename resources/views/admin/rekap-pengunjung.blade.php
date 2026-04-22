<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Pengunjung - Admin Buku Tamu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('admin.theme-styles')
    <style>
        #rekapTable thead tr {
            background: linear-gradient(90deg, var(--custom-primary) 0%, var(--custom-secondary) 100%);
            color: #fff;
        }
        #rekapTable thead th {
            border-color: transparent;
        }
        #rekapTable tbody tr:nth-child(odd) {
            background-color: #f0fdf4;
        }
        #rekapTable tbody tr:nth-child(even) {
            background-color: #dcfce7;
        }
        #rekapTable tfoot tr {
            background: #f8fafc;
            border-top: 2px solid #e2e8f0;
        }
        html.theme-dark #rekapTable tbody tr:nth-child(odd) {
            background-color: rgba(29, 84, 109, 0.18);
        }
        html.theme-dark #rekapTable tbody tr:nth-child(even) {
            background-color: rgba(29, 84, 109, 0.12);
        }
        html.theme-dark tfoot,
        html.theme-dark tfoot tr,
        html.theme-dark tfoot td,
        html.theme-dark #rekapTable tfoot,
        html.theme-dark #rekapTable tfoot tr,
        html.theme-dark #rekapTable tfoot td {
            background-color: #1a1a2e !important;
            color: #ffffff !important;
        }
        html.theme-dark tfoot td span,
        html.theme-dark tfoot td div {
            color: #ffffff !important;
        }
        html.theme-dark tfoot .text-muted {
            color: rgba(255, 255, 255, 0.7) !important;
        }
        .badge.bg-success.fs-6,
        .badge.bg-secondary.fs-6 {
            padding: 6px 10px;
            border-radius: 999px;
        }
        .btn-outline-primary {
            border-color: var(--custom-accent);
            color: #166534;
        }
        .btn-outline-primary:hover {
            background-color: var(--custom-accent);
            color: #fff;
        }
        html.theme-dark .content-section .btn-group button,
        html.theme-dark .content-section button[style*="background: white"] {
            background: #1D546D !important;
            color: #F3F4F4 !important;
            border-color: rgba(95,149,152,0.35) !important;
        }
    </style>
</head>
<body>
    <div class="layout">
        <!-- Sidebar kiri -->
        @include('admin.partials.sidebar')


        <!-- Main content -->
        <main class="main-content">
            <header class="main-header">
                <div class="main-header-title">
                    <h1>Rekap Pengunjung</h1>
                    <p>Lihat statistik dan data pengunjung per tahun dan per bulan</p>
                    {{ now()->translatedFormat('d F Y, l') }}
                </div>
            </header>

            <section class="content-section">
                <!-- Year Selector -->
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-body">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                            <div>
                                <h5 class="card-title mb-0">Tahun: {{ $year }}</h5>
                            </div>
                            <div style="display: flex; gap: 8px; align-items: center;">
                                <!-- Export All Buttons -->
                                <div class="btn-group me-2">
                                    <button onclick="window.location.href='{{ route('admin.export.excel', ['year' => $year]) }}'" style="padding: 8px 16px; background: white; color: black; border: 1px solid #d1d5db; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; gap: 8px; align-items: center; font-size: 14px;">
                                        <i class="fa-solid fa-file-excel"></i>
                                        <span>Excel (Semua)</span>
                                    </button>
                                    <button onclick="window.location.href='{{ route('admin.export.pdf', ['year' => $year]) }}'" style="padding: 8px 16px; background: white; color: black; border: 1px solid #d1d5db; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; gap: 8px; align-items: center; font-size: 14px;">
                                        <i class="fa-solid fa-file-pdf"></i>
                                        <span>PDF (Semua)</span>
                                    </button>
                                    <button onclick="showImportModal()" style="padding: 8px 16px; background: white; color: black; border: 1px solid #d1d5db; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; gap: 8px; align-items: center; font-size: 14px;">
                                        <i class="fa-solid fa-file-import"></i>
                                        <span>Import Excel</span>
                                    </button>
                                    <button onclick="showDeleteModal()" style="padding: 8px 16px; background: white; color: black; border: 1px solid #d1d5db; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; gap: 8px; align-items: center; font-size: 14px;">
                                        <i class="fa-solid fa-trash"></i>
                                        <span>Hapus Data</span>
                                    </button>
                                </div>

                                <button onclick="changeYear({{ $year - 1 }})" class="btn btn-outline-secondary btn-sm" {{ $year <= date('Y') - 5 ? 'disabled' : '' }}>
                                    <i class="fa-solid fa-chevron-left"></i>
                                </button>
                                <span style="font-weight: 600; min-width: 60px; text-align: center;">{{ $year }}</span>
                                <button onclick="changeYear({{ $year + 1 }})" class="btn btn-outline-secondary btn-sm" {{ $year >= date('Y') ? 'disabled' : '' }}>
                                    <i class="fa-solid fa-chevron-right"></i>
                                </button>
                                
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Summary Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="rekapTable">
                                <thead>
                                    <tr>
                                        <th style="width: 25%;">Bulan</th>
                                        <th style="width: 25%; text-align: center;">Jumlah Pengunjung</th>
                                        <th style="width: 50%; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($monthlyData as $month => $count)
                                        <tr id="month-{{ $month }}" class="{{ $selectedMonth == $month ? 'table-active' : '' }}">
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    <span style="font-weight: 600; color: #1f2937;">
                                                        {{ \Carbon\Carbon::createFromDate(null, $month, 1)->format('F') }}
                                                    </span>
                                                    @if($selectedMonth == $month)
                                                        <span class="badge bg-primary">Aktif</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td style="text-align: center;">
                                                <span class="badge {{ $count > 0 ? 'bg-success' : 'bg-secondary' }} fs-6">
                                                    {{ $count }}
                                                </span>
                                                
                                            </td>
                                            <td style="text-align: center;">
                                                <div class="btn-group" role="group">
                                                    <button onclick="viewMonthDetails('{{ $month }}')" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                        <i class="fa-solid fa-eye"></i>
                                                        <span style="margin-left: 4px;">Detail</span>
                                                    </button>
                                                    <button onclick="window.location.href='{{ route('admin.export.month.excel', ['year' => $year, 'month' => $month]) }}'" style="padding: 6px 12px; background: white; color: black; border: 1px solid #d1d5db; border-radius: 6px; font-weight: 600; cursor: pointer; display: flex; gap: 6px; align-items: center; font-size: 12px;" title="Export Excel">
                                                        <i class="fa-solid fa-file-excel"></i>
                                                        <span>Excel</span>
                                                    </button>
                                                    <button onclick="window.location.href='{{ route('admin.export.month.pdf', ['year' => $year, 'month' => $month]) }}'" style="padding: 6px 12px; background: white; color: black; border: 1px solid #d1d5db; border-radius: 6px; font-weight: 600; cursor: pointer; display: flex; gap: 6px; align-items: center; font-size: 12px;" title="Export PDF">
                                                        <i class="fa-solid fa-file-pdf"></i>
                                                        <span>PDF</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        @if($selectedMonth == $month && $pengunjungBulan)
                                        <tr>
                                            <td colspan="3" class="p-0" style="border-top: none;">
                                                <div class="card border-0 rounded-0 shadow-none m-0 bg-light">
                                                    <div class="card-header bg-secondary text-white" style="border-radius: 0;">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="card-title mb-0 text-white">
                                                                <i class="fa-solid fa-list me-2"></i>Detail Pengunjung {{ \Carbon\Carbon::createFromDate(null, $selectedMonth, 1)->format('F') }} {{ $year }}
                                                            </div>
                                                            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <div id="bulk-actions-{{ $selectedMonth }}" class="mb-3 d-none">
                                                                <button type="button" class="btn btn-sm btn-danger" onclick="bulkDelete({{ $selectedMonth }})">
                                                                    <i class="fa-solid fa-trash-can me-1"></i> Hapus Terpilih (<span id="selected-count-{{ $selectedMonth }}">0</span>)
                                                                </button>
                                                            </div>
                                                            <table class="table table-bordered table-hover" style="font-size: 0.9rem;">
                                                                <thead class="table-dark">
                                                                    <tr>
                                                                        <th style="width: 40px; text-align: center;">
                                                                            <input type="checkbox" class="form-check-input" id="check-all-{{ $selectedMonth }}" onclick="toggleAll({{ $selectedMonth }}, this)">
                                                                        </th>
                                                                        <th style="width: 50px; text-align: center;">No</th>
                                                                        <th style="width: 100px; text-align: center;">Tanggal</th>
                                                                        <th style="width: 70px; text-align: center;">Foto</th>
                                                                        <th>Nama / Kontak</th>
                                                                        <th>Instansi / Pendidikan</th>
                                                                        <th>Yang Ditemui</th>
                                                                        <th style="width: 100px;">Usia / Gender</th>
                                                                        <th style="width: 200px;">Keperluan</th>
                                                                        @foreach($customFields as $cf)
                                                                            <th>{{ $cf->label }}</th>
                                                                        @endforeach
                                                                        <th style="width: 80px; text-align: center;">Aksi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse($pengunjungBulan as $index => $pengunjung)
                                                                        <tr id="row-{{ $pengunjung->id }}">
                                                                            <td style="text-align: center;">
                                                                                <input type="checkbox" class="form-check-input row-checkbox-{{ $selectedMonth }}" value="{{ $pengunjung->id }}" onclick="updateBulkActions({{ $selectedMonth }})">
                                                                            </td>
                                                                            <td style="text-align: center;">{{ $pengunjungBulan->firstItem() + $index }}</td>
                                                                            <td style="text-align: center;">
                                                                                <span class="badge bg-light text-dark border">{{ \Carbon\Carbon::parse($pengunjung->tanggal_kunjungan)->format('d/m/Y') }}</span>
                                                                            </td>
                                                                            <td style="text-align: center;">
                                                                                @if($pengunjung->selfie_photo)
                                                                                    <a href="{{ asset('storage/selfies/' . $pengunjung->selfie_photo) }}" target="_blank">
                                                                                        <img src="{{ asset('storage/selfies/' . $pengunjung->selfie_photo) }}" alt="Foto" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px; border: 1px solid #ddd;">
                                                                                    </a>
                                                                                @else
                                                                                    <span class="text-muted" style="font-size: 0.8rem;">-</span>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                <div style="font-weight: 600; color: #1f2937;">
                                                                                    {{ $pengunjung->nama }}
                                                                                </div>
                                                                                @if($pengunjung->no_hp)
                                                                                    <div><small class="text-muted"><i class="fa-solid fa-phone me-1"></i> {{ $pengunjung->no_hp }}</small></div>
                                                                                @endif
                                                                                @if($pengunjung->email)
                                                                                    <div><small class="text-muted"><i class="fa-solid fa-envelope me-1"></i> {{ $pengunjung->email }}</small></div>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if($pengunjung->instansi)
                                                                                    <div style="font-weight: 500;">{{ $pengunjung->instansi }}</div>
                                                                                @endif
                                                                                
                                                                                @php
                                                                                    $pendidikanText = $pengunjung->pendidikan == 'Lainnya' 
                                                                                        ? $pengunjung->pendidikan_lainnya 
                                                                                        : $pengunjung->pendidikan;
                                                                                @endphp
                                                                                
                                                                                @if($pendidikanText)
                                                                                    <small class="text-muted">
                                                                                        <i class="fas fa-graduation-cap me-1"></i> {{ $pendidikanText }}
                                                                                    </small>
                                                                                @elseif(!$pengunjung->instansi)
                                                                                    <span class="text-muted">-</span>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if($pengunjung->yang_ditemui)
                                                                                    {{ $pengunjung->yang_ditemui }}
                                                                                @else
                                                                                    <span class="text-muted">-</span>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                <div style="margin-bottom: 2px;">
                                                                                    @if($pengunjung->usia)
                                                                                        <span class="badge bg-secondary">{{ $pengunjung->usia }} th</span>
                                                                                    @else
                                                                                        <span class="text-muted">-</span>
                                                                                    @endif
                                                                                </div>
                                                                                <div>
                                                                                    @if($pengunjung->gender)
                                                                                        <span class="badge {{ $pengunjung->gender == 'L' ? 'bg-primary' : 'bg-pink' }}" style="{{ $pengunjung->gender == 'P' ? 'background-color: #ffc0cb; color: #000;' : '' }}">
                                                                                            {{ $pengunjung->gender == 'L' ? 'L' : 'P' }}
                                                                                        </span>
                                                                                    @else
                                                                                        <span class="text-muted">-</span>
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                @php
                                                                                    if ((int)$pengunjung->keperluan_kategori === 8) {
                                                                                        $keperluanText = $pengunjung->keperluan_lainnya ?? 'Lainnya';
                                                                                    } else {
                                                                                        $keperluanText = $labelKeperluan[$pengunjung->keperluan_kategori] ?? 'Lainnya';
                                                                                    }
                                                                                @endphp
                                                                                <small>{{ $keperluanText }}</small>
                                                                            </td>
                                                                            @foreach($customFields as $cf)
                                                                                <td>
                                                                                    @php
                                                                                        $val = $pengunjung->metadata[$cf->name] ?? '-';
                                                                                    @endphp
                                                                                    {{ is_array($val) ? implode(', ', $val) : $val }}
                                                                                </td>
                                                                            @endforeach
                                                                            <td style="text-align: center; white-space:nowrap;">
                                                                                <div class="btn-group" role="group">
                                                                                    <a href="{{ route('admin.pengunjung.edit', ['pengunjung' => $pengunjung->id, 'year' => $year, 'month' => $selectedMonth]) }}" class="btn btn-sm btn-outline-warning" title="Edit Data">
                                                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                                                    </a>
                                                                                    <form action="{{ route('admin.pengunjung.destroy', ['pengunjung' => $pengunjung->id, 'year' => $year, 'month' => $selectedMonth]) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus data tamu ini?');">
                                                                                        @csrf
                                                                                        @method('DELETE')
                                                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Data">
                                                                                            <i class="fa-solid fa-trash-can"></i>
                                                                                        </button>
                                                                                    </form>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="7" class="text-center">Tidak ada data detail untuk bulan ini.</td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="mt-3">
                                                            {{ $pengunjungBulan->appends(['year' => $year, 'month' => $selectedMonth])->links('vendor.pagination.bootstrap-4') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endif

                                    @endforeach
                                </tbody>
                                <tfoot style="border-top: 3px solid rgba(0,0,0,0.1);">
                                    <tr style="font-weight: bold;">
                                        <td>Total {{ $year }}</td>
                                        <td style="text-align: center;">
                                            <span class="badge bg-primary fs-5">{{ $totalSetahun }}</span>
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="text-muted">Rata-rata: {{ $totalSetahun > 0 ? round($totalSetahun / 12, 1) : 0 }}/bulan</span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </main>


    </div>

    <!-- Import Excel Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">
                        <i class="fa-solid fa-file-import me-2"></i>Import Data Pengunjung
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.import.excel') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateImportForm()">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            <strong>📋 Format Excel Singkat:</strong><br>
                            • Header: No, Tanggal, Waktu, Nama, Usia, Gender, No. HP, Email, Instansi, Pendidikan, Yang Ditemui, Keperluan, Foto Selfie<br>
                            • Smart mapping: posisi kolom otomatis, bisa dari file export<br>
                            • Tanggal mengikuti Excel (dd/mm/yyyy, dd-mm-yyyy, dll)<br>
                            • Gender: L/P, Laki-laki/Perempuan, Male/Female → disimpan L/P<br>
                            • Keperluan: teks atau angka 1–8 → otomatis dipetakan<br>
                            • Wajib: Nama dan Tanggal Kunjungan
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="import_year" class="form-label">
                                        <i class="fa-solid fa-calendar me-2"></i>Pilih Tahun
                                    </label>
                                    <select class="form-select" id="import_year" name="import_year" required>
                                        @php
                                            $currentYear = date('Y');
                                            $startYear = $currentYear - 5;
                                            $endYear = $currentYear + 2;
                                        @endphp
                                        @for($y = $endYear; $y >= $startYear; $y--)
                                            <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                                                {{ $y }} {{ $y == $currentYear ? '(Tahun Ini)' : '' }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="import_month" class="form-label">
                                        <i class="fa-solid fa-calendar-days me-2"></i>Pilih Bulan
                                    </label>
                                    <select class="form-select" id="import_month" name="import_month" required>
                                        @php
                                            $namaBulan = [
                                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                            ];
                                        @endphp
                                        @foreach($namaBulan as $bulan => $nama)
                                            <option value="{{ $bulan }}" {{ $selectedMonth == $bulan ? 'selected' : '' }}>
                                                {{ $nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Info singkat -->
                        <div class="mb-2">
                            <small class="text-muted">Data akan dimasukkan ke Tahun/Bulan yang dipilih.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="excel_file" class="form-label">
                                <i class="fa-solid fa-file-excel me-2"></i>Pilih File Excel
                            </label>
                            <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xlsx,.xls" required>
                            <div class="form-text">Hanya file Excel (.xlsx, .xls) yang diperbolehkan. File akan disimpan di folder storage/imports/[tahun]/[bulan]/</div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="update_existing" name="update_existing" value="1">
                                <label class="form-check-label" for="update_existing">
                                    <i class="fa-solid fa-sync me-2"></i>Update data yang sudah ada (jika ada duplikat)
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="backup_file" name="backup_file" value="1" checked>
                                <label class="form-check-label" for="backup_file">
                                    <i class="fa-solid fa-save me-2"></i>Simpan backup file Excel di storage
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success" id="importBtn">
                            <i class="fa-solid fa-upload me-2"></i>Import Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Data Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fa-solid fa-exclamation-triangle me-2"></i>Konfirmasi Hapus Data
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.delete.data') }}" method="POST" onsubmit="return validateDeleteForm()">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                            <strong>PERINGATAN!</strong> Tindakan ini akan menghapus data pengunjung secara permanen dan tidak dapat dibatalkan.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="delete_year" class="form-label">
                                        <i class="fa-solid fa-calendar me-2"></i>Tahun
                                    </label>
                                    <select class="form-select" id="delete_year" name="delete_year" required>
                                        @php
                                            $currentYear = date('Y');
                                            $startYear = $currentYear - 5;
                                            $endYear = $currentYear;
                                        @endphp
                                        @for($y = $endYear; $y >= $startYear; $y--)
                                            <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                                                {{ $y }} {{ $y == $currentYear ? '(Tahun Ini)' : '' }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="delete_month" class="form-label">
                                        <i class="fa-solid fa-calendar-days me-2"></i>Bulan
                                    </label>
                                    <select class="form-select" id="delete_month" name="delete_month">
                                        <option value="">Semua Bulan</option>
                                        @php
                                            $namaBulan = [
                                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                            ];
                                        @endphp
                                        @foreach($namaBulan as $bulan => $nama)
                                            <option value="{{ $bulan }}" {{ $selectedMonth == $bulan ? 'selected' : '' }}>
                                                {{ $nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="confirm_delete" name="confirm_delete" value="1" required>
                                <label class="form-check-label" for="confirm_delete">
                                    <strong>Saya setuju</strong> untuk menghapus data pengunjung pada periode yang dipilih
                                </label>
                            </div>
                        </div>

                        <div class="info-box">
                            <h4><i class="fa-solid fa-recycle"></i> Sampah & Pemulihan</h4>
                            <ul>
                                <li>Data yang dihapus dipindahkan ke halaman Pengaturan → Sampah</li>
                                <li>Data dapat dipulihkan dalam waktu 30 hari</li>
                                <li>Lewat 30 hari akan dihapus permanen otomatis</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger" id="deleteBtn">
                            <i class="fa-solid fa-trash me-2"></i>Pindahkan ke Sampah
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Event listeners lama dihapus karena sekarang impor selalu pilih Tahun & Bulan

        function changeYear(year) {
            window.location.href = "{{ route('admin.rekap-pengunjung') }}?year=" + year;
        }

        function viewMonthDetails(month) {
            let year = {{ $year }};
            let currentUrl = new URL(window.location.href);
            let currentMonth = currentUrl.searchParams.get('month');
            
            // Jika bulan yang sama sudah aktif, tutup detailnya
            if (currentMonth == month) {
                window.location.href = "{{ route('admin.rekap-pengunjung') }}?year=" + year;
            } else {
                // Jika bulan berbeda atau tidak ada yang aktif, buka detail
                window.location.href = "{{ route('admin.rekap-pengunjung') }}?year=" + year + "&month=" + month;
            }
        }

        

        function showImportModal() {
            var modal = new bootstrap.Modal(document.getElementById('importModal'));
            modal.show();
        }

        function showDeleteModal() {
            var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        // Bulk Actions
        function toggleAll(month, checkbox) {
            const checkboxes = document.querySelectorAll('.row-checkbox-' + month);
            checkboxes.forEach(cb => cb.checked = checkbox.checked);
            updateBulkActions(month);
        }

        function updateBulkActions(month) {
            const checkboxes = document.querySelectorAll('.row-checkbox-' + month + ':checked');
            const bulkActions = document.getElementById('bulk-actions-' + month);
            const selectedCount = document.getElementById('selected-count-' + month);
            const checkAll = document.getElementById('check-all-' + month);
            const allCheckboxes = document.querySelectorAll('.row-checkbox-' + month);

            if (checkboxes.length > 0) {
                bulkActions.classList.remove('d-none');
                selectedCount.innerText = checkboxes.length;
            } else {
                bulkActions.classList.add('d-none');
            }

            // Update check all state
            if (checkAll) {
                checkAll.checked = checkboxes.length === allCheckboxes.length && allCheckboxes.length > 0;
            }
        }

        async function bulkDelete(month) {
            const checkboxes = document.querySelectorAll('.row-checkbox-' + month + ':checked');
            const ids = Array.from(checkboxes).map(cb => cb.value);

            if (ids.length === 0) return;

            if (!confirm(`Yakin ingin menghapus ${ids.length} data tamu terpilih?`)) {
                return;
            }

            try {
                const response = await fetch("{{ route('admin.pengunjung.bulk-delete') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-HTTP-Method-Override': 'DELETE'
                    },
                    body: JSON.stringify({ ids: ids })
                });

                const result = await response.json();

                if (result.success) {
                    // Remove rows from table
                    ids.forEach(id => {
                        const row = document.getElementById('row-' + id);
                        if (row) row.remove();
                    });
                    
                    // Update count and hide bulk actions
                    updateBulkActions(month);
                    
                    alert(result.message);
                } else {
                    alert('Gagal menghapus data: ' + (result.message || 'Terjadi kesalahan'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghapus data.');
            }
        }

        function validateImportForm() {
            const fileInput = document.getElementById('excel_file');
            const file = fileInput.files[0];
            
            if (!file) {
                alert('Silakan pilih file Excel terlebih dahulu.');
                return false;
            }
            
            const fileName = file.name.toLowerCase();
            if (!fileName.endsWith('.xlsx') && !fileName.endsWith('.xls')) {
                alert('Hanya file Excel (.xlsx, .xls) yang diperbolehkan.');
                return false;
            }
            
            // Show loading state
            const importBtn = document.getElementById('importBtn');
            const originalText = importBtn.innerHTML;
            importBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Mengimport...';
            importBtn.disabled = true;
            
            // Re-enable after 5 seconds (fallback)
            setTimeout(() => {
                importBtn.innerHTML = originalText;
                importBtn.disabled = false;
            }, 5000);
            
            return true;
        }

                        function validateDeleteForm() {
            const confirmCheckbox = document.getElementById('confirm_delete');
            const deleteBtn = document.getElementById('deleteBtn');
                            const monthSelect = document.getElementById('delete_month');
                            // Izinkan "Semua Bulan" dengan value kosong
                            if (monthSelect && monthSelect.value === '') {
                                monthSelect.removeAttribute('required');
                            }
            
            if (!confirmCheckbox.checked) {
                alert('Anda harus mencentang kotak konfirmasi untuk melanjutkan penghapusan data.');
                return false;
            }
            
            // Show loading state
            const originalText = deleteBtn.innerHTML;
            deleteBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Menghapus...';
            deleteBtn.disabled = true;
            
            // Re-enable after 5 seconds (fallback)
            setTimeout(() => {
                deleteBtn.innerHTML = originalText;
                deleteBtn.disabled = false;
            }, 5000);
            
            return true;
        }


    </script>
</body>
</html>
