<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerBooking;

class HistoriLayananController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');

        // Ambil semua data, filter status jika dipilih
        $query = CustomerBooking::query();

        if ($status) {
            $query->where('status', $status);
        }

        $histories = $query->with('service')->orderBy('created_at', 'desc')->get();

        return view('admin.histori.index', compact('histories', 'status'));
    }
}

