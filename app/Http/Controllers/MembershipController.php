<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Membership;
use App\Models\User;
use App\Models\Pelanggan;
use App\Models\CustomerMembership;
use Carbon\Carbon;

class MembershipController extends Controller
{
    public function index()
    {
        $memberships = Membership::all();

        $pelanggansWithMembership = Pelanggan::with('membership')
            ->whereNotNull('membership_id')
            ->get();

        $usersWithMembership = User::with('membership')
            ->whereNotNull('membership_id')
            ->get();

        $customermemberships = CustomerMembership::with('customer')->get();

        return view('admin.membership.index', compact(
            'memberships',
            'pelanggansWithMembership',
            'usersWithMembership',
            'customermemberships'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
        ]);

        Membership::create([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
        ]);

        return redirect()->route('membership.index')->with('success', 'Membership berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $membership = Membership::findOrFail($id);
        return view('membership.edit', compact('membership'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric',
        ]);

        $membership = Membership::findOrFail($id);

        $membership->update([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
        ]);

        return redirect()->route('membership.index')->with('success', 'Data berhasil diperbarui.');
    }

    // ✅ Tambahan Fitur Input Manual Pelanggan Membership
   public function createPelanggan()
    {
        $kodeMembership = 'MBR-' . strtoupper(Str::random(6)); // contoh: MBR-X8F7KZ
        $tanggalExpired = Carbon::now()->addMonths(3)->format('Y-m-d'); // 3 bulan dari hari ini

        return view('membership.create-pelanggan', compact('kodeMembership', 'tanggalExpired'));
    }

    public function storePelanggan(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:20',
            'member_code' => 'required|string|unique:customermemberships,member_code',
            'expired_at' => 'required|date',
        ]);

        CustomerMembership::create([
            'name' => $request->name,
            'whatsapp' => $request->whatsapp,
            'member_code' => $request->member_code,
            'expired_at' => $request->expired_at,
        ]);

        return redirect()->route('membership.index')->with('success', 'Pelanggan membership berhasil ditambahkan.');
    }
}