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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Vite CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Sidebar active indicator */
        .sidebar-link { position: relative; }
        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: #1e3a5f;
            border-radius: 0 4px 4px 0;
        }
        .sidebar-link.active { background: #f1f5f9; color: #0f172a; font-weight: 700; }

        /* Smooth scrollbar */
        .sidebar-scroll::-webkit-scrollbar { width: 3px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }

        /* Toast animation */
        @keyframes toastIn { from { opacity: 0; transform: translateX(40px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes toastOut { from { opacity: 1; transform: translateX(0); } to { opacity: 0; transform: translateX(40px); } }
        .toast-enter { animation: toastIn 0.3s ease-out forwards; }
        .toast-exit { animation: toastOut 0.3s ease-in forwards; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex antialiased">
<div class="flex w-full min-h-screen">
    {{-- ===== SIDEBAR ===== --}}
    <aside id="sidebar" class="w-[260px] flex-shrink-0 bg-white flex flex-col z-10 border-r border-slate-200 transition-all duration-300 overflow-hidden">
        {{-- Logo --}}
        <div class="px-6 py-5 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-slate-800 flex items-center justify-center">
                    <span class="text-white font-extrabold text-sm tracking-wider">JIS</span>
                </div>
                <div>
                    <div class="text-sm font-bold text-slate-800 tracking-wide">JoFresh</div>
                    <div class="text-[10px] text-slate-400 font-medium">Inventory System</div>
                </div>
            </div>
        </div>

        {{-- User Profile --}}
        <div class="flex items-center px-5 py-5 border-b border-slate-100">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-slate-600 to-slate-800 text-white flex items-center justify-center font-bold text-sm mr-3 flex-shrink-0">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div class="min-w-0">
                <div class="font-semibold text-sm truncate">{{ Auth::user()->name }}</div>
                <div class="text-xs text-slate-400 font-medium">Kasir</div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="px-3 py-4 flex-grow sidebar-scroll overflow-y-auto">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-3 mb-2">Menu</div>

            <a href="{{ url('/kasir/dashboard') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 mb-1 {{ Request::is('kasir/dashboard') ? 'active' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                </svg>
                Dashboard
            </a>

            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-3 mb-2 mt-4">Transaksi</div>

            <a href="{{ url('/kasir/transaksi') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 mb-1 {{ Request::is('kasir/transaksi') ? 'active' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Input Transaksi
            </a>
            <a href="{{ url('/kasir/tagihan') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 mb-1 {{ Request::is('kasir/tagihan') ? 'active' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                Belum Dibayar
            </a>
            <a href="{{ url('/kasir/riwayat') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 mb-1 {{ Request::is('kasir/riwayat') ? 'active' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                Riwayat Transaksi
            </a>
            <a href="{{ url('/kasir/reminder-history') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 mb-1 {{ Request::is('kasir/reminder-history') ? 'active' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                Histori Reminder
            </a>
        </nav>

        {{-- Logout --}}
        <div class="px-3 pb-4 mt-auto border-t border-slate-100 pt-3">
            <form action="{{ url('/logout') }}" method="POST" id="form-logout">
                @csrf
                <button type="button" onclick="showLogoutModal()" class="flex items-center gap-3 w-full px-3 py-2.5 bg-transparent border-none rounded-lg cursor-pointer text-sm text-slate-400 transition-all duration-200 hover:bg-red-50 hover:text-red-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
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
        <header class="h-16 px-8 flex items-center justify-between bg-white border-b border-slate-200 flex-shrink-0">
            <div class="flex items-center gap-3">
                <button id="sidebar-toggle" class="p-2 -ml-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-800 transition-colors cursor-pointer border-none bg-transparent outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <h2 class="text-lg font-bold text-slate-800">@yield('title')</h2>
            </div>
            <div id="header-date" class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200">
                <span id="hd-day" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"></span>
                <span id="hd-date" class="text-xs font-semibold text-slate-600"></span>
                <span class="w-px h-3 bg-slate-200"></span>
                <span id="hd-time" class="text-xs font-bold text-slate-800 tabular-nums" style="font-variant-numeric:tabular-nums;"></span>
            </div>
        </header>

        {{-- Content --}}
        <div class="p-8 flex-grow overflow-y-auto bg-slate-50">
            @yield('content')
        </div>
    </main>
</div>

<!-- Logout Modal -->
<div id="modal-logout" class="fixed inset-0 bg-slate-900/50 z-[100] flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 modal-overlay [&.active]:opacity-100 [&.active]:pointer-events-auto">
    <div class="bg-white rounded-2xl p-8 max-w-sm w-full mx-4 shadow-2xl transform scale-95 transition-transform duration-300 modal-content relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-slate-600 to-slate-800"></div>
        <div class="text-center">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-slate-600">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Keluar Sistem?</h3>
            <p class="text-sm text-slate-500 mb-6">Apakah Anda yakin ingin mengakhiri sesi dan keluar dari aplikasi?</p>
            <div class="flex flex-col gap-2.5">
                <button type="button" onclick="document.getElementById('modal-logout').classList.remove('active'); document.getElementById('modal-logout').querySelector('.modal-content').classList.remove('scale-100')" class="w-full px-5 py-2.5 rounded-xl font-bold text-white bg-slate-800 hover:bg-slate-700 transition-all cursor-pointer border-none outline-none text-sm">
                    Tidak, Tetap Disini
                </button>
                <button type="button" onclick="document.getElementById('form-logout').submit()" class="w-full px-5 py-2.5 rounded-xl font-semibold bg-slate-100 text-slate-500 hover:bg-slate-200 transition-all cursor-pointer border-none outline-none text-sm">
                    Ya, Keluar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Global Toast Container --}}
<div id="global-toast" class="fixed top-5 right-5 z-[200] flex flex-col gap-2"></div>

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
