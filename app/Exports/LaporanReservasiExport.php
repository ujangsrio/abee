<?php

namespace App\Exports;

use App\Models\CustomerBooking;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class LaporanReservasiExport implements FromCollection, WithHeadings, WithMapping
{
    protected $dateFrom;
    protected $dateUntil;
    protected $status;
    protected $serviceId;

    // PERBAIKAN: Update constructor untuk menerima 4 parameter
    public function __construct($dateFrom = null, $dateUntil = null, $status = null, $serviceId = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateUntil = $dateUntil;
        $this->status = $status;
        $this->serviceId = $serviceId;
    }

    public function collection()
    {
        $query = CustomerBooking::with(['service', 'customer'])
            ->where('status', 'Selesai');

        // Apply filters
        if ($this->dateFrom) {
            $query->whereDate('date', '>=', $this->dateFrom);
        }

        if ($this->dateUntil) {
            $query->whereDate('date', '<=', $this->dateUntil);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->serviceId) {
            $query->where('service_id', $this->serviceId);
        }

        return $query->orderBy('date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Pelanggan',
            'WhatsApp',
            'Layanan',
            'Tanggal',
            'Jam',
            'Harga',
            'Tipe Pembayaran',
            'Status DP',
            'Status Reservasi',
            'Tanggal Dibuat'
        ];
    }

    public function map($booking): array
    {
        return [
            $booking->id,
            $booking->customer_name,
            $booking->customer->whatsapp ?? '-',
            $booking->service->nama ?? '-',
            Carbon::parse($booking->date)->format('d-m-Y'),
            $booking->time,
            'Rp ' . number_format($booking->service->harga ?? 0, 0, ',', '.'),
            $booking->tipe_pembayaran == 'full' ? 'Lunas' : 'DP',
            $booking->status_dp,
            $booking->status,
            Carbon::parse($booking->created_at)->format('d-m-Y H:i'),
        ];
    }
}
