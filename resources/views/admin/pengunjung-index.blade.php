<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pengunjung - Admin Buku Tamu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('admin.theme-styles')
</head>
<body>
    <div class="layout">
        <!-- Sidebar kiri -->
        @include('admin.partials.sidebar')

        <!-- Main content -->
        <main class="main-content">
            <header class="main-header">
                <div class="main-header-title">
                    <h1>Data Pengunjung</h1>
                    <p>Kelola dan lihat semua data pengunjung</p>
                    {{ now()->translatedFormat('d F Y, l') }}
                </div>
            </header>

            <section class="content-section">
                <!-- Filter Card -->
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-body">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                            <div>
                                <h5 class="card-title mb-0">Filter Data</h5>
                            </div>
                            <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                                <select onchange="changeYear(this.value)" class="form-select form-select-sm" style="width: auto;">
                                    @foreach($years as $y)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                                    @endforeach
                                </select>
                                
                                <select onchange="changeMonth(this.value)" class="form-select form-select-sm" style="width: auto;">
                                    <option value="0" {{ $month == 0 ? 'selected' : '' }}>Semua Bulan</option>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::createFromFormat('m', $m)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                                
                                <div class="btn-group" role="group">
                                    <button onclick="printTable()" class="btn btn-primary btn-sm">
                                        <i class="fa-solid fa-print"></i>
                                        <span style="margin-left: 4px;">Cetak</span>
                                    </button>
                                    <button onclick="deleteSelected()" class="btn btn-danger btn-sm" id="deleteSelectedBtn" style="display: none;">
                                        <i class="fa-solid fa-trash-can"></i>
                                        <span style="margin-left: 4px;">Hapus Terpilih</span>
                                    </button>
                                    <button onclick="exportSelected()" class="btn btn-success btn-sm" id="exportSelectedBtn" style="display: none;">
                                        <i class="fa-solid fa-download"></i>
                                        <span style="margin-left: 4px;">Export Terpilih</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="pengunjungTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 5%; text-align: center;">
                                            <input type="checkbox" id="selectAll" class="form-check-input" onchange="toggleSelectAll()">
                                        </th>
                                        <th style="width: 5%;">No</th>
                                        <th style="width: 20%;">Nama</th>
                                        <th style="width: 12%;">Usia</th>
                                        <th style="width: 8%;">Gender</th>
                                        <th style="width: 15%;">Instansi</th>
                                        <th style="width: 15%;">Keperluan</th>
                                        <th style="width: 12%;">Tanggal</th>
                                        <th style="width: 8%; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pengunjung as $index => $item)
                                        <tr>
                                            <td style="text-align: center;">
                                                <input type="checkbox" class="form-check-input item-checkbox" value="{{ $item->id }}" onchange="updateSelectedCount()">
                                            </td>
                                            <td>{{ ($pengunjung->currentPage() - 1) * $pengunjung->perPage() + $index + 1 }}</td>
                                            <td>
                                                <div style="font-weight: 600; color: #1f2937;">
                                                    {{ $item->nama }}
                                                </div>
                                                @if($item->no_hp)
                                                    <small class="text-muted">{{ $item->no_hp }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->usia)
                                                    <span class="badge bg-secondary">{{ $item->usia }} th</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->gender)
                                                    <span class="badge {{ $item->gender == 'L' ? 'bg-primary' : 'bg-pink' }}">
                                                        {{ $item->gender == 'L' ? 'L' : 'P' }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->instansi ?: '-' }}</td>
                                            <td>
                                                @php
                                                    $labelKeperluan = [
                                                        1 => 'Layanan Pengelolaan Hasil',
                                                        2 => 'Layanan Pemanfaatan Hasil', 
                                                        3 => 'Layanan Perpustakaan',
                                                        4 => 'Layanan Magang',
                                                        5 => 'Layanan Informasi',
                                                        6 => 'Layanan Publikasi',
                                                        7 => 'Rapat/Pertemuan',
                                                        8 => 'Lainnya'
                                                    ];
                                                    $keperluanText = $item->keperluan_kategori ? ($labelKeperluan[$item->keperluan_kategori] ?? 'Lainnya') : ($item->keperluan_lainnya ?: '-');
                                                @endphp
                                                <small>{{ $keperluanText }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info text-dark">
                                                    {{ \Carbon\Carbon::parse($item->tanggal_kunjungan)->format('d M Y') }}
                                                </span>
                                            </td>
                                            <td style="text-align: center;">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.pengunjung.edit', ['pengunjung' => $item->id, 'year' => $year, 'month' => $month]) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    <form action="{{ route('admin.pengunjung.destroy', ['pengunjung' => $item->id, 'year' => $year, 'month' => $month]) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus data tamu ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                            <i class="fa-solid fa-trash-can"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($pengunjung->hasPages())
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted">
                                    Menampilkan {{ $pengunjung->firstItem() }} sampai {{ $pengunjung->lastItem() }} dari {{ $pengunjung->total() }} data
                                </div>
                                {{ $pengunjung->appends(['year' => $year, 'month' => $month])->links() }}
                            </div>
                        @endif
                        
                        @if($pengunjung->isEmpty())
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Tidak ada data pengunjung</h5>
                                <p class="text-muted">Tidak ada data pengunjung untuk filter yang dipilih</p>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        </main>


    </div>

    <script>
        // Toggle select all checkboxes
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.item-checkbox');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            
            updateSelectedCount();
        }
        
        // Update selected count and show/hide bulk action buttons
        function updateSelectedCount() {
            const checkboxes = document.querySelectorAll('.item-checkbox:checked');
            const deleteBtn = document.getElementById('deleteSelectedBtn');
            const exportBtn = document.getElementById('exportSelectedBtn');
            const selectAll = document.getElementById('selectAll');
            const allCheckboxes = document.querySelectorAll('.item-checkbox');
            
            // Show/hide bulk action buttons
            if (checkboxes.length > 0) {
                deleteBtn.style.display = 'inline-block';
                exportBtn.style.display = 'inline-block';
            } else {
                deleteBtn.style.display = 'none';
                exportBtn.style.display = 'none';
            }
            
            // Update select all checkbox state
            if (allCheckboxes.length > 0) {
                if (checkboxes.length === allCheckboxes.length) {
                    selectAll.checked = true;
                    selectAll.indeterminate = false;
                } else if (checkboxes.length > 0) {
                    selectAll.checked = false;
                    selectAll.indeterminate = true;
                } else {
                    selectAll.checked = false;
                    selectAll.indeterminate = false;
                }
            }
        }
        
        // Delete selected items
        function deleteSelected() {
            const checkboxes = document.querySelectorAll('.item-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.value);
            
            if (ids.length === 0) {
                alert('Pilih minimal satu data untuk dihapus');
                return;
            }
            
            if (confirm(`Apakah Anda yakin ingin menghapus ${ids.length} data yang dipilih?`)) {
                // Create form for bulk delete
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.pengunjung.bulk-delete", ["year" => $year, "month" => $month]) }}';
                
                // Add CSRF token
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);
                
                // Add method override for DELETE
                const method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'DELETE';
                form.appendChild(method);
                
                // Add selected IDs
                ids.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    form.appendChild(input);
                });
                
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Export selected items
        function exportSelected() {
            const checkboxes = document.querySelectorAll('.item-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.value);
            
            if (ids.length === 0) {
                alert('Pilih minimal satu data untuk diexport');
                return;
            }
            
            // Create form for bulk export
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.pengunjung.bulk-export", ["year" => $year, "month" => $month]) }}';
            
            // Add CSRF token
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);
            
            // Add selected IDs
            ids.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                form.appendChild(input);
            });
            
            document.body.appendChild(form);
            form.submit();
        }

        function changeYear(year) {
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('year', year);
            window.location.href = currentUrl.toString();
        }

        function changeMonth(month) {
            const currentUrl = new URL(window.location);
            if (month == 0) {
                currentUrl.searchParams.delete('month');
            } else {
                currentUrl.searchParams.set('month', month);
            }
            window.location.href = currentUrl.toString();
        }

        function printTable() {
            const printContent = document.getElementById('pengunjungTable').outerHTML;
            const originalContent = document.body.innerHTML;
            
            document.body.innerHTML = `
                <div style="padding: 20px;">
                    <h1>Data Pengunjung</h1>
                    <p>Tahun: {{ $year }}, Bulan: {{ $month > 0 ? \Carbon\Carbon::createFromFormat('m', $month)->format('F') : 'Semua' }}</p>
                    ${printContent}
                </div>
            `;
            
            window.print();
            document.body.innerHTML = originalContent;
            location.reload();
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateSelectedCount();
        });
    </script>
</body>
</html>
