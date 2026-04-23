<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with(['user', 'mitra', 'items'])->orderBy('created_at', 'desc');

        if ($request->filled('filter_date')) {
            $query->whereDate('created_at', $request->filter_date);
        }

        $transactions = $query->get();
        return view('transactions', compact('transactions'));
    }
}
