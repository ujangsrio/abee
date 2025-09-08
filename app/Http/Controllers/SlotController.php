<?php

namespace App\Http\Controllers;

use App\Models\Slot;
use Illuminate\Http\Request;

class SlotController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'layanan_id' => 'required|exists:layanans,id',
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i',
        ]);

        Slot::create([
            'layanan_id' => $request->layanan_id,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
        ]);

        return back()->with('success', 'Slot berhasil ditambahkan.');
    }
}
