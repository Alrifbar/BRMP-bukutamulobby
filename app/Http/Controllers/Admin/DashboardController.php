<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengunjung;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function dashboard()
    {
        if (!session('admin_verified')) {
            return redirect()->route('admin.verify');
        }

        $totalPengunjung = Pengunjung::count();
        $bulanIni = Pengunjung::whereMonth('tanggal_kunjungan', now()->month)
            ->whereYear('tanggal_kunjungan', now()->year)
            ->count();
        $hariIni = Pengunjung::whereDate('tanggal_kunjungan', now()->toDateString())
            ->count();
        $mingguIni = Pengunjung::whereBetween('tanggal_kunjungan', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->count();

        // Data untuk grafik per bulan tahun ini (Optimized GROUP BY)
        $grafikData = Pengunjung::whereYear('tanggal_kunjungan', now()->year)
            ->selectRaw('MONTH(tanggal_kunjungan) as bulan, COUNT(*) as total')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')
            ->all();

        $grafikBulanan = [];
        for ($m = 1; $m <= 12; $m++) {
            $grafikBulanan[] = $grafikData[$m] ?? 0;
        }

        $tahunIni = Pengunjung::whereYear('tanggal_kunjungan', now()->year)->count();
        $targetBulanan = 100;
        $persentaseTarget = min(($bulanIni / $targetBulanan) * 100, 100);

        $dataLengkap = Pengunjung::whereNotNull('nama')
            ->whereNotNull('no_hp')
            ->whereNotNull('email')
            ->whereNotNull('instansi')
            ->count();
        $persentaseLengkap = $totalPengunjung > 0 ? ($dataLengkap / $totalPengunjung) * 100 : 0;

        $pengunjungTerbaru = Pengunjung::select('nama', 'instansi', 'keperluan_kategori', 'keperluan_lainnya', 'tanggal_kunjungan')
            ->orderBy('tanggal_kunjungan', 'desc')
            ->take(10)
            ->get();

        $layananPopuler = Pengunjung::selectRaw('keperluan_kategori, COUNT(*) as total')
            ->groupBy('keperluan_kategori')
            ->orderBy('total', 'desc')
            ->take(3)
            ->get()
            ->map(function($item) {
                return [
                    'nama' => $this->getLabelKeperluan($item->keperluan_kategori),
                    'total' => $item->total
                ];
            });

        return view('admin.dashboard', [
            'totalPengunjung' => $totalPengunjung,
            'bulanIni' => $bulanIni,
            'hariIni' => $hariIni,
            'mingguIni' => $mingguIni,
            'tahunIni' => $tahunIni,
            'grafikBulanan' => $grafikBulanan,
            'targetBulanan' => $targetBulanan,
            'persentaseTarget' => $persentaseTarget,
            'dataLengkap' => $dataLengkap,
            'persentaseLengkap' => $persentaseLengkap,
            'pengunjungTerbaru' => $pengunjungTerbaru,
            'layananPopuler' => $layananPopuler,
        ]);
    }

    public function grafik(Request $request)
    {
        set_time_limit(120);
        $currentYear = (int) now()->year;
        
        $minDbYear = Pengunjung::min('tanggal_kunjungan');
        $minDbYear = $minDbYear ? (int) Carbon::parse($minDbYear)->format('Y') : $currentYear;
        $minYear = min($minDbYear, $currentYear - 5);
        
        $maxDbYear = Pengunjung::max('tanggal_kunjungan');
        $maxDbYear = $maxDbYear ? (int) Carbon::parse($maxDbYear)->format('Y') : $currentYear;
        $maxYear = max($maxDbYear, $currentYear + 5);
        
        $availableYears = range($minYear, $maxYear);
        rsort($availableYears);

        $year = $request->input('year', $currentYear);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $isCustomFilter = !empty($startDate) || !empty($endDate);
        
        if ($isCustomFilter) {
            if ($startDate && !$endDate) $endDate = now()->format('Y-m-d');
            elseif (!$startDate && $endDate) $startDate = Carbon::parse($endDate)->startOfYear()->format('Y-m-d');
            if ($startDate > $endDate) { $temp = $startDate; $startDate = $endDate; $endDate = $temp; }
        } else {
            $startDate = Carbon::createFromDate($year, 1, 1)->startOfDay()->format('Y-m-d');
            $endDate = Carbon::createFromDate($year, 12, 31)->endOfDay()->format('Y-m-d');
        }
        
        $baseVisitors = Pengunjung::whereDate('tanggal_kunjungan', '>=', $startDate)
            ->whereDate('tanggal_kunjungan', '<=', $endDate)
            ->get();
        
        $trendData = []; $trendLabels = []; $trendType = 'monthly'; $trendTitle = 'Grafik Tren Bulanan';

        if ($isCustomFilter) {
            $diffInDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
            if ($diffInDays <= 62) {
                $trendType = 'daily'; $trendTitle = 'Grafik Tren Harian';
                $currDate = Carbon::parse($startDate); $endDateObj = Carbon::parse($endDate);
                $dailyCounts = $baseVisitors->groupBy(fn($item) => Carbon::parse($item->tanggal_kunjungan)->format('Y-m-d'))->map->count();
                while ($currDate <= $endDateObj) {
                    $dateStr = $currDate->format('Y-m-d'); $trendLabels[] = $currDate->format('d M');
                    $trendData[] = $dailyCounts[$dateStr] ?? 0; $currDate->addDay();
                }
            } elseif ($diffInDays <= 1095) {
                $trendType = 'monthly'; $trendTitle = 'Grafik Tren Bulanan';
                $currDate = Carbon::parse($startDate)->startOfMonth(); $endDateObj = Carbon::parse($endDate)->endOfMonth();
                $monthlyCounts = $baseVisitors->groupBy(fn($item) => Carbon::parse($item->tanggal_kunjungan)->format('Y-m'))->map->count();
                while ($currDate <= $endDateObj) {
                    $dateKey = $currDate->format('Y-m'); $trendLabels[] = $currDate->format('M Y');
                    $trendData[] = $monthlyCounts[$dateKey] ?? 0; $currDate->addMonth();
                }
            } else {
                $trendType = 'yearly'; $trendTitle = 'Grafik Tren Tahunan';
                $startYear = Carbon::parse($startDate)->year; $endYear = Carbon::parse($endDate)->year;
                $yearlyCounts = $baseVisitors->groupBy(fn($item) => Carbon::parse($item->tanggal_kunjungan)->format('Y'))->map->count();
                for ($y = $startYear; $y <= $endYear; $y++) {
                    $trendLabels[] = (string)$y; $trendData[] = $yearlyCounts[$y] ?? 0;
                }
            }
        } else {
            $trendType = 'monthly'; $trendTitle = 'Grafik Tren Bulanan (' . $year . ')';
            $trendLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            $yearVisitors = $baseVisitors->filter(fn($v) => Carbon::parse($v->tanggal_kunjungan)->year == $year);
            for ($m = 1; $m <= 12; $m++) {
                $trendData[] = $yearVisitors->filter(fn($v) => Carbon::parse($v->tanggal_kunjungan)->month == $m)->count();
            }
        }
        
        if (empty($trendData)) { $trendLabels = ['Tidak Ada Data']; $trendData = [0]; }

        // 2. Data Tahunan (5 Tahun Terakhir)
        $grafikTahunan = [];
        $labelsTahunan = [];
        for ($i = 4; $i >= 0; $i--) {
            $y = $currentYear - $i;
            $labelsTahunan[] = (string)$y;
            $grafikTahunan[] = Pengunjung::whereYear('tanggal_kunjungan', $y)->count();
        }

        // 3. Data Gender Line (Tren Gender)
        $genderLineLabels = $trendLabels;
        $genderLineDataL = [];
        $genderLineDataP = [];

        if ($trendType === 'daily') {
            $currDate = Carbon::parse($startDate);
            $endDateObj = Carbon::parse($endDate);
            $dailyL = $baseVisitors->filter(fn($v) => ($v->gender ?? $this->detectGender($v->nama)) === 'L')
                ->groupBy(fn($item) => Carbon::parse($item->tanggal_kunjungan)->format('Y-m-d'))->map->count();
            $dailyP = $baseVisitors->filter(fn($v) => ($v->gender ?? $this->detectGender($v->nama)) === 'P')
                ->groupBy(fn($item) => Carbon::parse($item->tanggal_kunjungan)->format('Y-m-d'))->map->count();
            while ($currDate <= $endDateObj) {
                $dateStr = $currDate->format('Y-m-d');
                $genderLineDataL[] = $dailyL[$dateStr] ?? 0;
                $genderLineDataP[] = $dailyP[$dateStr] ?? 0;
                $currDate->addDay();
            }
        } elseif ($trendType === 'monthly') {
            if ($isCustomFilter) {
                $currDate = Carbon::parse($startDate)->startOfMonth();
                $endDateObj = Carbon::parse($endDate)->endOfMonth();
                $monthlyL = $baseVisitors->filter(fn($v) => ($v->gender ?? $this->detectGender($v->nama)) === 'L')
                    ->groupBy(fn($item) => Carbon::parse($item->tanggal_kunjungan)->format('Y-m'))->map->count();
                $monthlyP = $baseVisitors->filter(fn($v) => ($v->gender ?? $this->detectGender($v->nama)) === 'P')
                    ->groupBy(fn($item) => Carbon::parse($item->tanggal_kunjungan)->format('Y-m'))->map->count();
                while ($currDate <= $endDateObj) {
                    $dateKey = $currDate->format('Y-m');
                    $genderLineDataL[] = $monthlyL[$dateKey] ?? 0;
                    $genderLineDataP[] = $monthlyP[$dateKey] ?? 0;
                    $currDate->addMonth();
                }
            } else {
                for ($m = 1; $m <= 12; $m++) {
                    $genderLineDataL[] = $baseVisitors->filter(fn($v) => Carbon::parse($v->tanggal_kunjungan)->month == $m && ($v->gender ?? $this->detectGender($v->nama)) === 'L')->count();
                    $genderLineDataP[] = $baseVisitors->filter(fn($v) => Carbon::parse($v->tanggal_kunjungan)->month == $m && ($v->gender ?? $this->detectGender($v->nama)) === 'P')->count();
                }
            }
        } else { // yearly
            $startYear = Carbon::parse($startDate)->year;
            $endYear = Carbon::parse($endDate)->year;
            $yearlyL = $baseVisitors->filter(fn($v) => ($v->gender ?? $this->detectGender($v->nama)) === 'L')
                ->groupBy(fn($item) => Carbon::parse($item->tanggal_kunjungan)->format('Y'))->map->count();
            $yearlyP = $baseVisitors->filter(fn($v) => ($v->gender ?? $this->detectGender($v->nama)) === 'P')
                ->groupBy(fn($item) => Carbon::parse($item->tanggal_kunjungan)->format('Y'))->map->count();
            for ($y = $startYear; $y <= $endYear; $y++) {
                $genderLineDataL[] = $yearlyL[$y] ?? 0;
                $genderLineDataP[] = $yearlyP[$y] ?? 0;
            }
        }

        // 4. Data Usia (Binned)
        $dataUmurL = [0,0,0,0,0,0,0]; // 0-17, 18-25, 26-35, 36-45, 46-55, 56-65, 65+
        $dataUmurP = [0,0,0,0,0,0,0];
        foreach ($baseVisitors as $v) {
            $age = (int)($v->usia ?? 0);
            $gender = $v->gender ?? $this->detectGender($v->nama);
            $idx = 0;
            if ($age <= 17) $idx = 0;
            elseif ($age <= 25) $idx = 1;
            elseif ($age <= 35) $idx = 2;
            elseif ($age <= 45) $idx = 3;
            elseif ($age <= 55) $idx = 4;
            elseif ($age <= 65) $idx = 5;
            else $idx = 6;
            
            if ($gender === 'L') $dataUmurL[$idx]++;
            elseif ($gender === 'P') $dataUmurP[$idx]++;
        }
        $dataUmur = [];
        for ($i=0; $i<7; $i++) $dataUmur[] = $dataUmurL[$i] + $dataUmurP[$i];

        // 5. Data Pendidikan
        $pendidikanCounts = $baseVisitors->whereNotNull('pendidikan')->where('pendidikan', '!=', '')
            ->groupBy('pendidikan')->map->count()->sortDesc();
        $dataPendidikanFormatted = [
            'labels' => $pendidikanCounts->keys()->toArray(),
            'data' => $pendidikanCounts->values()->toArray()
        ];
        if (empty($dataPendidikanFormatted['labels'])) {
            $dataPendidikanFormatted = ['labels' => ['Tidak Ada Data'], 'data' => [0]];
        }

        $keperluanData = $baseVisitors->groupBy(function($v) {
            if ($v->keperluan_kategori == 8 && !empty($v->keperluan_lainnya)) return $v->keperluan_lainnya;
            if ($v->keperluan_kategori == 8 && empty($v->keperluan_lainnya)) return null;
            return $this->getLabelKeperluan($v->keperluan_kategori);
        })->map->count()->filter()->sortDesc();

        $labelsKeperluan = $keperluanData->keys()->toArray();
        $grafikKeperluan = $keperluanData->values()->toArray();

        $dataGender = ['Laki-laki' => 0, 'Perempuan' => 0, 'Tidak Diketahui' => 0];
        foreach ($baseVisitors as $v) {
            $gender = $v->gender ?? $this->detectGender($v->nama);
            if ($gender === 'L') $dataGender['Laki-laki']++;
            elseif ($gender === 'P') $dataGender['Perempuan']++;
            else $dataGender['Tidak Diketahui']++;
        }

        return view('admin.grafik', [
            'totalPengunjung' => Pengunjung::count(),
            'bulanIni' => Pengunjung::whereMonth('tanggal_kunjungan', now()->month)->whereYear('tanggal_kunjungan', now()->year)->count(),
            'hariIni' => Pengunjung::whereDate('tanggal_kunjungan', now()->toDateString())->count(),
            'tahunIni' => Pengunjung::whereYear('tanggal_kunjungan', now()->year)->count(),
            'totalFilter' => $baseVisitors->count(),
            'trendData' => $trendData,
            'trendLabels' => $trendLabels,
            'trendTitle' => $trendTitle,
            'trendType' => $trendType,
            'labelsKeperluan' => $labelsKeperluan,
            'grafikKeperluan' => $grafikKeperluan,
            'dataGender' => $dataGender,
            'genderLineLabels' => $genderLineLabels,
            'genderLineDataL' => $genderLineDataL,
            'genderLineDataP' => $genderLineDataP,
            'dataUmurL' => $dataUmurL,
            'dataUmurP' => $dataUmurP,
            'dataUmur' => $dataUmur,
            'dataPendidikanFormatted' => $dataPendidikanFormatted,
            'labelsTahunan' => $labelsTahunan,
            'grafikTahunan' => $grafikTahunan,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'year' => $year,
            'availableYears' => $availableYears,
        ]);
    }

    public function detectGender($nama)
    {
        $maleKeywords = ['budi', 'andi', 'joko', 'ahmad', 'muhammad', 'abdul'];
        $femaleKeywords = ['siti', 'sri', 'dewi', 'ratna', 'fitri', 'indah'];
        $namaLower = strtolower($nama);
        foreach ($femaleKeywords as $k) if (strpos($namaLower, $k) !== false) return 'P';
        foreach ($maleKeywords as $k) if (strpos($namaLower, $k) !== false) return 'L';
        return 'U';
    }

    public function verify()
    {
        return view('admin.verify');
    }

    public function grafikData(Request $request)
    {
        $year = $request->input('year', now()->year);
        $startDate = $request->input('start_date', Carbon::createFromDate($year, 1, 1)->startOfDay()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::createFromDate($year, 12, 31)->endOfDay()->format('Y-m-d'));

        $baseVisitors = Pengunjung::whereDate('tanggal_kunjungan', '>=', $startDate)
            ->whereDate('tanggal_kunjungan', '<=', $endDate)
            ->get();

        // Trend logic
        $trendData = [];
        $trendLabels = [];
        $diffInDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
        
        if ($diffInDays <= 62) {
            $currDate = Carbon::parse($startDate);
            $dailyCounts = $baseVisitors->groupBy(fn($item) => Carbon::parse($item->tanggal_kunjungan)->format('Y-m-d'))->map->count();
            while ($currDate <= Carbon::parse($endDate)) {
                $trendLabels[] = $currDate->format('d M');
                $trendData[] = $dailyCounts[$currDate->format('Y-m-d')] ?? 0;
                $currDate->addDay();
            }
        } else {
            $currDate = Carbon::parse($startDate)->startOfMonth();
            $monthlyCounts = $baseVisitors->groupBy(fn($item) => Carbon::parse($item->tanggal_kunjungan)->format('Y-m'))->map->count();
            while ($currDate <= Carbon::parse($endDate)->endOfMonth()) {
                $trendLabels[] = $currDate->format('M Y');
                $trendData[] = $monthlyCounts[$currDate->format('Y-m')] ?? 0;
                $currDate->addMonth();
            }
        }

        return response()->json([
            'trendData' => $trendData,
            'trendLabels' => $trendLabels,
            'totalFilter' => $baseVisitors->count(),
            'dataGender' => [
                'Laki-laki' => $baseVisitors->filter(fn($v) => ($v->gender ?? $this->detectGender($v->nama)) === 'L')->count(),
                'Perempuan' => $baseVisitors->filter(fn($v) => ($v->gender ?? $this->detectGender($v->nama)) === 'P')->count(),
            ]
        ]);
    }

    private function getLabelKeperluan($kategori)
    {
        $labels = [
            '1' => 'Layanan Pengelolaan Hasil (Paten, PVT, Cipta, Merek)',
            '2' => 'Layanan Pemanfaatan Hasil (Kerja sama, Lisensi, Mediasi, Konsultasi)',
            '3' => 'Layanan Perpustakaan',
            '4' => 'Layanan Magang',
            '5' => 'Layanan Informasi dan Dokumentasi',
            '6' => 'Layanan Publikasi Warta',
            '7' => 'Rapat/Pertemuan',
            '8' => 'Lainnya'
        ];
        return $labels[$kategori] ?? $kategori;
    }

    public function exportGrafikHarian(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        
        $start = $startDate ? Carbon::parse($startDate) : now()->subDays(29);
        $end = $endDate ? Carbon::parse($endDate) : now();
        
        $visits = Pengunjung::selectRaw('DATE(tanggal_kunjungan) as date, count(*) as count')
            ->whereDate('tanggal_kunjungan', '>=', $start)
            ->whereDate('tanggal_kunjungan', '<=', $end)
            ->groupBy('date')
            ->pluck('count', 'date');
            
        $data = [];
        $curr = $start->copy();
        $daysCount = 0;
        
        while ($curr <= $end && $daysCount < 366) {
            $dateStr = $curr->format('Y-m-d');
            $count = $visits[$dateStr] ?? 0;
            $data[] = ['date' => $curr->copy(), 'count' => $count];
            $curr->addDay();
            $daysCount++;
        }
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(['No', 'Tanggal', 'Jumlah Pengunjung'], null, 'A1');
        
        $row = 2;
        foreach ($data as $index => $item) {
            $sheet->fromArray([$index + 1, $item['date']->format('d/m/Y'), $item['count']], null, 'A' . $row);
            $row++;
        }
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Tren_Harian_' . $start->format('Ymd') . '_' . $end->format('Ymd') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        $writer->save('php://output');
        exit;
    }

    public function exportAllExcel()
    {
        $data = Pengunjung::orderBy('tanggal_kunjungan', 'desc')->get();
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(['No', 'Nama', 'Instansi', 'Tanggal', 'Keperluan'], null, 'A1');
        
        $row = 2;
        foreach ($data as $index => $p) {
            $sheet->fromArray([
                $index + 1, 
                $p->nama, 
                $p->instansi, 
                $p->tanggal_kunjungan, 
                $this->getLabelKeperluan($p->keperluan_kategori)
            ], null, 'A' . $row);
            $row++;
        }
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Semua_Data_Pengunjung.xlsx"');
        $writer->save('php://output');
        exit;
    }

    public function exportAllChartsPdf(Request $request)
    {
        try {
            $images = $request->input('images', []);
            $title = 'Laporan Grafik Pengunjung';
            $period = $request->input('period', '');
            
            $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>' . $title . '</title><style>body { font-family: Arial, sans-serif; margin: 20px; background: white;} .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #22c55e; padding-bottom: 15px;} h1 { color: #16a34a; font-size: 24px; margin: 0;} .period { color: #666; font-size: 14px;margin: 5px 0 0 0;} .chart-container { margin-bottom: 30px; page-break-inside: avoid;border: 1px solid #ddd; padding: 15px;} .chart-title { font-size: 18px; font-weight: bold; color: #333; margin-bottom: 15px;text-align: center;} .chart-image img { width: 100%; height: auto; max-height: 250px;border: 1px solid #eee;} .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; border-top: 1px solid #ddd; padding-top: 10px;}</style></head><body><div class="header"><h1>LAPORAN GRAFIK ANALITIK</h1><div class="period">Periode: ' . $period . '</div></div>';
            
            foreach ($images as $label => $src) {
                if ($src) {
                    $html .= '<div class="chart-container"><div class="chart-title">' . $label . '</div><div class="chart-image"><img src="' . $src . '" alt="' . $label . '"></div></div>';
                }
            }
            
            $html .= '<div class="footer"><p>Dicetak: ' . Carbon::now()->format('d/m/Y H:i:s') . '</p><p>Sistem Buku Tamu Digital</p></div></body></html>';
            
            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'landscape');
            $filename = 'Grafik_Pengunjung_Lengkap_' . date('Ymd_His') . '.pdf';
            return response($pdf->output(), 200)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function exportChartPdf(Request $request)
    {
        try {
            $image = $request->input('image', '');
            $name = $request->input('name', 'Grafik');
            $period = $request->input('period', '');
            
            $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>body { font-family: Arial, sans-serif; margin: 20px;} .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #22c55e; padding-bottom: 10px;} h1 { color: #16a34a; font-size: 20px;} .chart-image img { width: 100%; height: auto; border: 1px solid #ddd;}</style></head><body><div class="header"><h1>' . $name . '</h1><p>Periode: ' . $period . '</p></div><div class="chart-image"><img src="' . $image . '"></div></body></html>';
            
            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'landscape');
            $filename = 'Grafik_' . str_replace(' ', '_', $name) . '_' . date('Ymd_His') . '.pdf';
            return response($pdf->output(), 200)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function exportChartExcel(Request $request)
    {
        try {
            $type = $request->input('type');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $year = $request->input('year', now()->year);

            $baseVisitors = Pengunjung::whereDate('tanggal_kunjungan', '>=', $startDate)
                ->whereDate('tanggal_kunjungan', '<=', $endDate)
                ->get();

            $data = [];
            $labels = [];
            $name = 'Grafik';

            switch ($type) {
                case 'trend':
                    $name = 'Tren Pengunjung';
                    // Re-calculate trend data similar to grafik() method
                    // For simplicity, I'll just use the same logic
                    $diffInDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
                    if ($diffInDays <= 62) {
                        $currDate = Carbon::parse($startDate);
                        $dailyCounts = $baseVisitors->groupBy(fn($item) => Carbon::parse($item->tanggal_kunjungan)->format('Y-m-d'))->map->count();
                        while ($currDate <= Carbon::parse($endDate)) {
                            $labels[] = $currDate->format('d M Y');
                            $data[] = $dailyCounts[$currDate->format('Y-m-d')] ?? 0;
                            $currDate->addDay();
                        }
                    } else {
                        $currDate = Carbon::parse($startDate)->startOfMonth();
                        $monthlyCounts = $baseVisitors->groupBy(fn($item) => Carbon::parse($item->tanggal_kunjungan)->format('Y-m'))->map->count();
                        while ($currDate <= Carbon::parse($endDate)->endOfMonth()) {
                            $labels[] = $currDate->format('M Y');
                            $data[] = $monthlyCounts[$currDate->format('Y-m')] ?? 0;
                            $currDate->addMonth();
                        }
                    }
                    break;
                case 'keperluan':
                    $name = 'Kategori Keperluan';
                    $keperluanData = $baseVisitors->groupBy(function($v) {
                        if ($v->keperluan_kategori == 8 && !empty($v->keperluan_lainnya)) return $v->keperluan_lainnya;
                        return $this->getLabelKeperluan($v->keperluan_kategori);
                    })->map->count()->sortDesc();
                    $labels = $keperluanData->keys()->toArray();
                    $data = $keperluanData->values()->toArray();
                    break;
                case 'gender_line':
                    $name = 'Tren Gender';
                    // Similar to trend but with gender split
                    // Just return total for simplicity in Excel
                    $labels = ['Laki-laki', 'Perempuan', 'Tidak Diketahui'];
                    $data = [
                        $baseVisitors->filter(fn($v) => ($v->gender ?? $this->detectGender($v->nama)) === 'L')->count(),
                        $baseVisitors->filter(fn($v) => ($v->gender ?? $this->detectGender($v->nama)) === 'P')->count(),
                        $baseVisitors->filter(fn($v) => !in_array($v->gender ?? $this->detectGender($v->nama), ['L', 'P']))->count(),
                    ];
                    break;
                case 'usia':
                    $name = 'Distribusi Usia';
                    $labels = ['0-17', '18-25', '26-35', '36-45', '46-55', '56-65', '65+'];
                    $data = [0,0,0,0,0,0,0];
                    foreach ($baseVisitors as $v) {
                        $age = (int)($v->usia ?? 0);
                        if ($age <= 17) $data[0]++;
                        elseif ($age <= 25) $data[1]++;
                        elseif ($age <= 35) $data[2]++;
                        elseif ($age <= 45) $data[3]++;
                        elseif ($age <= 55) $data[4]++;
                        elseif ($age <= 65) $data[5]++;
                        else $data[6]++;
                    }
                    break;
                case 'pendidikan':
                    $name = 'Tingkat Pendidikan';
                    $pendidikanCounts = $baseVisitors->whereNotNull('pendidikan')->where('pendidikan', '!=', '')
                        ->groupBy('pendidikan')->map->count()->sortDesc();
                    $labels = $pendidikanCounts->keys()->toArray();
                    $data = $pendidikanCounts->values()->toArray();
                    break;
                case 'tahunan':
                    $name = 'Pengunjung Tahunan';
                    for ($i = 4; $i >= 0; $i--) {
                        $y = now()->year - $i;
                        $labels[] = (string)$y;
                        $data[] = Pengunjung::whereYear('tanggal_kunjungan', $y)->count();
                    }
                    break;
            }
            
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'Kategori');
            $sheet->setCellValue('B1', 'Jumlah');
            $sheet->getStyle('A1:B1')->getFont()->setBold(true);
            
            $row = 2;
            foreach ($labels as $index => $label) {
                $sheet->setCellValue('A' . $row, $label);
                $sheet->setCellValue('B' . $row, $data[$index] ?? 0);
                $row++;
            }
            
            foreach (range('A', 'B') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);
            
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filename = 'Data_' . str_replace(' ', '_', $name) . '_' . date('Ymd_His') . '.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }
}
