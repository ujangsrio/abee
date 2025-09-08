<?php

namespace App\Http\Controllers;

use App\Models\Customer;

class PelangganController extends Controller
{
    public function index()
    {
        $pelanggan = Customer::with('user')->get();

        return view('admin.pelanggan.index', compact('pelanggan'));
    }
}
