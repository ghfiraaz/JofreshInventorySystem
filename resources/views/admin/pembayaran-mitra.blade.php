@extends('layouts.admin')

@section('title', 'Pembayaran Mitra')

@section('content')

<div class="card block p-16 text-center">
    <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#94a3b8" class="w-8 h-8">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
        </svg>
    </div>
    <h3 class="font-bold text-xl mb-2 text-slate-800">Belum Ada Transaksi</h3>
    <p class="text-slate-600 text-base">Data pembayaran mitra akan muncul di sini setelah kasir mencatat transaksi.</p>
</div>

@endsection
