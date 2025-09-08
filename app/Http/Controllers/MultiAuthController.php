<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MultiAuthController extends Controller
{
    // Tampilkan form login
    public function showLoginForm()
    {
        return view('auth.login'); // pastikan view ini tersedia
    }

    // Proses login untuk admin & customer
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        // Login sebagai admin
        if (Auth::guard('admin')->attempt($credentials)) {
            $user = Auth::guard('admin')->user();
            if ($user->role === 'admin') {
                $request->session()->regenerate();
                return redirect()->intended('/admin/dashboard');
            } else {
                Auth::guard('admin')->logout();
            }
        }

        // Login sebagai customer
        if (Auth::guard('customer')->attempt($credentials)) {
            $user = Auth::guard('customer')->user();
            if ($user->role === 'customer') {
                $request->session()->regenerate();
                return redirect()->intended('/customer/dashboard');
            } else {
                Auth::guard('customer')->logout();
            }
        }


        // Kalau gagal
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    // Tampilkan form register untuk customer
    public function customerRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'whatsapp' => 'required|string|max:20',
        ]);

        // Simpan ke tabel `users`
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer',
        ]);

        // Simpan ke tabel `customers`
        Customer::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'is_member' => 0,
            'kode_member' => null,
            'whatsapp' => $request->whatsapp,
            'photo' => null,
        ]);

        // ✅ Simpan juga ke tabel `pelanggans`
        \App\Models\Pelanggan::create([
            'name' => $user->name,
            'email' => $user->email,
            'whatsapp' => $request->whatsapp,
        ]);

        // Login langsung
        Auth::guard('customer')->login($user);

        return redirect()->intended('/customer/dashboard');
    }

    public function showCustomerRegisterForm()
    {
        return view('auth.customer-register');
    }

}
