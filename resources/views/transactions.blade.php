@extends(Auth::user()->role === 'Admin' ? 'layouts.admin' : 'layouts.app')
@section('title', 'Riwayat Transaksi')
@section('content')

<style>
.trx-cal-wrap{position:relative;display:inline-block;}
.trx-cal-trigger{display:flex;align-items:center;gap:8px;padding:8px 16px;border-radius:12px;cursor:pointer;background:linear-gradient(135deg,#e0e7ff,#f0fdf4);border:1.5px solid #a5b4fc;color:#3730a3;font-weight:600;font-size:.93rem;transition:all .18s;user-select:none;box-shadow:0 2px 8px rgba(99,102,241,.08);}
.trx-cal-trigger:hover{border-color:#6366f1;background:linear-gradient(135deg,#c7d2fe,#dcfce7);}
.trx-cal-popup{position:absolute;top:calc(100% + 8px);right:0;z-index:200;background:#fff;border-radius:18px;box-shadow:0 12px 40px rgba(99,102,241,.16);border:1.5px solid #e0e7ff;width:308px;overflow:hidden;animation:tCalSlide .18s ease;}
@keyframes tCalSlide{from{opacity:0;transform:translateY(-8px);}to{opacity:1;transform:translateY(0);}}
.trx-cal-hdr{display:flex;align-items:center;justify-content:space-between;padding:14px 16px 10px;background:linear-gradient(135deg,#4f46e5,#6366f1);}
.trx-cal-nav{background:rgba(255,255,255,.18);border:none;border-radius:8px;width:32px;height:32px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#fff;transition:background .15s;}
.trx-cal-nav:hover{background:rgba(255,255,255,.32);}
.trx-cal-title{background:transparent;border:none;color:#fff;font-weight:700;font-size:1rem;cursor:pointer;padding:4px 10px;border-radius:8px;transition:background .15s;}
.trx-cal-title:hover{background:rgba(255,255,255,.18);}
.trx-cal-wdays{display:grid;grid-template-columns:repeat(7,1fr);padding:8px 12px 4px;}
.trx-cal-wdays span{text-align:center;font-size:.72rem;font-weight:700;color:#6366f1;text-transform:uppercase;letter-spacing:.04em;}
.trx-cal-days{display:grid;grid-template-columns:repeat(7,1fr);gap:2px;padding:4px 12px 12px;}
.trx-cal-day{aspect-ratio:1;display:flex;align-items:center;justify-content:center;border-radius:10px;font-size:.85rem;font-weight:500;cursor:pointer;color:#374151;transition:all .15s;border:none;background:transparent;}
.trx-cal-day:hover{background:#e0e7ff;color:#3730a3;}
.trx-cal-day.today{background:#f0fdf4;color:#16a34a;font-weight:700;border:1.5px solid #86efac;}
.trx-cal-day.selected{background:linear-gradient(135deg,#4f46e5,#6366f1);color:#fff!important;font-weight:700;box-shadow:0 2px 8px rgba(99,102,241,.3);}
.trx-cal-day.other-month{color:#d1d5db;}
.trx-cal-grid3{display:grid;grid-template-columns:repeat(3,1fr);gap:6px;padding:12px 14px 14px;}
.trx-cal-grid3 button{padding:10px 4px;border:none;border-radius:10px;font-size:.82rem;font-weight:600;cursor:pointer;color:#374151;background:transparent;transition:all .15s;}
.trx-cal-grid3 button:hover{background:#e0e7ff;color:#3730a3;}
.trx-cal-grid3 button.active{background:linear-gradient(135deg,#4f46e5,#6366f1);color:#fff;box-shadow:0 2px 8px rgba(99,102,241,.3);}
#trx-detail-modal{position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:300;display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity .2s;}
#trx-detail-modal.active{opacity:1;pointer-events:auto;}
#trx-detail-box{background:#fff;border-radius:20px;width:100%;max-width:520px;margin:16px;box-shadow:0 24px 60px rgba(0,0,0,.18);overflow:hidden;transform:scale(.96);transition:transform .2s;}
#trx-detail-modal.active #trx-detail-box{transform:scale(1);}
</style>

{{-- Top Bar --}}
<div class="flex flex-wrap justify-between items-center gap-4 mb-6">
    <div class="relative w-full max-w-xs">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
        <input type="text" id="trx-search" placeholder="Cari no. transaksi / kasir..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-blue-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 transition-all">
    </div>
    <div class="flex items-center gap-3">
        @php $trxUrl = Auth::user()->role === 'Admin' ? url('/admin/transactions') : url('/transactions'); @endphp
        <form method="GET" action="{{ $trxUrl }}" id="trx-filter-form">
            <input type="hidden" name="filter_date" id="trx-hidden-date" value="{{ request('filter_date','') }}">
        </form>
        <div class="trx-cal-wrap" id="trx-cal-wrap">
            <button class="trx-cal-trigger" id="trx-cal-trigger" type="button">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                <span id="trx-trigger-lbl">{{ request('filter_date') ? \Carbon\Carbon::parse(request('filter_date'))->translatedFormat('d M Y') : 'Pilih Tanggal' }}</span>
                <svg class="w-3 h-3 opacity-60" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
            </button>
            <div class="trx-cal-popup hidden" id="trx-cal-popup">
                <div class="trx-cal-hdr">
                    <button class="trx-cal-nav" id="trx-cal-prev" type="button"><svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg></button>
                    <button class="trx-cal-title" id="trx-cal-titlebtn" type="button"></button>
                    <button class="trx-cal-nav" id="trx-cal-next" type="button"><svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg></button>
                </div>
                <div id="trx-cal-body"></div>
            </div>
        </div>
        @if(request('filter_date'))
        <div class="flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg" style="background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;">
            <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
            {{ \Carbon\Carbon::parse(request('filter_date'))->translatedFormat('d M Y') }}
            <a href="{{ $trxUrl }}" class="ml-1 text-rose-500 hover:text-rose-700 no-underline font-bold">×</a>
        </div>
        @endif
    </div>
</div>

{{-- Table --}}
<div class="rounded-2xl overflow-hidden border border-indigo-100 shadow-sm">
    <table class="w-full" id="trx-table">
        <thead>
            <tr style="background:linear-gradient(135deg,#eef2ff,#f0fdf4);">
                <th class="py-3.5 px-5 text-left text-xs font-bold uppercase tracking-wider text-indigo-500">Tanggal</th>
                <th class="py-3.5 px-5 text-left text-xs font-bold uppercase tracking-wider text-indigo-500">No. Transaksi</th>
                <th class="py-3.5 px-5 text-left text-xs font-bold uppercase tracking-wider text-indigo-500">Mitra</th>
                <th class="py-3.5 px-5 text-left text-xs font-bold uppercase tracking-wider text-indigo-500">Item</th>
                <th class="py-3.5 px-5 text-left text-xs font-bold uppercase tracking-wider text-indigo-500">Status</th>
                <th class="py-3.5 px-5 text-left text-xs font-bold uppercase tracking-wider text-indigo-500">Total</th>
                <th class="py-3.5 px-5 text-center text-xs font-bold uppercase tracking-wider text-indigo-500">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-slate-100">
            @php $rowColors=['bg-white','bg-blue-50/40','bg-purple-50/40','bg-green-50/30','bg-amber-50/30']; $ri=0; @endphp
            @forelse($transactions as $trx)
            @php
                $rowBg=$rowColors[$ri%count($rowColors)]; $ri++;
                $itemsJson = json_encode($trx->items->map(function($i){
                    return ['nama'=>$i->nama_produk,'qty'=>$i->jumlah,'harga'=>$i->harga_satuan,'subtotal'=>$i->subtotal];
                })->values());
            @endphp
            <tr class="trx-row {{ $rowBg }} hover:bg-indigo-50/60 transition-colors"
                data-search="{{ strtolower($trx->no_transaksi.' '.($trx->user->name??'').' '.($trx->mitra->nama??'')) }}"
                data-no="{{ $trx->no_transaksi }}"
                data-tanggal="{{ $trx->created_at->format('d/m/Y H:i') }}"
                data-mitra="{{ $trx->mitra->nama ?? '-' }}"
                data-mitra-alamat="{{ $trx->mitra->alamat ?? '-' }}"
                data-mitra-kontak="{{ $trx->mitra->kontak ?? '-' }}"
                data-kasir="{{ $trx->user->name ?? 'Kasir' }}"
                data-status="{{ $trx->status_pembayaran }}"
                data-total="Rp {{ number_format($trx->total_harga,0,',','.') }}"
                data-items='{{ $itemsJson }}'>
                <td class="py-3.5 px-5 text-sm text-slate-600 whitespace-nowrap">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                <td class="py-3.5 px-5"><span class="font-bold text-indigo-700 text-sm">{{ $trx->no_transaksi }}</span></td>
                <td class="py-3.5 px-5 text-sm text-slate-600">{{ $trx->mitra->nama ?? '-' }}</td>
                <td class="py-3.5 px-5"><span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="background:#e0e7ff;color:#4338ca;">{{ $trx->total_item }} item</span></td>
                <td class="py-3.5 px-5">
                    @if($trx->status_pembayaran === 'Sudah Dibayar')
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="background:#dcfce7;color:#15803d;">Lunas</span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="background:#fef9c3;color:#b45309;">Belum Dibayar</span>
                    @endif
                </td>
                <td class="py-3.5 px-5 text-sm font-bold" style="color:#15803d;">Rp {{ number_format($trx->total_harga,0,',','.') }}</td>
                <td class="py-3.5 px-5 text-center">
                    <button onclick="showTrxDetail(this.closest('tr'))" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors cursor-pointer border-none bg-transparent" title="Detail Transaksi">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[18px] h-[18px]"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                    </button>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="py-16 text-center text-slate-400 text-sm">Belum ada riwayat transaksi.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Detail Modal --}}
<div id="trx-detail-modal">
    <div id="trx-detail-box">
        <div id="trx-detail-content"></div>
    </div>
</div>

<script>
document.getElementById('trx-search').addEventListener('input',function(){
    const q=this.value.toLowerCase();
    document.querySelectorAll('.trx-row').forEach(r=>{r.style.display=r.dataset.search.includes(q)?'':'none';});
});
document.getElementById('trx-detail-modal').addEventListener('click',function(e){ if(e.target===this) this.classList.remove('active'); });

function showTrxDetail(row){
    const no=row.dataset.no, tgl=row.dataset.tanggal, mitra=row.dataset.mitra;
    const kasir=row.dataset.kasir, status=row.dataset.status, total=row.dataset.total;
    const mitraAlamat=row.dataset.mitraAlamat, mitraKontak=row.dataset.mitraKontak;
    const items=JSON.parse(row.dataset.items||'[]');
    const isPaid=status==='Sudah Dibayar';
    const statusBadge=isPaid?'<span style="background:#dcfce7;color:#15803d;" class="px-3 py-1 rounded-full text-xs font-bold">Lunas</span>':'<span style="background:#fef9c3;color:#b45309;" class="px-3 py-1 rounded-full text-xs font-bold">Belum Dibayar</span>';
    const fmt=n=>new Intl.NumberFormat('id-ID').format(n);
    let itemsHtml=items.map(i=>`<tr><td class="py-2 text-sm text-slate-700">${i.nama}</td><td class="py-2 text-sm text-slate-600 text-center">${i.qty}</td><td class="py-2 text-sm text-slate-600 text-right">Rp ${fmt(i.harga)}</td><td class="py-2 text-sm font-semibold text-right" style="color:#15803d;">Rp ${fmt(i.subtotal)}</td></tr>`).join('');

    document.getElementById('trx-detail-content').innerHTML=`
        <div style="background:linear-gradient(135deg,#4f46e5,#6366f1);" class="px-6 py-5 flex justify-between items-start">
            <div>
                <div class="text-indigo-200 text-xs font-bold uppercase tracking-wider mb-1">Detail Transaksi</div>
                <div class="text-white font-black text-lg">${no}</div>
                <div class="text-indigo-200 text-xs mt-1">${tgl}</div>
            </div>
            <button onclick="document.getElementById('trx-detail-modal').classList.remove('active')" class="text-white/70 hover:text-white text-2xl font-bold bg-transparent border-none cursor-pointer leading-none">&times;</button>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 gap-3 mb-5">
                <div class="bg-slate-50 rounded-xl p-3.5"><div class="text-xs text-slate-400 font-semibold mb-1">Mitra</div><div class="font-bold text-slate-800 text-sm">${mitra}</div></div>
                <div class="bg-slate-50 rounded-xl p-3.5"><div class="text-xs text-slate-400 font-semibold mb-1">Kasir</div><div class="font-bold text-slate-800 text-sm">${kasir}</div></div>
                <div class="bg-slate-50 rounded-xl p-3.5"><div class="text-xs text-slate-400 font-semibold mb-1">Status</div><div class="mt-1">${statusBadge}</div></div>
                <div class="bg-slate-50 rounded-xl p-3.5"><div class="text-xs text-slate-400 font-semibold mb-1">Alamat Mitra</div><div class="font-medium text-slate-700 text-sm">${mitraAlamat}</div></div>
            </div>
            <div class="mb-5">
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Item Produk</div>
                <div class="rounded-xl border border-slate-100 overflow-hidden">
                    <table class="w-full">
                        <thead><tr style="background:#f8fafc;"><th class="py-2 px-3 text-left text-xs font-bold text-slate-400">Produk</th><th class="py-2 px-3 text-center text-xs font-bold text-slate-400">Qty</th><th class="py-2 px-3 text-right text-xs font-bold text-slate-400">Harga</th><th class="py-2 px-3 text-right text-xs font-bold text-slate-400">Subtotal</th></tr></thead>
                        <tbody class="divide-y divide-slate-50">${itemsHtml}</tbody>
                    </table>
                </div>
            </div>
            <div class="flex justify-between items-center bg-indigo-50 rounded-xl px-5 py-4 border border-indigo-100">
                <span class="font-bold text-slate-700">Total</span>
                <span class="font-black text-xl" style="color:#4f46e5;">${total}</span>
            </div>
            <div class="mt-4 flex justify-end">
                <button onclick="document.getElementById('trx-detail-modal').classList.remove('active')" class="px-6 py-2.5 rounded-xl font-semibold text-slate-500 bg-slate-100 hover:bg-slate-200 text-sm border-none cursor-pointer transition-all">Tutup</button>
            </div>
        </div>`;
    document.getElementById('trx-detail-modal').classList.add('active');
}

// Calendar
(function(){
    const MID=['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const MS=['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    const DS=['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
    let mode='days',today=new Date(),cY=today.getFullYear(),cM=today.getMonth(),sel=null;
    const preset='{{ request("filter_date","") }}';
    if(preset){const p=new Date(preset+'T00:00:00');sel={y:p.getFullYear(),m:p.getMonth(),d:p.getDate()};cY=sel.y;cM=sel.m;}
    const trigger=document.getElementById('trx-cal-trigger'),popup=document.getElementById('trx-cal-popup'),titleBtn=document.getElementById('trx-cal-titlebtn'),prevBtn=document.getElementById('trx-cal-prev'),nextBtn=document.getElementById('trx-cal-next'),body=document.getElementById('trx-cal-body'),wrap=document.getElementById('trx-cal-wrap'),hidden=document.getElementById('trx-hidden-date'),form=document.getElementById('trx-filter-form'),lbl=document.getElementById('trx-trigger-lbl');
    function render(){mode==='days'?rDays():mode==='months'?rMonths():rYears();}
    function rDays(){
        titleBtn.textContent=MID[cM]+' '+cY;body.innerHTML='';
        const wd=document.createElement('div');wd.className='trx-cal-wdays';
        DS.forEach(d=>{const s=document.createElement('span');s.textContent=d;wd.appendChild(s);});body.appendChild(wd);
        const g=document.createElement('div');g.className='trx-cal-days';
        const fd=new Date(cY,cM,1).getDay(),dim=new Date(cY,cM+1,0).getDate(),dip=new Date(cY,cM,0).getDate();
        for(let i=fd-1;i>=0;i--){g.appendChild(mkD(dip-i,'other-month',true));}
        for(let d=1;d<=dim;d++){
            const it=d===today.getDate()&&cM===today.getMonth()&&cY===today.getFullYear();
            const is2=sel&&sel.y===cY&&sel.m===cM&&sel.d===d;
            g.appendChild(mkD(d,(it?'today ':'')+(is2?'selected':''),false,d));
        }
        const tr=(fd+dim)%7===0?0:7-(fd+dim)%7;
        for(let i=1;i<=tr;i++){g.appendChild(mkD(i,'other-month',true));}
        body.appendChild(g);
    }
    function mkD(n,cls,dis,dn){
        const b=document.createElement('button');b.type='button';b.className='trx-cal-day '+cls;b.textContent=n;
        if(!dis&&dn){b.onclick=()=>{sel={y:cY,m:cM,d:dn};const dd=String(dn).padStart(2,'0'),mm=String(cM+1).padStart(2,'0');hidden.value=`${cY}-${mm}-${dd}`;lbl.textContent=`${dd} ${MS[cM]} ${cY}`;popup.classList.add('hidden');form.submit();};}
        return b;
    }
    function rMonths(){
        titleBtn.textContent=String(cY);body.innerHTML='';const g=document.createElement('div');g.className='trx-cal-grid3';
        MS.forEach((m,i)=>{const b=document.createElement('button');b.type='button';b.textContent=m;b.className=sel&&sel.y===cY&&sel.m===i?'active':'';b.onclick=()=>{cM=i;mode='days';render();};g.appendChild(b);});body.appendChild(g);
    }
    function rYears(){
        const sy=Math.floor(cY/12)*12;titleBtn.textContent=`${sy} – ${sy+11}`;body.innerHTML='';const g=document.createElement('div');g.className='trx-cal-grid3';
        for(let y=sy;y<sy+12;y++){const b=document.createElement('button');b.type='button';b.textContent=y;b.className=y===cY?'active':'';b.onclick=()=>{cY=y;mode='months';render();};g.appendChild(b);}body.appendChild(g);
    }
    titleBtn.addEventListener('click',()=>{mode=mode==='days'?'months':mode==='months'?'years':'years';render();});
    prevBtn.addEventListener('click',()=>{if(mode==='days'){cM--;if(cM<0){cM=11;cY--;}}else if(mode==='months')cY--;else cY-=12;render();});
    nextBtn.addEventListener('click',()=>{if(mode==='days'){cM++;if(cM>11){cM=0;cY++;}}else if(mode==='months')cY++;else cY+=12;render();});
    trigger.addEventListener('click',e=>{e.stopPropagation();const h=popup.classList.contains('hidden');popup.classList.toggle('hidden');if(h){mode='days';render();}});
    document.addEventListener('click',e=>{if(!wrap.contains(e.target))popup.classList.add('hidden');});
    render();
})();
</script>
@endsection
