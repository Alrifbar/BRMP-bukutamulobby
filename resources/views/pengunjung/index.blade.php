<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Tamu Pengunjung</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        body {
            background: #f3f6fb;
            min-height: 100vh;
            color: #1b1b18;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #21c16b, #0a9b4e);
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.1);
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .actions {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 12px;
            border: none;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #21c16b, #0a9b4e);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(33, 193, 107, 0.3);
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: white;
            padding: 10px 15px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
            border: 1px solid #e2e8f0;
        }

        .search-box input {
            border: none;
            outline: none;
            font-size: 14px;
            width: 250px;
        }

        .card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
            border: 1px solid rgba(34, 197, 94, 0.1);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
            border-radius: 14px;
        }

        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #eef2f7;
            text-align: left;
            font-size: 14px;
        }

        tr:nth-child(even) td {
            background: #f8fafc;
        }

        tr:hover td {
            background: #f0f9ff;
        }

        th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #ffffff;
            background: linear-gradient(135deg, #16a34a, #22c55e);
            font-weight: 600;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 30px;
            gap: 10px;
        }

        .pagination a {
            padding: 8px 12px;
            border-radius: 8px;
            background: white;
            color: #374151;
            text-decoration: none;
            font-size: 14px;
            border: 1px solid #e5e7eb;
            transition: all 0.2s ease;
        }

        .pagination a:hover {
            background: #f3f4f6;
            border-color: #d1d5db;
        }

        .pagination .current {
            background: linear-gradient(135deg, #21c16b, #0a9b4e);
            color: white;
            border-color: #21c16b;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #6b7280;
            font-size: 16px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .header {
                padding: 20px;
            }

            .header h1 {
                font-size: 22px;
            }

            .actions {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                width: 100%;
            }

            .search-box input {
                width: 100%;
            }

            .card {
                padding: 15px;
            }

            th, td {
                padding: 8px 10px;
                font-size: 12px;
            }

            table {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Daftar Tamu Pengunjung</h1>
            <p>Buku tamu digital - Daftar pengunjung terkini</p>
        </div>

        <div class="actions">
            <a href="{{ route('buku-tamu.create') }}" class="btn btn-primary">
                ✍️ Isi Buku Tamu
            </a>
            
            <div class="search-box">
                <span>🔍</span>
                <input type="text" placeholder="Cari nama atau instansi..." id="searchInput">
            </div>
        </div>

        <div class="card">
            @if($pengunjung->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Usia</th>
                            <th>No. HP</th>
                            <th>Email</th>
                            <th>Instansi</th>
                            <th>Yang Ditemui</th>
                            <th>Keperluan</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pengunjung as $index => $item)
                            <tr>
                                <td>{{ ($pengunjung->currentPage() - 1) * $pengunjung->perPage() + $index + 1 }}</td>
                                <td><strong>{{ $item->nama }}</strong></td>
                                <td>{{ $item->usia ?? '-' }}</td>
                                <td>{{ $item->no_hp ?? '-' }}</td>
                                <td>{{ $item->email ?? '-' }}</td>
                                <td>{{ $item->instansi ?? '-' }}</td>
                                <td>{{ $item->yang_ditemui ?? '-' }}</td>
                                <td>
                                    @if ((int) $item->keperluan_kategori === 8)
                                        {{ $item->keperluan_lainnya ?? 'Lainnya' }}
                                    @else
                                        {{ $labelKeperluan[(int) $item->keperluan_kategori] ?? $item->keperluan_kategori }}
                                    @endif
                                </td>
                                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="pagination">
                    {{ $pengunjung->links() }}
                </div>
            @else
                <div class="no-data">
                    <p>📝 Belum ada data pengunjung.</p>
                    <p style="margin-top: 10px; font-size: 14px;">
                        <a href="{{ route('buku-tamu.create') }}" style="color: #21c16b; text-decoration: none; font-weight: 600;">
                            Isi buku tamu sekarang →
                        </a>
                    </p>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Simple search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
