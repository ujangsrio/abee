<?php

namespace App\Exports;

use App\Models\CustomerBooking;
use App\Helpers\LayananHelper;
use Illuminate\Support\Carbon;

class LaporanExport
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

public function download()
{
    $bookings = $this->getData();

    $fileName = 'laporan-reservasi-' . date('Y-m-d-H-i') . '.csv';

    $headers = [
        'Content-Type' => 'text/csv; charset=utf-8',
        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        'Pragma' => 'no-cache',
        'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
        'Expires' => '0'
    ];

    $callback = function () use ($bookings) {
        $file = fopen('php://output', 'w');

        // Tambahkan BOM agar Excel membaca UTF-8
        fwrite($file, "\xEF\xBB\xBF");

        // ==== HEADER UTAMA ====
        fputcsv($file, [], ';'); // baris kosong atas
        fputcsv($file, ['LAPORAN RESERVASI - ARETHA BEAUTY'], ';');
        fputcsv($file, ['Periode: ' . ($this->startDate ? $this->formatDate($this->startDate) . ' - ' . $this->formatDate($this->endDate) : 'Semua Data')], ';');
        fputcsv($file, ['Dibuat pada: ' . date('d/m/Y H:i')], ';');
        fputcsv($file, [], ';'); // baris kosong

        // ==== HEADER KOLOM ====
        fputcsv($file, [
            'No',
            'Nama Pelanggan',
            'Layanan',
            'Tanggal',
            'Waktu',
            'Status',
            'Harga (Rp)',
            'Status DP'
        ], ';');

        // ==== DATA ====
        $no = 1;
        $totalPendapatan = 0;
        $totalReservasi = $bookings->count();
        $totalSelesai = $bookings->where('status', 'Selesai')->count();

        foreach ($bookings as $booking) {
            $harga = LayananHelper::getHargaLayanan($booking->service_id);
            if ($booking->status === 'Selesai') {
                $totalPendapatan += $harga;
            }

            fputcsv($file, [
                $no++,
                $this->cleanText($booking->customer_name),
                $this->cleanText(LayananHelper::getNamaLayanan($booking->service_id)),
                $this->formatDate($booking->date),
                $booking->time,
                $booking->status,
                $harga,
                $booking->status_dp
            ], ';');
        }

       
        // ==== RINGKASAN ====
        fputcsv($file, ['RINGKASAN LAPORAN'], ';');
        fputcsv($file, ['Total Reservasi', $totalReservasi], ';');
        fputcsv($file, ['Reservasi Selesai', $totalSelesai], ';');
        fputcsv($file, ['Reservasi Dibatalkan', $bookings->where('status', 'Dibatalkan')->count()], ';');
        fputcsv($file, ['Total Pendapatan (Rp)', $totalPendapatan], ';');

       
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

    protected function getData()
    {
        $query = CustomerBooking::with(['customer']);
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('date', [$this->startDate, $this->endDate]);
        }
        return $query->orderBy('date', 'desc')->get();
    }

    protected function formatDate($date)
    {
        if (!$date) return '';
        return Carbon::parse($date)->format('d/m/Y');
    }

    protected function cleanText($text)
    {
        if (is_null($text)) return '';
        return trim(preg_replace('/\s+/', ' ', str_replace(["\r", "\n", "\t"], ' ', $text)));
    }
}