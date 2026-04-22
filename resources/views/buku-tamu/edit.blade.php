<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Pengunjung</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            background: radial-gradient(circle at top left, #d9f7ea 0, #f3f6fb 45%, #e5f7ee 100%);
            color: #111827;
            padding: 32px 16px;
        }

        .card {
            width: 100%;
            max-width: 820px;
            background: #ffffff;
            border-radius: 28px;
            box-shadow: 0 28px 60px rgba(15, 23, 42, 0.18);
            padding: 26px 30px 30px;
            border: 1px solid rgba(34, 197, 94, 0.15);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 32px 68px rgba(15, 23, 42, 0.22);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
            padding-bottom: 14px;
            border-bottom: 1px solid #e5e7eb;
        }

        .card-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 4px;
            letter-spacing: 0.01em;
        }

        .card-subtitle {
            font-size: 13px;
            color: #6b7280;
        }

        .edit-info {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border: 1px solid #fbbf24;
            border-left: 4px solid #f59e0b;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #92400e;
        }

        .alert {
            padding: 10px 12px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 14px;
        }

        .alert-success {
            background: linear-gradient(135deg, #ecfdf3, #dcfce7);
            color: #15803d;
            border: 1px solid #bbf7d0;
            border-left: 4px solid #16a34a;
        }

        .alert-error {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            color: #b91c1c;
            border: 1px solid #fecaca;
            border-left: 4px solid #dc2626;
        }

        .alert-error ul {
            padding-left: 18px;
        }

        form {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px 20px;
            margin-top: 10px;
        }

        .field-full {
            grid-column: span 2;
        }

        label {
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            display: block;
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="number"],
        input[type="email"],
        select {
            width: 100%;
            border-radius: 12px;
            border: 1px solid #d1d5db;
            padding: 10px 12px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
            background-color: #f9fafb;
        }

        input:focus,
        select:focus {
            border-color: #22c55e;
            box-shadow: 0 0 0 1px #bbf7d0;
            background-color: #ffffff;
        }

        .hint {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 2px;
        }

        .actions {
            grid-column: span 2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 4px;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(90deg, #22c55e, #16a34a);
            border: none;
            color: #ffffff;
            font-size: 14px;
            font-weight: 500;
            padding: 10px 22px;
            border-radius: 999px;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(34, 197, 94, 0.4);
            transition: transform 0.1s ease, box-shadow 0.1s ease, filter 0.1s ease;
        }

        .btn-primary:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
            box-shadow: 0 14px 30px rgba(34, 197, 94, 0.45);
        }

        .btn-secondary {
            padding: 10px 18px;
            border-radius: 999px;
            border: 1px solid #d1d5db;
            background: #ffffff;
            color: #111827;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }

        @media (max-width: 640px) {
            form {
                grid-template-columns: 1fr;
            }

            .field-full,
            .actions {
                grid-column: span 1;
            }

            .actions {
                flex-direction: column-reverse;
                align-items: stretch;
            }

            .btn-primary,
            .btn-secondary {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Edit Data Pengunjung</div>
                <div class="card-subtitle">Perbarui data Anda yang sudah terdaftar.</div>
            </div>
        </div>

        <div class="edit-info">
            <strong>Informasi Edit:</strong> Anda dapat mengedit data ini sebanyak {{ $pengunjung->edit_attempts }} kali lagi. Simpan link ini dengan baik untuk edit di kemudian hari.
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

        <form method="POST" action="{{ route('buku-tamu.update', $pengunjung->unique_token) }}">
            @csrf
            @method('PUT')

            <div class="field-full">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" value="{{ old('nama', $pengunjung->nama) }}" required>
            </div>

            <div>
                <label>Usia</label>
                <input type="number" name="usia" min="1" max="100" list="ageList" value="{{ old('usia', $pengunjung->usia) }}" required>
                <datalist id="ageList">
                    @for ($i = 1; $i <= 100; $i++)
                        <option value="{{ $i }}">{{ $i }} tahun</option>
                    @endfor
                </datalist>
            </div>

            <div>
                <label>Jenis Kelamin</label>
                <select name="gender" required>
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    <option value="L" @selected(old('gender', $pengunjung->gender) == 'L')>Laki-laki</option>
                    <option value="P" @selected(old('gender', $pengunjung->gender) == 'P')>Perempuan</option>
                </select>
            </div>

            <div>
                <label>No. Handphone</label>
                <input type="text" name="no_hp" value="{{ old('no_hp', $pengunjung->no_hp) }}" required>
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
                <select name="pendidikan" id="pendidikan" required>
                    <option value="">-- Pilih Pendidikan --</option>
                    <option value="SD" @selected(old('pendidikan', $pengunjung->pendidikan) == 'SD')">SD</option>
                    <option value="SMP" @selected(old('pendidikan', $pengunjung->pendidikan) == 'SMP')">SMP</option>
                    <option value="SMA" @selected(old('pendidikan', $pengunjung->pendidikan) == 'SMA')">SMA</option>
                    <option value="SMK" @selected(old('pendidikan', $pengunjung->pendidikan) == 'SMK')">SMK</option>
                    <option value="D1" @selected(old('pendidikan', $pengunjung->pendidikan) == 'D1')">D1</option>
                    <option value="D2" @selected(old('pendidikan', $pengunjung->pendidikan) == 'D2')">D2</option>
                    <option value="D3" @selected(old('pendidikan', $pengunjung->pendidikan) == 'D3')">D3</option>
                    <option value="D4" @selected(old('pendidikan', $pengunjung->pendidikan) == 'D4')">D4</option>
                    <option value="S1" @selected(old('pendidikan', $pengunjung->pendidikan) == 'S1')">S1</option>
                    <option value="S2" @selected(old('pendidikan', $pengunjung->pendidikan) == 'S2')">S2</option>
                    <option value="S3" @selected(old('pendidikan', $pengunjung->pendidikan) == 'S3')">S3</option>
                    <option value="Lainnya" @selected(old('pendidikan', $pengunjung->pendidikan) == 'Lainnya' || old('pendidikan_lainnya', $pengunjung->pendidikan_lainnya))>Lainnya</option>
                </select>
            </div>

            <div id="pendidikan_lainnya_field" style="display: none;">
                <label>Pendidikan/Pekerjaan Lainnya</label>
                <input type="text" name="pendidikan_lainnya" id="pendidikan_lainnya" value="{{ old('pendidikan_lainnya', $pengunjung->pendidikan_lainnya ?? '') }}" placeholder="Tuliskan pendidikan/pekerjaan lainnya...">
            </div>

            <div class="field-full">
                <label>Yang Ditemui</label>
                <input type="text" name="yang_ditemui" value="{{ old('yang_ditemui', $pengunjung->yang_ditemui) }}" required>
            </div>

            <div class="field-full">
                <label>Keperluan</label>
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

            <div class="field-full" id="lainnya_field" style="display: none;">
                <label>Jika pilih "Lainnya", tuliskan</label>
                <input type="text" name="keperluan_lainnya" id="keperluan_lainnya" value="{{ old('keperluan_lainnya', $pengunjung->keperluan_lainnya ?? '') }}" placeholder="Tuliskan keperluan lainnya...">
            </div>

            <div class="actions">
                <a href="{{ route('buku-tamu.terima-kasih', $pengunjung->unique_token) }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const keperluanSelect = document.getElementById('keperluan_kategori');
            const lainnyaField = document.getElementById('lainnya_field');
            const pendidikanSelect = document.getElementById('pendidikan');
            const pendidikanLainnyaField = document.getElementById('pendidikan_lainnya_field');
            
            function toggleLainnyaField() {
                if (keperluanSelect.value === '8') {
                    lainnyaField.style.display = 'block';
                    document.getElementById('keperluan_lainnya').required = true;
                } else {
                    lainnyaField.style.display = 'none';
                    document.getElementById('keperluan_lainnya').required = false;
                }
            }

            function togglePendidikanLainnyaField() {
                if (pendidikanSelect.value === 'Lainnya') {
                    pendidikanLainnyaField.style.display = 'block';
                    document.getElementById('pendidikan_lainnya').required = true;
                } else {
                    pendidikanLainnyaField.style.display = 'none';
                    document.getElementById('pendidikan_lainnya').required = false;
                }
            }
            
            // Check on page load
            toggleLainnyaField();
            togglePendidikanLainnyaField();
            
            // Check on change
            keperluanSelect.addEventListener('change', toggleLainnyaField);
            pendidikanSelect.addEventListener('change', togglePendidikanLainnyaField);
        });
    </script>
</body>
</html>
