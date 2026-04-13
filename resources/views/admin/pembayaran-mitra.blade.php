@extends('layouts.admin')

@section('title', 'Pembayaran Mitra')

@section('content')

<div class="card" style="display:block; padding: 4rem; text-align: center;">
    <div style="width:64px;height:64px;background:#f1f5f9;border-radius:16px;display:flex;align-items:center;justify-content:center;margin: 0 auto 1.5rem;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#94a3b8" style="width:32px;height:32px;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
        </svg>
    </div>
    <h3 class="fw-bold-700 mb-2" style="color: var(--text-main);">Belum Ada Transaksi</h3>
    <p class="text-muted" style="font-size: 1rem;">Data pembayaran mitra akan muncul di sini setelah kasir mencatat transaksi.</p>
</div>

@endsection
