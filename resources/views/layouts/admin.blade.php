<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>JIS Admin - @yield('title')</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Vite CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex antialiased">
<div class="flex w-full">
    <aside id="sidebar" class="w-[260px] flex-shrink-0 bg-white flex flex-col z-10 border-r border-slate-200 shadow-[2px_0_8px_rgba(0,0,0,0.02)] transition-all duration-300 overflow-hidden">
        <div class="px-6 py-6 text-center border-b border-slate-100 flex justify-center">
            <div class="jis-box-sidebar">
                <h2>J I S</h2>
            </div>
        </div>

        <div class="flex items-center px-6 py-7 border-b border-slate-100">
            <div class="w-11 h-11 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-[1.1rem] mr-4">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div>
                <div class="font-bold text-base">{{ Auth::user()->name }}</div>
                <div class="text-slate-600 text-sm">Admin JoFresh</div>
            </div>
        </div>

        <ul class="px-4 py-6 flex-grow flex flex-col gap-2">
            <a href="{{ url('/admin/dashboard') }}" class="flex items-center px-5 py-3.5 rounded-xl font-medium transition-all duration-200 text-base {{ Request::is('admin/dashboard') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800 hover:translate-x-1' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px] mr-3.5 transition-transform duration-400">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                </svg>
                Dashboard
            </a>
            <a href="{{ url('/admin/produk') }}" class="flex items-center px-5 py-3.5 rounded-xl font-medium transition-all duration-200 text-base {{ Request::is('admin/produk') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800 hover:translate-x-1' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px] mr-3.5 transition-transform duration-400">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                </svg>
                Produk
            </a>
            <a href="{{ url('/admin/mitra') }}" class="flex items-center px-5 py-3.5 rounded-xl font-medium transition-all duration-200 text-base {{ Request::is('admin/mitra') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800 hover:translate-x-1' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px] mr-3.5 transition-transform duration-400">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016 2.993 2.993 0 0 0 2.25-1.016 3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                </svg>
                Mitra
            </a>
            <a href="{{ url('/admin/transactions') }}" class="flex items-center px-5 py-3.5 rounded-xl font-medium transition-all duration-200 text-base {{ Request::is('admin/transactions') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800 hover:translate-x-1' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px] mr-3.5 transition-transform duration-400">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Riwayat Transaksi
            </a>
        </ul>

        <div class="px-5 pb-5">
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

    <main class="flex-grow flex flex-col w-full min-w-0 transition-all duration-300">
        <header class="h-20 px-10 flex items-center justify-between bg-slate-50 flex-shrink-0">
            <div class="flex items-center gap-4">
                <button id="sidebar-toggle" class="p-2 -ml-2 rounded-lg text-slate-400 hover:bg-slate-200/50 hover:text-slate-800 transition-colors cursor-pointer border-none bg-transparent outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <h2 class="text-2xl font-bold">@yield('title')</h2>
            </div>
            <div class="flex items-center gap-6">
                <div class="text-slate-600 text-sm font-medium" id="header-date"></div>
                
                @if(Auth::user()->role === 'Superadmin')
                <div class="relative">
                    <button type="button" onclick="document.getElementById('notif-dropdown').classList.toggle('hidden')" class="relative p-2 rounded-full bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer outline-none shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                        <span class="absolute top-0 right-0 -mt-1 -mr-1 flex h-3.5 w-3.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-red-500 border-2 border-white"></span>
                        </span>
                    </button>

                    <div id="notif-dropdown" class="hidden absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-lg border border-gray-100 z-50 overflow-hidden">
                        <div class="px-4 py-3 bg-slate-50 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-slate-800">Notifikasi</h3>
                        </div>
                        <div class="max-h-[300px] overflow-y-auto">
                            <a href="{{ url('/owner/laporan-harian') }}" target="_blank" class="block px-4 py-4 hover:bg-blue-50/50 transition-colors border-b border-gray-50 no-underline group">
                                <div class="flex gap-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-slate-700 font-medium leading-snug">Berikut merupakan laporan transaksi per hari ini.</p>
                                        <p class="text-xs text-blue-600 mt-1 font-semibold">Klik untuk Download PDF</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </header>
        <div class="p-10 flex-grow overflow-y-auto">
            @yield('content')
        </div>
    </main>
</div>

<!-- Logout Modal (Psychological UI) -->
<div id="modal-logout" class="fixed inset-0 bg-slate-900/50 z-[100] flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 modal-overlay font-sans [&.active]:opacity-100 [&.active]:pointer-events-auto">
    <div class="bg-white rounded-3xl p-8 max-w-sm w-full mx-4 shadow-2xl transform scale-95 transition-transform duration-300 modal-content relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-red-500 to-rose-500"></div>
        <div class="text-center">
            <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-red-500">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-slate-800 mb-2">Keluar Sistem?</h3>
            <p class="text-slate-600 mb-8">Apakah Anda yakin ingin mengakhiri sesi dan keluar dari aplikasi?</p>
            <div class="mt-8 flex flex-col gap-3">
                <button type="button" onclick="document.getElementById('modal-logout').classList.remove('active'); document.getElementById('modal-logout').querySelector('.modal-content').classList.remove('scale-100')" class="w-full px-6 py-3 rounded-xl font-bold bg-red-600 text-white hover:bg-red-700 shadow-[0_4px_14px_rgba(220,38,38,0.4)] transition-all cursor-pointer border-none outline-none">
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

    // Set current date in header
    const d = new Date();
    const days = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    document.getElementById('header-date').textContent = `${days[d.getDay()]}, ${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
</script>
</body>
</html>
