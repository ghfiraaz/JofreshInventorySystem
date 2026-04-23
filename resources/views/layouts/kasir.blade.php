<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>JIS Kasir - @yield('title')</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Vite CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex antialiased">
<div class="flex w-full min-h-screen">
    {{-- ===== SIDEBAR ===== --}}
    <aside id="sidebar" class="w-[260px] flex-shrink-0 bg-white flex flex-col z-10 border-r border-slate-200 shadow-[2px_0_8px_rgba(0,0,0,0.02)] transition-all duration-300 overflow-hidden">
        {{-- Logo --}}
        <div class="px-6 py-6 text-center border-b border-slate-100 flex justify-center">
            <div class="jis-box-sidebar">
                <h2>J I S</h2>
            </div>
        </div>

        {{-- User Profile --}}
        <div class="flex items-center px-6 py-7 border-b border-slate-100">
            <div class="w-11 h-11 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-[1.1rem] mr-4">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div>
                <div class="font-bold text-base truncate max-w-[130px]">{{ Auth::user()->name }}</div>
                <div class="text-slate-600 text-sm">Kasir</div>
            </div>
        </div>

        {{-- Navigation --}}
        <ul class="px-4 py-6 flex-grow flex flex-col gap-2" id="sidebar-menu">
            <a href="{{ url('/kasir/dashboard') }}" class="flex items-center px-5 py-3.5 rounded-xl font-medium transition-all duration-200 text-base {{ Request::is('kasir/dashboard') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800 hover:translate-x-1' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px] mr-3.5 transition-transform duration-400">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                </svg>
                Dashboard
            </a>
            
            <div class="mt-2">
                <button type="button" onclick="document.getElementById('transaksi-submenu').classList.toggle('hidden'); this.querySelector('.chevron').classList.toggle('rotate-180')" class="flex items-center justify-between w-full px-5 py-3.5 rounded-xl font-medium transition-all duration-200 text-base border-none outline-none cursor-pointer {{ Request::is('kasir/transaksi') || Request::is('kasir/tagihan') || Request::is('kasir/riwayat') ? 'bg-slate-100 text-slate-900 font-bold' : 'bg-transparent text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px] mr-3.5 transition-transform duration-400">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                        </svg>
                        Transaksi
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 chevron transition-transform duration-200 {{ Request::is('kasir/transaksi') || Request::is('kasir/tagihan') || Request::is('kasir/riwayat') ? 'rotate-180' : '' }}">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
                <div id="transaksi-submenu" class="ml-9 mt-1 flex flex-col gap-1 {{ Request::is('kasir/transaksi') || Request::is('kasir/tagihan') || Request::is('kasir/riwayat') ? '' : 'hidden' }}">
                    <a href="{{ url('/kasir/transaksi') }}" class="block px-4 py-2.5 rounded-lg text-sm font-medium transition-colors {{ Request::is('kasir/transaksi') ? 'bg-slate-200/50 text-slate-900' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' }}">
                        Input Transaksi
                    </a>
                    <a href="{{ url('/kasir/tagihan') }}" class="block px-4 py-2.5 rounded-lg text-sm font-medium transition-colors {{ Request::is('kasir/tagihan') ? 'bg-slate-200/50 text-slate-900' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' }}">
                        Belum Dibayar
                    </a>
                    <a href="{{ url('/kasir/riwayat') }}" class="block px-4 py-2.5 rounded-lg text-sm font-medium transition-colors {{ Request::is('kasir/riwayat') ? 'bg-slate-200/50 text-slate-900' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' }}">
                        Riwayat Transaksi
                    </a>
                </div>
            </div>
        </ul>

        {{-- Logout --}}
        <div class="px-5 pb-5 mt-auto">
            <form action="{{ url('/logout') }}" method="POST" id="form-logout">
                @csrf
                <button type="button" onclick="showLogoutModal()" class="flex items-center w-full px-5 py-3.5 bg-transparent border-none rounded-xl cursor-pointer font-medium text-base text-slate-500 transition-all duration-200 hover:bg-slate-50 hover:text-slate-800">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25" />
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- ===== MAIN CONTENT ===== --}}
    <main class="flex-grow flex flex-col w-full min-w-0 transition-all duration-300">
        {{-- Header --}}
        <header class="h-20 px-10 flex items-center justify-between bg-slate-50 flex-shrink-0">
            <div class="flex items-center gap-4">
                <button id="sidebar-toggle" class="p-2 -ml-2 rounded-lg text-slate-400 hover:bg-slate-200/50 hover:text-slate-800 transition-colors cursor-pointer border-none bg-transparent outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <h2 class="text-2xl font-bold text-slate-800">@yield('title')</h2>
            </div>
            <div id="header-date" class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 shadow-sm">
                <span id="hd-day" class="text-xs font-bold text-indigo-400 uppercase tracking-widest"></span>
                <span id="hd-date" class="text-sm font-bold text-slate-700"></span>
                <span class="w-px h-4 bg-blue-200"></span>
                <span id="hd-time" class="text-sm font-bold text-blue-600 tabular-nums" style="font-variant-numeric:tabular-nums;"></span>
            </div>
        </header>

        {{-- Content --}}
        <div class="p-10 flex-grow overflow-y-auto">
            @yield('content')
        </div>
    </main>
