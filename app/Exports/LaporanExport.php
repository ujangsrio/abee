<?php

namespace App\Exports;

use App\Models\CustomerBooking;
use App\Helpers\LayananHelper;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class LaporanExport
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    private function cleanText($text)
    {
        if (is_null($text)) {
            return '';
        }

        // Remove non-printable characters except tabs, newlines, and carriage returns
        $text = preg_replace('/[^\x20-\x7E\x0A\x0D\x09]/', '', $text);

        // Trim whitespace
        $text = trim($text);

        // Escape starting characters that Excel might interpret as formula
        if (in_array(substr($text, 0, 1), ['=', '+', '-', '@'])) {
            $text = "'" . $text;
        }

        return $text;
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

                // FORMAT TANGGAL YANG DIPERBAIKI
                $formattedDate = $this->formatDateForExport($booking->date);

                fputcsv($file, [
                    $no++,
                    $this->cleanText($booking->customer_name),
                    $this->cleanText(LayananHelper::getNamaLayanan($booking->service_id)),
                    $formattedDate, // Menggunakan format yang diperbaiki
                    $booking->time,
                    $booking->status,
                    number_format($harga, 0, ',', '.'), // Format angka dengan separator
                    $booking->status_dp
                ], ';');
            }

            // ==== RINGKASAN ====
            fputcsv($file, [], ';'); // baris kosong
            fputcsv($file, ['RINGKASAN LAPORAN'], ';');
            fputcsv($file, ['Total Reservasi', $totalReservasi], ';');
            fputcsv($file, ['Reservasi Selesai', $totalSelesai], ';');
            fputcsv($file, ['Reservasi Dibatalkan', $bookings->where('status', 'Dibatalkan')->count()], ';');
            fputcsv($file, ['Total Pendapatan (Rp)', number_format($totalPendapatan, 0, ',', '.')], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function getData()
    {
        $query = CustomerBooking::query();
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('date', [$this->startDate, $this->endDate]);
        }
        return $query->orderBy('date', 'desc')->get();
    }

    /**
     * Format tanggal untuk display (tampilan)
     */
    protected function formatDate($date)
    {
        if (!$date) return '';
        return Carbon::parse($date)->format('d/m/Y');
    }

    /**
     * Format tanggal khusus untuk export CSV/Excel
     * Menggunakan format ISO yang lebih universal
     */
    protected function formatDateForExport($date)
    {
        if (!$date) return '';

        try {
            // Format dengan quotes untuk memaksa Excel membaca sebagai teks
            return "'" . Carbon::parse($date)->format('d/m/Y');
        } catch (\Exception $e) {
            return $date;
        }
    }

    /**
     * Alternatif: Format dengan quotes untuk memastikan Excel membaca sebagai teks
     */
    protected function formatDateAsText($date)
    {
        if (!$date) return '';

        try {
            $formatted = Carbon::parse($date)->format('d/m/Y');
            return "'" . $formatted; // Tambahkan apostrof untuk memaksa format teks
        } catch (\Exception $e) {
            return $date;
        }
    }
}
