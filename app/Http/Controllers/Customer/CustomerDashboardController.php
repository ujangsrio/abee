<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerBooking;
use App\Models\Layanan;
use App\Models\Promo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class CustomerDashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::guard('customer')->user();
        $customer = Customer::where('user_id', $user->id)->first();
        $membership = $customer->membership ?? null;

        $reservasiAktif = CustomerBooking::with('service')
            ->where('customer_id', $customer->id ?? 0)
            ->whereNotIn('status', ['selesai', 'dibatalkan'])
            ->orderBy('date', 'asc')
            ->get();

        $totalReservations = CustomerBooking::where('customer_id', $customer->id ?? 0)->count();

        $layanan = Layanan::all();

        // Ambil semua promo aktif
        $promoList = Promo::where('tanggal_berakhir', '>=', Carbon::now())->get();

        // Ambil salah satu promo untuk tampilan utama
        $promoUtama = $promoList->first();

        return view('customer.dashboard', [
            'reservasiAktif' => $reservasiAktif,
            'membership' => $membership,
            'totalReservations' => $totalReservations,
            'layanan' => $layanan,
            'promoLayanan' => $promoUtama,
            'semuaPromo' => $promoList,
        ]);
    }


    public function akun() {
        return view('customer.akun.index');
    }

    public function reservasiaktif() {
        return view('customer.reservasiaktif.index');
    }

    public function layanan() {
        $layanan = Layanan::all(); 
        return view('customer.layanan.index', compact('layanan'));
    }

    public function kontak() {
        return view('customer.kontak.index');
    }

    public function cari() {
        return view('customer.cari.index');
    }

    public function profil() {
        return view('customer.profil.index');
    }

    public function logout() {
        auth()->logout(); // Logout Laravel
        return redirect('/login');
    }

    
}
