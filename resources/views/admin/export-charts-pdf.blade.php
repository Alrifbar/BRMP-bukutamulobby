<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body { 
            font-family: 'Arial', sans-serif; 
            margin: 20px; 
            background: #ffffff;
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px; 
            border-bottom: 3px solid #22c55e; 
            padding-bottom: 15px;
            background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
        }
        .header h1 { 
            margin: 0; 
            font-size: 24px; 
            color: #16a34a; 
            font-weight: bold;
        }
        .header p { 
            margin: 8px 0 0 0; 
            color: #6b7280; 
            font-size: 14px;
            font-weight: 500;
        }
        .chart-container { 
            margin-bottom: 40px; 
            page-break-inside: avoid;
            border: 2px solid #e5e7eb; 
            border-radius: 12px; 
            padding: 20px;
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .chart-title { 
            font-size: 18px; 
            font-weight: bold; 
            color: #1f2937; 
            margin-bottom: 15px;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 10px;
        }
        .chart-image { 
            width: 100%; 
            height: auto; 
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: #ffffff;
        }
        .footer { 
            margin-top: 30px; 
            text-align: center; 
            color: #6b7280; 
            font-size: 12px; 
            border-top: 2px solid #e5e7eb; 
            padding-top: 15px;
            background: #f9fafb;
        }
        @page { 
            margin: 20mm; 
            size: A4 landscape;
        }
        /* Ensure charts don't get cut */
        .chart-container img {
            max-width: 100%;
            height: auto !important;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN GRAFIK ANALITIK</h1>
        <p><strong>Periode:</strong> {{ $period }}</p>
    </div>
    
    @foreach($images as $label => $src)
        @if($src)
            <div class="chart-container">
                <div class="chart-title">{{ $label }}</div>
                <img class="chart-image" src="{{ $src }}" alt="{{ $label }}">
            </div>
        @endif
    @endforeach
    
    <div class="footer">
        <p><strong>Dicetak:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
        <p>Sistem Buku Tamu Digital - BRMP</p>
    </div>
</body>
</html>