</div>

<!-- Logout Modal (Psychological UI) -->
<div id="modal-logout" class="fixed inset-0 bg-slate-900/50 z-[100] flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 modal-overlay font-sans [&.active]:opacity-100 [&.active]:pointer-events-auto">
    <div class="bg-white rounded-3xl p-8 max-w-sm w-full mx-4 shadow-2xl transform scale-95 transition-transform duration-300 modal-content relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-900 to-indigo-700"></div>
        <div class="text-center">
            <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-blue-900">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-slate-800 mb-2">Keluar Sistem?</h3>
            <p class="text-slate-600 mb-8">Apakah Anda yakin ingin mengakhiri sesi dan keluar dari aplikasi?</p>
            <div class="mt-8 flex flex-col gap-3">
                <button type="button" onclick="document.getElementById('modal-logout').classList.remove('active'); document.getElementById('modal-logout').querySelector('.modal-content').classList.remove('scale-100')" class="w-full px-6 py-3 rounded-xl font-bold text-white transition-all cursor-pointer border-none outline-none" style="background:#1e3a5f;" onmouseover="this.style.background='#162d4a'" onmouseout="this.style.background='#1e3a5f'">
                    Tidak, Tetap Disini
                </button>
                <button type="button" onclick="document.getElementById('form-logout').submit()" class="w-full px-6 py-3 rounded-xl font-bold bg-gray-100 text-gray-500 hover:bg-gray-200 transition-all cursor-pointer border-none outline-none">
                    Ya, Keluar
                </button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/main.js') }}?v={{ time() }}"></script>
<script>
    function showLogoutModal() {
        const modal = document.getElementById('modal-logout');
        modal.classList.add('active');
        setTimeout(() => {
            modal.querySelector('.modal-content').classList.add('scale-100');
        }, 10);
    }

    const days = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    function updateClock() {
        const d = new Date();
        const dayEl = document.getElementById('hd-day');
        const dateEl = document.getElementById('hd-date');
        const timeEl = document.getElementById('hd-time');
        if (dayEl) dayEl.textContent = days[d.getDay()];
        if (dateEl) dateEl.textContent = `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
        if (timeEl) {
            const hh = String(d.getHours()).padStart(2,'0');
            const mm = String(d.getMinutes()).padStart(2,'0');
            timeEl.textContent = `${hh}:${mm}`;
        }
    }
    updateClock();
    setInterval(updateClock, 1000);
</script>
</body>
</html>
