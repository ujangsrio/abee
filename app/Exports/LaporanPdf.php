<?php

namespace App\Exports;

use App\Models\CustomerBooking;
use App\Helpers\LayananHelper;

class LaporanPdf
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

        $html = $this->generateHtml($bookings);

        $fileName = 'laporan-reservasi-' . date('Y-m-d-H-i') . '.html';

        $headers = [
            'Content-Type' => 'text/html; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        return response($html, 200, $headers);
    }

    protected function getData()
    {
        $query = CustomerBooking::with(['customer']);

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('date', [$this->startDate, $this->endDate]);
        }

        return $query->orderBy('date', 'desc')->get();
    }

    protected function generateHtml($bookings)
    {
        $totalReservasi = $bookings->count();
        $totalSelesai = $bookings->where('status', 'Selesai')->count();
        $totalPendapatan = 0;

        foreach ($bookings->where('status', 'Selesai') as $booking) {
            $totalPendapatan += LayananHelper::getHargaLayanan($booking->service_id);
        }

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Laporan Reservasi - Aretha Beauty</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
                .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #8B5CF6; padding-bottom: 10px; }
                .header h1 { color: #333; margin: 0; }
                .header p { color: #666; margin: 5px 0 0 0; }
                .summary { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
                .summary h3 { margin: 0 0 10px 0; color: #333; }
                .summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
                .summary-item { text-align: center; padding: 10px; background: white; border-radius: 5px; border: 1px solid #e0e0e0; }
                .summary-value { font-size: 18px; font-weight: bold; color: #8B5CF6; }
                .summary-label { font-size: 12px; color: #666; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10px; }
                th { background-color: #8B5CF6; color: white; padding: 8px; text-align: left; border: 1px solid #ddd; }
                td { padding: 6px; border: 1px solid #ddd; }
                tr:nth-child(even) { background-color: #f8f9fa; }
                .footer { margin-top: 30px; text-align: center; color: #666; font-size: 10px; border-top: 1px solid #ddd; padding-top: 10px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Laporan Reservasi - Aretha Beauty</h1>
                <p>Periode: ' . ($this->startDate ? date('d M Y', strtotime($this->startDate)) . ' - ' . date('d M Y', strtotime($this->endDate)) : 'Semua Data') . '</p>
                <p>Dibuat pada: ' . date('d M Y H:i') . '</p>
            </div>

            <div class="summary">
                <h3>Ringkasan</h3>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-value">' . $totalReservasi . '</div>
                        <div class="summary-label">Total Reservasi</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-value">' . $totalSelesai . '</div>
                        <div class="summary-label">Selesai</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-value">Rp ' . number_format($totalPendapatan, 0, ',', '.') . '</div>
                        <div class="summary-label">Total Pendapatan</div>
                    </div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pelanggan</th>
                        <th>Layanan</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th>Status DP</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody>';

        $no = 1;
        foreach ($bookings as $booking) {
            $html .= '
                    <tr>
                        <td>' . $no++ . '</td>
                        <td>' . $booking->customer_name . '</td>
                        <td>' . LayananHelper::getNamaLayanan($booking->service_id) . '</td>
                        <td>' . $booking->date->format('d/m/Y') . '</td>
                        <td>' . $booking->time . '</td>
                        <td>' . $booking->status . '</td>
                        <td>' . $booking->status_dp . '</td>
                        <td>Rp ' . number_format(LayananHelper::getHargaLayanan($booking->service_id), 0, ',', '.') . '</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>

            <div class="footer">
                Laporan ini dibuat secara otomatis oleh Sistem Aretha Beauty
            </div>
        </body>
        </html>';

        return $html;
    }
}
