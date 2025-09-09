<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerMembership;
use Illuminate\Support\Facades\Auth;

class DetailAkunController extends Controller
{
    // Tampilkan detail akun
    public function index()
    {
        $user = Auth::guard('customer')->user()->load('customer');

        if (!$user || !$user->customer) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $customer = $user->customer;

        $membership = CustomerMembership::where('customer_id', $customer->id)->first();

        return view('customer.akun.index', compact('customer', 'membership'));
    }

    // Tampilkan form pendaftaran membership
    public function showMembershipForm()
    {
        $user = Auth::guard('customer')->user()->load('customer');

        if (!$user || !$user->customer) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $customer = $user->customer;

        return view('customer.akun.membership_form', compact('customer'));
    }

    // Proses pendaftaran membership
    public function registerMembership(Request $request)
    {
        $user = Auth::guard('customer')->user()->load('customer');

        if (!$user || !$user->customer) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $request->validate([
            'agree' => 'required|accepted'
        ]);

        $customer = $user->customer;
        $customer->is_member = true;
        $customer->save();

        return redirect()->route('customer.akun.index')->with('success', 'Berhasil daftar membership!');
    }
}
