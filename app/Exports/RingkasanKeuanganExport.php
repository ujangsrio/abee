<?php

namespace App\Exports;

use App\Filament\Resources\LaporanKeuanganResource;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RingkasanKeuanganExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function array(): array
    {
        $ringkasan = LaporanKeuanganResource::getRingkasanKeuangan($this->startDate, $this->endDate);

        return [
            ['PENDAPATAN', '', ''],
            ['Pendapatan dari Reservasi', 'Rp ' . number_format($ringkasan['pendapatan_reservasi'], 0, ',', '.'), ''],
            ['Pendapatan Manual', 'Rp ' . number_format($ringkasan['pendapatan_manual'], 0, ',', '.'), ''],
            ['Total Pendapatan', 'Rp ' . number_format($ringkasan['total_pendapatan'], 0, ',', '.'), ''],
            ['', '', ''],
            ['PENGELUARAN', '', ''],
            ['Total Pengeluaran', 'Rp ' . number_format($ringkasan['total_pengeluaran'], 0, ',', '.'), ''],
            ['', '', ''],
            ['LABA BERSIH', 'Rp ' . number_format($ringkasan['laba_bersih'], 0, ',', '.'), $ringkasan['laba_bersih'] >= 0 ? 'UNTUNG' : 'RUGI'],
        ];
    }

    public function headings(): array
    {
        $periode = $this->startDate && $this->endDate
            ? 'Periode: ' . \Carbon\Carbon::parse($this->startDate)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($this->endDate)->format('d M Y')
            : 'Semua Periode';

        return [
            ['LAPORAN RINGKASAN KEUANGAN - ARETHA BEAUTY'],
            [$periode],
            [''],
            ['KETERANGAN', 'JUMLAH', 'STATUS']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Judul
        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Periode
        $sheet->mergeCells('A2:C2');
        $sheet->getStyle('A2')->getFont()->setItalic(true);

        // Header tabel
        $sheet->getStyle('A4:C4')->getFont()->setBold(true);
        $sheet->getStyle('A4:C4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A4:C4')->getFill()->getStartColor()->setARGB('FF2D5F7D');
        $sheet->getStyle('A4:C4')->getFont()->getColor()->setARGB('FFFFFFFF');

        // Auto size
        foreach (range('A', 'C') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return [];
    }

    public function title(): string
    {
        return 'Ringkasan';
    }
}
