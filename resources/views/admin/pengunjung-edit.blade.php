<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Pengunjung</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('css/admin-pengunjung-edit.css') }}" rel="stylesheet">
</head>
<body>
    <button id="themeToggle" class="theme-toggle" title="Toggle tema">☾</button>
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Edit Data Pengunjung</div>
                <div class="card-subtitle">Perbarui data pengunjung yang sudah mengisi buku tamu.</div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.pengunjung.update', $pengunjung) }}">
            @csrf
            @method('PUT')

            <input type="hidden" name="redirect_year" value="{{ $year }}">
            <input type="hidden" name="redirect_month" value="{{ (int) ($month ?? 0) }}">

            <div class="field-full">
                <label>Nama Lengkap *</label>
                <input type="text" name="nama" value="{{ old('nama', $pengunjung->nama) }}" required>
            </div>

            <div>
                <label>Usia</label>
                <input type="number" name="usia" min="0" max="100" value="{{ old('usia', $pengunjung->usia) }}" required>
            </div>

            <div>
                <label>Jenis Kelamin *</label>
                <select name="gender" required>
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    <option value="L" {{ old('gender', $pengunjung->gender) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('gender', $pengunjung->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>

            <div>
                <label>No. Handphone *</label>
                <input type="tel" name="no_hp" value="{{ old('no_hp', $pengunjung->no_hp) }}" required>
            </div>

            <div>
                <div style="font-size: 14px; color: #6b7280; margin-bottom: 6px; text-align: center;">Opsional</div>
                <label>E-mail</label>
                <input type="email" name="email" value="{{ old('email', $pengunjung->email) }}">
            </div>

            <div>
                <label>Instansi</label>
                <input type="text" name="instansi" value="{{ old('instansi', $pengunjung->instansi) }}" required>
            </div>

            <div>
                <label>Pendidikan/Pekerjaan</label>
                <select name="pendidikan" required>
                    <option value="">-- Pilih Pendidikan/Pekerjaan --</option>
                    <option value="SD" {{ old('pendidikan', $pengunjung->pendidikan) == 'SD' ? 'selected' : '' }}>SD/Sederajat</option>
                    <option value="SMP" {{ old('pendidikan', $pengunjung->pendidikan) == 'SMP' ? 'selected' : '' }}>SMP/Sederajat</option>
                    <option value="SMA" {{ old('pendidikan', $pengunjung->pendidikan) == 'SMA' ? 'selected' : '' }}>SMA/Sederajat</option>
                    <option value="D1" {{ old('pendidikan', $pengunjung->pendidikan) == 'D1' ? 'selected' : '' }}>D1</option>
                    <option value="D2" {{ old('pendidikan', $pengunjung->pendidikan) == 'D2' ? 'selected' : '' }}>D2</option>
                    <option value="D3" {{ old('pendidikan', $pengunjung->pendidikan) == 'D3' ? 'selected' : '' }}>D3</option>
                    <option value="S1" {{ old('pendidikan', $pengunjung->pendidikan) == 'S1' ? 'selected' : '' }}>S1/D4</option>
                    <option value="S2" {{ old('pendidikan', $pengunjung->pendidikan) == 'S2' ? 'selected' : '' }}>S2</option>
                    <option value="S3" {{ old('pendidikan', $pengunjung->pendidikan) == 'S3' ? 'selected' : '' }}>S3</option>
                    <option value="Pelajar" {{ old('pendidikan', $pengunjung->pendidikan) == 'Pelajar' ? 'selected' : '' }}>Pelajar</option>
                    <option value="Mahasiswa" {{ old('pendidikan', $pengunjung->pendidikan) == 'Mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    <option value="PNS" {{ old('pendidikan', $pengunjung->pendidikan) == 'PNS' ? 'selected' : '' }}>PNS</option>
                    <option value="Swasta" {{ old('pendidikan', $pengunjung->pendidikan) == 'Swasta' ? 'selected' : '' }}>Swasta</option>
                    <option value="Wiraswasta" {{ old('pendidikan', $pengunjung->pendidikan) == 'Wiraswasta' ? 'selected' : '' }}>Wiraswasta</option>
                    <option value="Lainnya" {{ old('pendidikan', $pengunjung->pendidikan) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <div>
                <label>Yang Ditemui</label>
                <input type="text" name="yang_ditemui" value="{{ old('yang_ditemui', $pengunjung->yang_ditemui) }}" required>
            </div>

            <div class="field-full" id="pendidikan_lainnya_group" style="display: none;">
                <label>Jelaskan Pendidikan/Pekerjaan Lainnya</label>
                <input type="text" name="pendidikan_lainnya" id="pendidikan_lainnya" value="{{ old('pendidikan_lainnya', $pengunjung->pendidikan_lainnya ?? '') }}" placeholder="Jelaskan pendidikan/pekerjaan Anda">
            </div>

            <div class="field-full">
                <label>Keperluan *</label>
                <select name="keperluan_kategori" id="keperluan_kategori" required>
                    <option value="">-- Pilih Keperluan --</option>
                    <option value="1" @selected((int) old('keperluan_kategori', $pengunjung->keperluan_kategori) === 1)>Layanan Pengelolaan Hasil (Paten, PVT, Cipta, Merek)</option>
                    <option value="2" @selected((int) old('keperluan_kategori', $pengunjung->keperluan_kategori) === 2)>Layanan Pemanfaatan Hasil (Kerja sama, Lisensi, Mediasi, Konsultasi)</option>
                    <option value="3" @selected((int) old('keperluan_kategori', $pengunjung->keperluan_kategori) === 3)>Layanan Perpustakaan</option>
                    <option value="4" @selected((int) old('keperluan_kategori', $pengunjung->keperluan_kategori) === 4)>Layanan Magang</option>
                    <option value="5" @selected((int) old('keperluan_kategori', $pengunjung->keperluan_kategori) === 5)>Layanan Informasi dan Dokumentasi</option>
                    <option value="6" @selected((int) old('keperluan_kategori', $pengunjung->keperluan_kategori) === 6)>Layanan Publikasi Warta</option>
                    <option value="7" @selected((int) old('keperluan_kategori', $pengunjung->keperluan_kategori) === 7)>Rapat/Pertemuan</option>
                    <option value="8" @selected((int) old('keperluan_kategori', $pengunjung->keperluan_kategori) === 8)>Lainnya</option>
                </select>
                <div class="hint">Pilih jenis layanan yang sesuai dengan kebutuhan Anda.</div>
            </div>

            <div class="field-full" id="keperluan_lainnya_group" style="display: none;">
                <label>Jelaskan Keperluan Lainnya</label>
                <input type="text" name="keperluan_lainnya" value="{{ old('keperluan_lainnya', $pengunjung->keperluan_lainnya ?? '') }}" placeholder="Jelaskan keperluan Anda">
            </div>

            <div class="actions">
                <a class="btn-secondary" href="{{ route('admin.rekap-pengunjung', array_filter(['year' => $year, 'month' => $month])) }}">Kembali</a>
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const keperluanSelect = document.getElementById('keperluan_kategori');
            const keperluanLainnyaGroup = document.getElementById('keperluan_lainnya_group');
            const pendidikanSelect = document.querySelector('select[name="pendidikan"]');
            const pendidikanLainnyaGroup = document.getElementById('pendidikan_lainnya_group');
            const themeToggle = document.getElementById('themeToggle');
            
            function toggleLainnyaFields() {
                // Toggle keperluan lainnya
                if (keperluanSelect.value === '8') {
                    keperluanLainnyaGroup.style.display = 'block';
                    const kl = keperluanLainnyaGroup.querySelector('input[name="keperluan_lainnya"]');
                    if (kl) kl.required = true;
                } else {
                    keperluanLainnyaGroup.style.display = 'none';
                    const kl = keperluanLainnyaGroup.querySelector('input[name="keperluan_lainnya"]');
                    if (kl) kl.required = false;
                }
                
                // Toggle pendidikan lainnya
                if (pendidikanSelect && pendidikanSelect.value === 'Lainnya') {
                    pendidikanLainnyaGroup.style.display = 'block';
                    const pl = document.getElementById('pendidikan_lainnya');
                    if (pl) pl.required = true;
                } else {
                    pendidikanLainnyaGroup.style.display = 'none';
                    const pl = document.getElementById('pendidikan_lainnya');
                    if (pl) pl.required = false;
                }
            }
            
            function applyTheme(theme) {
                document.body.classList.remove('light-mode', 'dark-mode');
                if (theme === 'dark') {
                    document.body.classList.add('dark-mode');
                    themeToggle.textContent = '☀';
                } else {
                    document.body.classList.add('light-mode');
                    themeToggle.textContent = '☾';
                }
                localStorage.setItem('theme', theme);
            }
            
            const savedTheme = localStorage.getItem('theme') || 'light';
            applyTheme(savedTheme);
            
            themeToggle.addEventListener('click', function() {
                const current = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
                const next = current === 'dark' ? 'light' : 'dark';
                applyTheme(next);
            });
            
            // Check on page load
            toggleLainnyaFields();
            
            // Check on change
            if (keperluanSelect) {
                keperluanSelect.addEventListener('change', toggleLainnyaFields);
            }
            
            if (pendidikanSelect) {
                pendidikanSelect.addEventListener('change', toggleLainnyaFields);
            }
        });
    </script>
</body>
</html>
