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

    public function update(Request $request, $id)
    {
        $user = Auth::guard('customer')->user();
        $customer = $user->customer;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'old_password' => 'nullable|string',
            'password' => 'nullable|min:6|confirmed',
        ]);

        // Update data dari tabel users
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            if (!Hash::check($request->old_password, $user->password)) {
                return back()->withErrors(['old_password' => 'Password lama tidak cocok']);
            }
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Update data dari tabel customers
        $customer->whatsapp = $request->whatsapp;

        if ($request->hasFile('photo')) {
            if ($customer->photo && Storage::disk('public')->exists($customer->photo)) {
                Storage::disk('public')->delete($customer->photo);
            }

            $filename = $request->file('photo')->store('photos', 'public');
            $customer->photo = $filename;
        }

        $customer->save();

        return redirect()->route('customer.akun.index')->with('success', 'Profil berhasil diperbarui.');
    }
}
