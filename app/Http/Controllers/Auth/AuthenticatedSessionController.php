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
    $request->authenticate(); // Validasi kredensial

    $request->session()->regenerate();

    $user = Auth::user();

    // Cek role atau redirect berdasarkan data user
    if ($user->role === 'admin') {
        return redirect()->intended('/admin/dashboard');
    } elseif ($user->role === 'customer') {
        return redirect()->intended('/customer/dashboard');
    } else {
        Auth::logout(); // role tidak valid
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

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
