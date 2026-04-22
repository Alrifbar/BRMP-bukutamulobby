<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapan Pengunjung BPHPMP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 15px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #22c55e;
            padding-bottom: 15px;
        }
        
        .header h1 {
            color: #16a34a;
            margin: 0;
            font-size: 18px;
        }
        
        .header h2 {
            color: #374151;
            margin: 3px 0 0 0;
            font-size: 14px;
        }
        
        .header p {
            color: #6b7280;
            margin: 3px 0 0 0;
            font-size: 12px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        th, td {
            border: 1px solid #d1d5db;
            padding: 6px;
            text-align: left;
            vertical-align: top;
            font-size: 9px;
        }
        
        th {
            background-color: #f3f4f6;
            font-weight: bold;
            color: #374151;
            font-size: 10px;
        }
        
        .no {
            text-align: center;
            font-weight: bold;
            width: 5%;
        }
        
        .tanggal, .waktu {
            text-align: center;
            white-space: nowrap;
            width: 10%;
        }
        
        .nama {
            font-weight: bold;
            min-width: 80px;
        }
        
        .keperluan {
            max-width: 150px;
            word-wrap: break-word;
        }
        
        .footer {
            margin-top: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 9px;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }
        
        @page {
            margin: 15px;
            size: landscape;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Buku Tamu Digital</h1>
        <h2>Balai Pengelola Hasil Perakitan dan Modernisasi Pertanian (BPHPMP)</h2>
        <p>Badan Perakitan dan Modernisasi Pertanian (BRMP)</p>
        <p><strong>Rekapan Pengunjung {{ $monthName ?? '' }} {{ $year }}</strong></p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th class="no">No</th>
                <th class="tanggal">Tanggal</th>
                <th class="waktu">Waktu</th>
                <th class="nama">Nama</th>
                <th>Usia</th>
                <th>No. HP</th>
                <th>Email</th>
                <th>Instansi</th>
                <th>Pendidikan</th>
                <th>Yang Ditemui</th>
                <th class="keperluan">Keperluan</th>
                @foreach($customFields as $cf)
                    <th>{{ $cf->label }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($pengunjung as $index => $p)
                <tr>
                    <td class="no">{{ $index + 1 }}</td>
                    <td class="tanggal">{{ \Carbon\Carbon::parse($p->tanggal_kunjungan)->format('d/m/Y') }}</td>
                    <td class="waktu">{{ \Carbon\Carbon::parse($p->tanggal_kunjungan)->format('H:i') }}</td>
                    <td class="nama">{{ $p->nama }}</td>
                    <td>{{ $p->usia ?? '-' }}</td>
                    <td>{{ $p->no_hp ?? '-' }}</td>
                    <td>{{ $p->email ?? '-' }}</td>
                    <td>{{ $p->instansi ?? '-' }}</td>
                    <td>{{ $p->pendidikan ?? '-' }}</td>
                    <td>{{ $p->yang_ditemui ?? '-' }}</td>
                    <td class="keperluan">
                        {{ $p->keperluan_label ?? $p->keperluan_kategori }}
                    </td>
                    @foreach($customFields as $cf)
                        <td>
                            @php
                                $val = $p->metadata[$cf->name] ?? '-';
                            @endphp
                            {{ is_array($val) ? implode(', ', $val) : $val }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>Dokumen ini dihasilkan oleh Buku Tamu Digital BPHPMP</p>
        <p>Tanggal cetak: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
        <p>Total pengunjung: {{ $pengunjung->count() }} orang</p>
    </div>
</body>
</html>
