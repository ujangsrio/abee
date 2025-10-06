<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        // Autentikasi kredensial
        $request->authenticate();

        // Regenerasi sesi untuk keamanan
        $request->session()->regenerate();

        // Ambil data user yang sedang login
        $user = Auth::user();

        // Cek role user dan arahkan ke halaman yang sesuai
        if ($user->role === 'admin') {
            // 🔹 Arahkan admin ke dashboard Filament
            return redirect()->intended('/panel-admin');
        } elseif ($user->role === 'customer') {
            // 🔹 Arahkan customer ke dashboard lama
            return redirect()->intended('/customer/dashboard');
        } else {
            // Jika role tidak dikenali, logout dan beri pesan error
            Auth::logout();
            return redirect('/login')->withErrors([
                'email' => 'Akun tidak memiliki role yang valid.',
            ]);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
