@extends(Auth::user()->role === 'Admin' ? 'layouts.admin' : 'layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div class="relative w-1/3 min-w-[250px]">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
        </svg>
        <input type="text" class="w-full pl-11 pr-4 py-2.5 bg-white border border-blue-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-300 focus:border-blue-900 transition-all text-sm" placeholder="Cari transaksi...">
    </div>
    <div class="flex items-center text-[0.95rem]">
        <label class="text-slate-600 font-medium">Periode:</label>
        <select class="ml-3 px-4 py-2.5 border border-blue-200 bg-white rounded-xl outline-none cursor-pointer shadow-sm text-sm hover:border-blue-300 transition-colors">
            <option>Semua</option>
            <option>1 Bulan Terakhir</option>
            <option>1 Minggu Terakhir</option>
        </select>
    </div>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>No. Referensi</th>
                <th>Tanggal</th>
                <th>Tipe</th>
                <th>Total Nominal</th>
                <th>Kasir</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $trx)
            <tr>
                <td class="font-bold">{{ $trx->no_transaksi }}</td>
                <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                <td><span class="bg-green-100 text-green-700 px-3 py-1.5 rounded-full text-xs font-semibold">{{ $trx->total_item }} Item</span></td>
                <td>Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</td>
                <td>{{ $trx->user->name ?? 'Kasir' }}</td>
                <td>
                    <button class="p-2 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors cursor-pointer border-none bg-transparent">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[18px] h-[18px]">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 2rem;">Belum ada riwayat transaksi yang tersimpan di database.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
