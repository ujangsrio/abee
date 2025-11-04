<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MultiAuthController extends Controller
{
    // Tampilkan form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses login untuk admin & customer
    public function login(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan dalam pengisian form.');
        }

        $credentials = $request->only('email', 'password');

        // Login sebagai admin
        if (Auth::guard('admin')->attempt($credentials)) {
            $user = Auth::guard('admin')->user();
            if ($user->role === 'admin') {
                $request->session()->regenerate();
                return redirect()->intended('/admin')
                    ->with('success', 'Login berhasil! Selamat datang Admin.');
            } else {
                Auth::guard('admin')->logout();
            }
        }

        // Login sebagai customer
        if (Auth::guard('customer')->attempt($credentials)) {
            $user = Auth::guard('customer')->user();
            if ($user->role === 'customer') {
                $request->session()->regenerate();
                return redirect()->intended('/customer/dashboard')
                    ->with('success', 'Login berhasil! Selamat datang di ArethaBeauty.');
            } else {
                Auth::guard('customer')->logout();
            }
        }

        // Jika semua attempt gagal
        return back()
            ->withErrors([
                'email' => 'Email atau password salah.',
            ])
            ->withInput()
            ->with('error', 'Login gagal. Periksa kembali email dan password Anda.');
    }

    // Proses logout
    public function logout(Request $request)
    {
        $user = Auth::user();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')
            ->with('success', $user ? 'Logout berhasil. Sampai jumpa!' : 'Logout berhasil.');
    }

    // Tampilkan form register untuk customer
    public function showCustomerRegisterForm()
    {
        return view('auth.customer-register');
    }

    // Proses register customer
    public function customerRegister(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'whatsapp' => 'required|string|max:20',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Pendaftaran gagal. Periksa kembali data Anda.');
        }

        try {
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

            // Login langsung
            Auth::guard('customer')->login($user);

            return redirect()->intended('/customer/dashboard')
                ->with('success', 'Pendaftaran berhasil! Selamat datang di ArethaBeauty.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }
}
