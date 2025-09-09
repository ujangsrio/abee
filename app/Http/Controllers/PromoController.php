<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Promo;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function index()
    {
        $promos = Promo::orderBy('tanggal_berakhir', 'asc')->get();
        return view('admin.promo.index', compact('promos'));
    }

    public function create()
    {
        return view('admin.promo.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_promo' => 'required|string',
            'deskripsi' => 'required|string',
            'diskon' => 'required|integer|min:1|max:100',
            'tanggal_berakhir' => 'required|date',
            'hanya_member' => 'sometimes|boolean',
        ]);

        Promo::create([
            'nama_promo' => $request->nama_promo,
            'deskripsi' => $request->deskripsi,
            'diskon' => $request->diskon,
            'tanggal_berakhir' => $request->tanggal_berakhir,
            'hanya_member' => $request->has('hanya_member'), // checkbox boolean
        ]);

        return redirect()->route('admin.promo.index')->with('success', 'Promo berhasil ditambahkan.');
    }



    public function edit(Promo $promo)
    {
        return view('admin.promo.edit', compact('promo'));
    }

    public function update(Request $request, Promo $promo)
    {
        $request->validate([
            'nama_promo' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_berakhir' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $promo->update([
            'nama_promo' => $request->nama_promo,
            'deskripsi' => $request->deskripsi,
            'diskon' => $request->diskon, // tambahkan jika diskon juga bisa diubah
            'tanggal_berakhir' => $request->tanggal_berakhir,
            'hanya_member' => $request->filled('hanya_member'), // ✅ ini paling aman dan akurat
        ]);

        return redirect()->route('admin.promo.index')->with('success', 'Promo berhasil diperbarui.');
    }

    public function destroy(Promo $promo)
    {
        $promo->delete();
        return redirect()->route('admin.promo.index')->with('success', 'Promo berhasil dihapus.');
    }

    public function show(Promo $promo)
    {
        return view('admin.promo.show', compact('promo'));
    }


}
