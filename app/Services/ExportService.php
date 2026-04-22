<?php

namespace App\Services;

use App\Models\Pengunjung;
use App\Models\FormField;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ExportService
{
    public function exportExcel($year, $month = null)
    {
        $pengunjung = Pengunjung::whereYear('tanggal_kunjungan', $year);
        
        if ($month) {
            $pengunjung->whereMonth('tanggal_kunjungan', $month);
        }
        
        $data = $pengunjung->orderBy('tanggal_kunjungan', 'desc')->get();
        
        // Ambil field kustom yang aktif
        $customFields = FormField::where('is_core', false)
            ->where('is_visible', true)
            ->orderBy('order')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set properties
        $spreadsheet->getProperties()
            ->setCreator('Buku Tamu Digital')
            ->setLastModifiedBy('Buku Tamu Digital')
            ->setTitle('Rekapan Pengunjung ' . ($month ? $this->getNamaBulan($month) . ' ' : '') . $year)
            ->setSubject('Data Pengunjung');
        
        // Header
        $headers = ['No', 'Tanggal', 'Waktu', 'Nama', 'Usia', 'Gender', 'No. HP', 'Email', 'Instansi', 'Pendidikan', 'Yang Ditemui', 'Keperluan', 'Foto Selfie'];
        
        // Tambahkan header kustom
        foreach ($customFields as $cf) {
            $headers[] = $cf->label;
        }
        
        $sheet->fromArray($headers, null, 'A1');
        
        // Hitung kolom terakhir (N, O, P, dst)
        $lastColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        
        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '22C55E']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:' . $lastColumnLetter . '1')->applyFromArray($headerStyle);
        
        // Data
        $row = 2;
        foreach ($data as $index => $p) {
            $rowData = [
                $index + 1,
                Carbon::parse($p->tanggal_kunjungan)->format('d/m/Y'),
                Carbon::parse($p->tanggal_kunjungan)->format('H:i'),
                $p->nama,
                $p->usia ?? '-',
                $p->gender == 'L' ? 'Laki-laki' : ($p->gender == 'P' ? 'Perempuan' : '-'),
                $p->no_hp ?? '-',
                $p->email ?? '-',
                $p->instansi ?? '-',
                $p->pendidikan ?? '-',
                $p->yang_ditemui ?? '-',
                ((int)$p->keperluan_kategori === 8) ? ($p->keperluan_lainnya ?? 'Lainnya') : $this->getLabelKeperluan($p->keperluan_kategori),
                $p->selfie_photo ? 'Ada' : 'Tidak ada',
            ];

            // Tambahkan data kustom
            foreach ($customFields as $cf) {
                $val = $p->metadata[$cf->name] ?? '-';
                $rowData[] = is_array($val) ? implode(', ', $val) : $val;
            }

            $sheet->fromArray($rowData, null, 'A' . $row);
            
            // Tambahkan foto di kolom M jika ada
            if ($p->selfie_photo) {
                $imagePath = Storage::disk('public')->path('selfies/' . $p->selfie_photo);
                if (Storage::disk('public')->exists('selfies/' . $p->selfie_photo)) {
                    try {
                        $drawing = new Drawing();
                        $drawing->setName('Selfie ' . $p->nama);
                        $drawing->setDescription('Selfie Photo');
                        $drawing->setPath($imagePath);
                        $drawing->setWidth(50);
                        $drawing->setHeight(50);
                        $drawing->setCoordinates('M' . $row);
                        $drawing->setOffsetX(5);
                        $drawing->setOffsetY(5);
                        $drawing->setWorksheet($sheet);
                        
                        $sheet->getRowDimension($row)->setRowHeight(60);
                    } catch (\Exception $e) {
                    }
                }
            }
            
            $row++;
        }
        
        // Auto-size columns
        $lastColIndex = count($headers);
        for ($i = 1; $i <= $lastColIndex; $i++) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            if ($columnLetter === 'L' || $i > 13) {
                $sheet->getColumnDimension($columnLetter)->setWidth(20);
                $sheet->getStyle($columnLetter . '2:' . $columnLetter . ($row - 1))->getAlignment()->setWrapText(true);
            } else {
                $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
            }
        }
        
        // Border
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];
        $sheet->getStyle('A1:' . $lastColumnLetter . ($row - 1))->applyFromArray($styleArray);
        
        $filename = 'Rekapan_Pengunjung_' . ($month ? $this->getNamaBulan($month) . '_' : '') . $year . '.xlsx';
        
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    public function exportPdf($year, $month = null)
    {
        set_time_limit(300);
        
        $pengunjung = Pengunjung::whereYear('tanggal_kunjungan', $year);
        
        if ($month) {
            $pengunjung->whereMonth('tanggal_kunjungan', $month);
        }
        
        $data = $pengunjung->orderBy('tanggal_kunjungan', 'desc')
                          ->limit(2000)
                          ->get();
        
        $labelKeperluan = $this->getLabelsKeperluan();
        
        // Ambil field kustom yang aktif
        $customFields = FormField::where('is_core', false)
            ->where('is_visible', true)
            ->orderBy('order')
            ->get();

        foreach ($data as $p) {
            if ((int)$p->keperluan_kategori === 8) {
                $p->keperluan_label = $p->keperluan_lainnya ?? 'Lainnya';
            } else {
                $p->keperluan_label = $labelKeperluan[$p->keperluan_kategori] ?? $p->keperluan_kategori;
            }
        }
        
        $filename = 'Rekapan_Pengunjung_' . ($month ? $this->getNamaBulan($month) . '_' : '') . $year . '.pdf';
        
        $pdf = Pdf::loadView('admin.export-pdf', [
            'pengunjung' => $data,
            'year' => $year,
            'month' => $month,
            'monthName' => $month ? $this->getNamaBulan($month) : null,
            'customFields' => $customFields,
        ]);
        
        $pdf->setOptions([
            'isRemoteEnabled' => false,
            'isHtml5ParserEnabled' => true,
            'defaultFont' => 'Arial',
            'enable_php' => false,
            'chroot' => public_path(),
        ]);
        
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download($filename);
    }

    public function getNamaBulan($month)
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $months[(int)$month] ?? '';
    }

    public function getLabelKeperluan($kategori)
    {
        $labels = $this->getLabelsKeperluan();
        return $labels[$kategori] ?? $kategori;
    }

    private function getLabelsKeperluan()
    {
        return [
            '1' => 'Layanan Pengelolaan Hasil (Paten, PVT, Cipta, Merek)',
            '2' => 'Layanan Pemanfaatan Hasil (Kerja sama, Lisensi, Mediasi, Konsultasi)',
            '3' => 'Layanan Perpustakaan',
            '4' => 'Layanan Magang',
            '5' => 'Layanan Informasi dan Dokumentasi',
            '6' => 'Layanan Publikasi Warta',
            '7' => 'Rapat/Pertemuan',
            '8' => 'Lainnya'
        ];
    }
}
