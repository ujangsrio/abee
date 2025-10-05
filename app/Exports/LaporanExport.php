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

            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");

            // Header utama (single line)
            fputcsv($file, ['LAPORAN RESERVASI - ARETHA BEAUTY']);
            fputcsv($file, ['Periode: ' . ($this->startDate ? $this->formatDate($this->startDate) . ' - ' . $this->formatDate($this->endDate) : 'Semua Data')]);
            fputcsv($file, ['Dibuat pada: ' . date('d/m/Y H:i')]);
            fputcsv($file, []); // Empty row

            // Column headers (hanya kolom penting)
            fputcsv($file, [
                'No',
                'Nama Pelanggan',
                'Layanan',
                'Tanggal',
                'Waktu',
                'Status',
                'Harga',
                'Status DP'
            ]);

            // Data rows
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
                    'Rp ' . number_format($harga, 0, ',', '.'),
                    $booking->status_dp
                ]);
            }

            // Empty rows
            fputcsv($file, []);
            fputcsv($file, []);

            // Summary dalam format yang rapi
            fputcsv($file, ['RINGKASAN']);
            fputcsv($file, ['Total Reservasi', $totalReservasi]);
            fputcsv($file, ['Reservasi Selesai', $totalSelesai]);
            fputcsv($file, ['Reservasi Dibatalkan', $bookings->where('status', 'Dibatalkan')->count()]);
            fputcsv($file, ['Total Pendapatan', 'Rp ' . number_format($totalPendapatan, 0, ',', '.')]);

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

    /**
     * Format date safely
     */
    protected function formatDate($date)
    {
        if (is_string($date)) {
            try {
                // Coba berbagai format date
                $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'Y/m/d'];
                foreach ($formats as $format) {
                    $parsed = Carbon::createFromFormat($format, $date);
                    if ($parsed !== false) {
                        return $parsed->format('d/m/Y');
                    }
                }
                // Jika semua gagal, coba parsing biasa
                return date('d/m/Y', strtotime($date));
            } catch (\Exception $e) {
                return $date;
            }
        }

        if ($date instanceof Carbon) {
            return $date->format('d/m/Y');
        }

        return $date;
    }

    /**
     * Clean text untuk CSV (remove karakter problematic)
     */
    protected function cleanText($text)
    {
        if (is_null($text)) {
            return '';
        }

        // Remove karakter yang bisa bikin CSV berantakan
        $text = str_replace(["\r", "\n", "\t"], ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text); // Multiple spaces to single space
        $text = trim($text);

        return $text;
    }
}
