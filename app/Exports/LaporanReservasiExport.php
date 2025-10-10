<?php

namespace App\Exports;

use App\Models\CustomerBooking;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class LaporanReservasiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $query = CustomerBooking::with(['service', 'customer'])
            ->where('status', 'Selesai');

        if ($this->startDate) {
            $query->whereDate('date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('date', '<=', $this->endDate);
        }

        return $query->orderBy('date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'NAMA PELANGGAN',
            'WHATSAPP',
            'LAYANAN',
            'TIPE LAYANAN',
            'TANGGAL',
            'JAM',
            'HARGA (Rp)',
            'TIPE PEMBAYARAN',
            'STATUS DP',
            'TANGGAL DIBUAT'
        ];
    }

    public function map($reservasi): array
    {
        // Format tipe layanan
        $tipeLayanan = $reservasi->tipe_layanan;
        $tipeLayananFormatted = '-';

        if (is_array($tipeLayanan)) {
            $tipeLayananFormatted = collect($tipeLayanan)->map(function ($item) {
                return match ($item) {
                    'home_service' => 'Home Service',
                    'studio' => 'Studio',
                    default => ucfirst($item)
                };
            })->implode(', ');
        } elseif (is_string($tipeLayanan)) {
            try {
                $decoded = json_decode($tipeLayanan, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $tipeLayananFormatted = collect($decoded)->map(function ($item) {
                        return match ($item) {
                            'home_service' => 'Home Service',
                            'studio' => 'Studio',
                            default => ucfirst($item)
                        };
                    })->implode(', ');
                }
            } catch (\Exception $e) {
                $tipeLayananFormatted = $tipeLayanan;
            }
        }

        return [
            $reservasi->id,
            $reservasi->customer_name,
            $reservasi->customer->whatsapp ?? '-',
            $reservasi->service->nama ?? 'Layanan tidak ditemukan',
            $tipeLayananFormatted,
            Carbon::parse($reservasi->date)->format('d/m/Y'),
            $reservasi->time,
            number_format($reservasi->service->harga ?? 0, 0, ',', '.'),
            $reservasi->tipe_pembayaran === 'full' ? 'Lunas' : 'DP',
            $reservasi->status_dp === 'Lunas' ? 'Lunas' : 'Belum',
            Carbon::parse($reservasi->created_at)->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header style
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
        $sheet->getStyle('A1:K1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A1:K1')->getFill()->getStartColor()->setARGB('FF2D5F7D');
        $sheet->getStyle('A1:K1')->getFont()->getColor()->setARGB('FFFFFFFF');

        // Auto size columns
        foreach (range('A', 'K') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return [];
    }

    public function title(): string
    {
        return 'Laporan Reservasi';
    }
}
