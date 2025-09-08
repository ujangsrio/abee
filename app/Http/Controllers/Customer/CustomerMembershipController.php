<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\CustomerMembership;

class CustomerMembershipController extends Controller
{
    public function create()
    {
        return view('customer.akun.membership');
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'whatsapp' => 'required|string|max:15',
    ]);

    // Ambil user & customer
    $user = Auth::guard('customer')->user();
$customer = \App\Models\Customer::where('user_id', $user->id)->first();

if (!$customer) {
    return back()->with('error', 'Customer tidak ditemukan.');
}

$validated['customer_id'] = $customer->id;


    // Generate kode unik
    do {
        $generatedCode = 'MB' . now()->format('Ymd') . rand(1000, 9999);
    } while (CustomerMembership::where('member_code', $generatedCode)->exists());

    // Tambahkan data
    $validated['member_code'] = $generatedCode;
    $validated['expired_at'] = Carbon::now('Asia/Jakarta')->addMonths(3);
    $validated['customer_id'] = $customer->id;

    CustomerMembership::create($validated);

    // Update data customer
    $customer->is_member = true;
    $customer->kode_member = $generatedCode;
    $customer->save();

    return redirect()
        ->route('customer.akun.index')
        ->with('success', 'Membership berhasil didaftarkan!');
}

}