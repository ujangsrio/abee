<?php

namespace App\Http\Controllers;

use App\Models\Reservasi;
use App\Models\CustomerBooking;
use Illuminate\Http\Request;

class ReservasiController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomerBooking::query();

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $bookings = CustomerBooking::with(['customer', 'service'])->get();
        return view('admin.reservasi.index', compact('bookings'));
    }

    public function terima($id)
    {
        $booking = CustomerBooking::with(['customer', 'service'])->findOrFail($id);

        Reservasi::create([
            'pelanggan_id'    => $booking->customer->id,
            'layanan_id'      => $booking->service_id,
            'tanggal'         => $booking->date,
            'jam'             => $booking->time,
            'harga'           => $booking->service->harga ?? 0,
            'bukti_transfer'  => $booking->bukti_transfer,
            'kontak'          => $booking->whatsapp,
            'tipe_layanan'    => $booking->tipe_layanan,
        ]);

        $booking->status = 'Dikonfirmasi';
        $booking->save();

        return redirect()->route('admin.reservasi.index')->with('success', 'Reservasi telah dikonfirmasi dan disimpan.');
    }

    public function tolak($id)
    {
        $reservasi = CustomerBooking::findOrFail($id);
        $reservasi->status = 'Dibatalkan';
        $reservasi->save();

        return redirect()->route('admin.reservasi.index')->with('success', 'Reservasi telah ditolak.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Menunggu,Dikonfirmasi,Dibatalkan,Selesai',
        ]);

        $reservasi = CustomerBooking::findOrFail($id);

        // Update status reservasi
        $reservasi->status = $request->status;

        // Jika status selesai → otomatis DP Lunas
        if ($request->status === 'Selesai') {
            $reservasi->status_dp = 'Lunas';
        }

        $reservasi->save();

        return redirect()->route('admin.reservasi.index')->with('success', 'Status reservasi berhasil diperbarui.');
    }

    public function confirmDp($id)
    {
        $booking = CustomerBooking::findOrFail($id);

        if ($booking->status === 'Dibatalkan') {
            return redirect()->route('admin.reservasi.index')->with('error', 'Reservasi sudah dibatalkan, tidak bisa dikonfirmasi lagi.');
        }

        $booking->status_dp = 'Lunas';
        $booking->status    = 'Dikonfirmasi';
        $booking->save();

        return redirect()->route('admin.reservasi.index')->with('success', 'Pembayaran DP telah dikonfirmasi dan reservasi otomatis dikonfirmasi.');
    }

    public function rejectDp($id)
    {
        $booking = CustomerBooking::findOrFail($id);

        $booking->status_dp = 'Belum';
        $booking->status    = 'Dibatalkan';
        $booking->save();

        return redirect()->route('admin.reservasi.index')->with('success', 'Pembayaran DP ditolak dan reservasi otomatis dibatalkan.');
    }
}
