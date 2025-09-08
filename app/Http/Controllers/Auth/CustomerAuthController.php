<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CustomerAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.customer-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('customer')->attempt($credentials)) {
            return redirect()->route('customer.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput();
    }

    public function showRegisterForm()
    {
        return view('auth.customer-register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'whatsapp' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'whatsapp' => $request->whatsapp,
            'password' => Hash::make($request->password),

        ]);

        Auth::guard('customer')->login($customer);

        return redirect()->route('customer.dashboard');
    }

    public function logout()
    {
        Auth::guard('customer')->logout();
        return redirect('/customer/login');
    }
}
