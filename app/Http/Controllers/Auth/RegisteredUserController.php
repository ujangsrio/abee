<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        // ============================
        // 1. VALIDASI INPUT
        // ============================
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:customers,email',
            ],

            'whatsapp' => [
                'required',
                'string',
                'regex:/^(?:\+62|62|0)8\d{8,11}$/',
                'unique:customers,whatsapp',
            ],

            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [

            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'whatsapp.regex' => 'Format nomor WhatsApp tidak valid. Gunakan format 08xxxx atau +628xxxx.',
            'whatsapp.unique' => 'Nomor WhatsApp sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        if ($validator->fails()) {
            return redirect('/customer/register')
                ->withErrors($validator)
                ->withInput();
        }

        // ============================
        // 2. SIMPAN CUSTOMER
        // ============================
        $customer = Customer::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'whatsapp' => $request->whatsapp,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($customer));

        // ============================
        // 3. LOGIN (HANYA DI PRODUCTION)
        // ============================
        if (!app()->environment('testing')) {
            Auth::login($customer);
        }

        // ============================
        // 4. REDIRECT KE DASHBOARD
        // ============================
        return redirect('/customer/dashboard');
    }
}
