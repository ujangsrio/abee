<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string|max:500',
            'kategori' => 'required|string|max:100',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            // Upload gambar jika ada - PERBAIKAN: gunakan directory gambar_layanan
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('gambar_layanan', $filename, 'public');
                $validated['gambar'] = $path; // Simpan path relatif
            }

            Layanan::create($validated);

            return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error storing layanan: ' . $e->getMessage());
            return back()->with('error', 'Gagal menambahkan layanan: ' . $e->getMessage());
        }
    }

    public function edit(Layanan $layanan)
    {
        $promos = Promo::all();
        $layanan->load('slots');
        return view('admin.layanan.edit', compact('layanan', 'promos'));
    }

    public function update(Request $request, $id)
    {
        $layanan = Layanan::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string|max:500',
            'kategori' => 'required|string|max:100',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            // Hapus gambar lama jika ada gambar baru - PERBAIKAN: gunakan directory gambar_layanan
            if ($request->hasFile('gambar')) {
                // Hapus gambar lama jika ada
                if ($layanan->gambar && Storage::disk('public')->exists($layanan->gambar)) {
                    Storage::disk('public')->delete($layanan->gambar);
                    Log::info('Gambar layanan lama dihapus: ' . $layanan->gambar);
                }

                // Upload gambar baru
                $file = $request->file('gambar');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('gambar_layanan', $filename, 'public'); // Update directory
                $validated['gambar'] = $path;
                Log::info('Gambar layanan baru diupload: ' . $path);
            }

            $layanan->update($validated);

            return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating layanan: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui layanan: ' . $e->getMessage());
        }
    }

    public function destroy(Layanan $layanan)
    {
        try {
            // Hapus gambar jika ada - PERBAIKAN: gunakan directory gambar_layanan
            if ($layanan->gambar && Storage::disk('public')->exists($layanan->gambar)) {
                Storage::disk('public')->delete($layanan->gambar);
                Log::info('Gambar layanan dihapus saat destroy: ' . $layanan->gambar);
            }

            $layanan->slots()->delete();
            $layanan->delete();

            return redirect()->route('admin.layanan.index')
                ->with('success', 'Layanan berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error in destroy layanan: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get image URL for display
     */
    public function getImageUrl($gambarPath)
    {
        if (!$gambarPath) {
            return null;
        }

        return asset('storage/' . $gambarPath);
    }
}
