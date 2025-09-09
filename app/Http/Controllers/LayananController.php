<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Promo;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    public function index()
    {
        $layanans = Layanan::with(['promo', 'slots'])->get();
        return view('admin.layanan.index', compact('layanans'));
    }

    public function create()
    {
        $promos = Promo::all();
        return view('admin.layanan.create', compact('promos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric',
            'tanggal' => 'required|date|after_or_equal:today',
            'deskripsi' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'promo_id' => 'nullable|exists:promos,id',
            'slots' => 'required|array|min:1',
            'slots.*' => 'required|date_format:H:i',
        ]);

        $gambar = null;
        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar')->store( 'photos', 'public');
        }

        $layanan = Layanan::create([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'tanggal' => $request->tanggal,
            'deskripsi' => $request->deskripsi,
            'gambar' => $gambar ? basename($gambar) : null,
            'promo_id' => $request->promo_id,
        ]);

        foreach ($request->slots as $jam) {
            $layanan->slots()->create([
                'jam' => $jam,
                'tanggal' => $layanan->tanggal, // fix: wajib ada
            ]);
        }

        return redirect()->route('admin.layanan.index')->with('success', 'Layanan dan slot berhasil ditambahkan.');
    }

    public function edit(Layanan $layanan)
    {
        $promos = Promo::all();
        $layanan->load('slots');
        return view('admin.layanan.edit', compact('layanan', 'promos'));
    }

    public function update(Request $request, Layanan $layanan)
    {
        $request->merge([
            'slots' => array_map(fn($jam) => substr($jam, 0, 5), $request->slots),
        ]);

        $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric',
            'tanggal' => 'required|date|after_or_equal:today',
            'deskripsi' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'promo_id' => 'nullable|exists:promos,id',
            'slots' => 'required|array|min:1',
            'slots.*' => ['required', 'date_format:H:i'],
            'slot_ids' => 'nullable|array',
        ]);

        $layanan->update([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'tanggal' => $request->tanggal,
            'deskripsi' => $request->deskripsi,
            'promo_id' => $request->promo_id,
        ]);

        // Gambar baru (opsional)
        if ($request->hasFile('gambar')) {
            $gambarBaru = $request->file('gambar')->store('photos', 'public');
            if ($layanan->gambar && file_exists(storage_path('app/public/photos/' . $layanan->gambar))) {
                unlink(storage_path('app/public/photos/' . $layanan->gambar));
            }
            $layanan->gambar = basename($gambarBaru);
            $layanan->save();
        }

        // Update Slot
        $slotIdsFromForm = $request->slot_ids ?? [];
        $newSlots = $request->slots;

        // Hapus slot yang tidak ada dalam form
        $layanan->slots()->whereNotIn('id', $slotIdsFromForm)->delete();

        foreach ($newSlots as $index => $jam) {
            $id = $slotIdsFromForm[$index] ?? null;
            if ($id) {
                // Update existing slot
                $slot = $layanan->slots()->where('id', $id)->first();
                if ($slot) {
                    $slot->update(['jam' => $jam, 'tanggal' => $layanan->tanggal]);
                }
            } else {
                // Tambah slot baru
                $layanan->slots()->create([
                    'jam' => $jam,
                    'tanggal' => $layanan->tanggal, // wajib
                ]);
            }
        }

        return redirect()->route('admin.layanan.index')->with('success', 'Layanan dan slot berhasil diperbarui.');
    }

    public function destroy(Layanan $layanan)
    {
        if ($layanan->gambar && file_exists(storage_path('app/public/photos/' . $layanan->gambar))) {
            unlink(storage_path('app/public/photos/' . $layanan->gambar));
        }

        // Hapus semua slot juga
        $layanan->slots()->delete();

        $layanan->delete();

        return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil dihapus.');
    }
}
