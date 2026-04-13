<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;

class TransaksiController extends Controller
{
    public function index()
    {
        $transactions = Transaksi::with('user')->orderBy('created_at', 'desc')->get();
        return view('transactions', compact('transactions'));
    }
}
