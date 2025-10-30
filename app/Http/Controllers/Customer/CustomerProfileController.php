<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CustomerProfileController extends Controller
{
    public function index()
    {
        $user = Auth::guard('customer')->user();
        $customer = $user->customer;

        return view('customer.profil.index', compact('customer', 'user'));
    }

    public function update(Request $request) 
    {
        $user = Auth::guard('customer')->user();
        $customer = $user->customer;

        // Aturan validasi yang diperbarui untuk mencakup whatsapp dan password
        $request->validate([
            'name' => 'required|string|min:5|max:255', 
            'email' => 'required|email|unique:users,email,' . $user->id,
            // Menambahkan validasi whatsapp untuk test cases 3, 5, dan 7
            'whatsapp' => 'required|string|min:10|max:15', 
            // Menggunakan required_with:password untuk test case 4
            'old_password' => 'nullable|string|required_with:password', 
            'password' => 'nullable|min:6|confirmed',
            // Aturan 'photo' dihapus karena tidak digunakan di view
        ]);

        // Update data dari tabel users
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            // Cek password lama hanya jika password baru diisi
            if (!Hash::check($request->old_password, $user->password)) {
                return back()->withErrors(['old_password' => 'Password lama tidak cocok']);
            }
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Muat ulang objek user yang baru dari database
        Auth::guard('customer')->setUser($user->fresh());

        // Update data dari tabel customers
        $customer->whatsapp = $request->whatsapp;

        // Logika photo dihapus/dibiarkan sebagai komentar sesuai permintaan
        // ...

        $customer->save();

        return redirect()->route('customer.akun.index')->with('success', 'Profil berhasil diperbarui.');
    }
}