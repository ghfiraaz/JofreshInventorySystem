<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>JoFresh Admin - @yield('title')</title>
    <link class="js-favicon" rel="icon" type="image/png" href="{{ asset('images/favicon-chicken.png') }}">
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
    <aside id="sidebar" class="w-[260px] flex-shrink-0 flex flex-col z-10 shadow-[2px_0_8px_rgba(0,0,0,0.02)] transition-all duration-300 overflow-hidden" style="background: #7B3911; border-right: 1px solid rgba(255,255,255,0.06);">
        <div class="px-6 py-6 text-center border-b flex justify-center items-center" style="border-color: rgba(255,255,255,0.08);">
            <img src="{{ asset('images/logo-jofresh-white.png') }}" alt="JoFresh" class="h-16 w-auto object-contain">
        </div>

        <!-- Sidebar Profile Display (Static, Above Dashboard Menu) -->
        <div class="flex items-center px-6 py-5 border-b" style="border-color: rgba(255,255,255,0.08);">
            <div class="w-11 h-11 rounded-full flex items-center justify-center font-bold text-lg mr-4 flex-shrink-0" style="background: #FAF0E6; color: #7B3911;">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div class="min-w-0 flex-grow">
                <div class="font-bold text-base text-white truncate">{{ Auth::user()->name }}</div>
                <div class="text-xs text-[rgba(255,255,255,0.6)] truncate">
                    @if(Auth::user()->role === 'Superadmin')
                        Owner JoFresh
                    @elseif(Auth::user()->role === 'Kasir')
                        Kasir JoFresh
                    @else
                        Admin JoFresh
                    @endif
                </div>
            </div>
        </div>

        <ul class="px-4 py-8 flex-grow flex flex-col gap-2 overflow-y-auto">
            <a href="{{ url('/admin/dashboard') }}" class="flex items-center px-5 py-3.5 rounded-xl font-medium transition-all duration-200 text-base {{ Request::is('admin/dashboard') ? 'font-bold' : 'hover:translate-x-1' }}" style="{{ Request::is('admin/dashboard') ? 'background: #8E4416; color: #ffffff;' : 'color: rgba(255,255,255,0.7);' }}" onmouseover="if(!this.classList.contains('font-bold')){this.style.background='rgba(255,255,255,0.08)';this.style.color='#ffffff';}" onmouseout="if(!this.classList.contains('font-bold')){this.style.background='transparent';this.style.color='rgba(255,255,255,0.7)';}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px] mr-3.5 transition-transform duration-400">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                </svg>
                Dashboard
            </a>
            <a href="{{ url('/admin/produk') }}" class="flex items-center px-5 py-3.5 rounded-xl font-medium transition-all duration-200 text-base {{ Request::is('admin/produk') ? 'font-bold' : 'hover:translate-x-1' }}" style="{{ Request::is('admin/produk') ? 'background: #8E4416; color: #ffffff;' : 'color: rgba(255,255,255,0.7);' }}" onmouseover="if(!this.classList.contains('font-bold')){this.style.background='rgba(255,255,255,0.08)';this.style.color='#ffffff';}" onmouseout="if(!this.classList.contains('font-bold')){this.style.background='transparent';this.style.color='rgba(255,255,255,0.7)';}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px] mr-3.5 transition-transform duration-400">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                </svg>
                Produk
            </a>
            <a href="{{ url('/admin/mitra') }}" class="flex items-center px-5 py-3.5 rounded-xl font-medium transition-all duration-200 text-base {{ Request::is('admin/mitra') ? 'font-bold' : 'hover:translate-x-1' }}" style="{{ Request::is('admin/mitra') ? 'background: #8E4416; color: #ffffff;' : 'color: rgba(255,255,255,0.7);' }}" onmouseover="if(!this.classList.contains('font-bold')){this.style.background='rgba(255,255,255,0.08)';this.style.color='#ffffff';}" onmouseout="if(!this.classList.contains('font-bold')){this.style.background='transparent';this.style.color='rgba(255,255,255,0.7)';}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px] mr-3.5 transition-transform duration-400">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016 2.993 2.993 0 0 0 2.25-1.016 3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 1 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                </svg>
                Mitra
            </a>
            <a href="{{ url('/admin/transactions') }}" class="flex items-center px-5 py-3.5 rounded-xl font-medium transition-all duration-200 text-base {{ Request::is('admin/transactions') ? 'font-bold' : 'hover:translate-x-1' }}" style="{{ Request::is('admin/transactions') ? 'background: #8E4416; color: #ffffff;' : 'color: rgba(255,255,255,0.7);' }}" onmouseover="if(!this.classList.contains('font-bold')){this.style.background='rgba(255,255,255,0.08)';this.style.color='#ffffff';}" onmouseout="if(!this.classList.contains('font-bold')){this.style.background='transparent';this.style.color='rgba(255,255,255,0.7)';}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px] mr-3.5 transition-transform duration-400">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Riwayat Transaksi
            </a>
            <a href="{{ url('/log-stok') }}" class="flex items-center px-5 py-3.5 rounded-xl font-medium transition-all duration-200 text-base {{ Request::is('log-stok') ? 'font-bold' : 'hover:translate-x-1' }}" style="{{ Request::is('log-stok') ? 'background: #8E4416; color: #ffffff;' : 'color: rgba(255,255,255,0.7);' }}" onmouseover="if(!this.classList.contains('font-bold')){this.style.background='rgba(255,255,255,0.08)';this.style.color='#ffffff';}" onmouseout="if(!this.classList.contains('font-bold')){this.style.background='transparent';this.style.color='rgba(255,255,255,0.7)';}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px] mr-3.5 transition-transform duration-400">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z" />
                </svg>
                Log Stok
            </a>
        </ul>

        <div class="px-4 pb-5 pt-4 border-t" style="border-color: rgba(255,255,255,0.08);">
            <form action="{{ url('/logout') }}" method="POST" id="form-logout" class="hidden">
                @csrf
            </form>
            <button type="button" onclick="showLogoutModal()" class="flex items-center w-full px-3 py-2.5 bg-transparent border-none rounded-xl cursor-pointer font-medium text-sm transition-all duration-200" style="color: rgba(255, 255, 255, 0.7);" onmouseover="this.style.background='rgba(255, 255, 255, 0.08)';this.style.color='#ffffff';" onmouseout="this.style.background='transparent';this.style.color='rgba(255, 255, 255, 0.7)';">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4.5 h-4.5 mr-2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25" />
                </svg>
                Keluar
            </button>
        </div>
    </aside>

    <main class="flex-grow flex flex-col w-full min-w-0 transition-all duration-300">
        <header class="h-20 px-6 md:px-10 flex items-center justify-between bg-[#FAF6F0] flex-shrink-0 border-b border-[#E0D5CA]">
            <div class="flex items-center gap-4">
                <button id="sidebar-toggle" class="p-2 rounded-lg transition-colors cursor-pointer border-none bg-transparent outline-none" style="color: #9C8B7E;" onmouseover="this.style.background='rgba(123,57,17,0.06)';this.style.color='#7B3911';" onmouseout="this.style.background='transparent';this.style.color='#9C8B7E';">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <h2 class="text-2xl font-bold" style="color: #3D1B07;">@yield('title')</h2>
            </div>
            <div class="flex items-center gap-6">
                <div id="header-date" class="flex items-center gap-2 px-3 py-1.5 rounded-xl shadow-sm" style="background: linear-gradient(135deg, #FAF0E6, #FFF8F0); border: 1px solid #E0C4A8;">
                    <span id="hd-day" class="text-xs font-bold uppercase tracking-widest" style="color: #C8702A;"></span>
                    <span id="hd-date" class="text-sm font-bold" style="color: #3D1B07;"></span>
                    <span class="w-px h-4" style="background: #E0C4A8;"></span>
                    <span id="hd-time" class="text-sm font-bold tabular-nums" style="color: #A1511E; font-variant-numeric:tabular-nums;"></span>
                </div>
                
                <div class="relative notif-bell-container">
                    <button type="button" id="btn-notif-bell" class="relative p-2 rounded-full bg-white text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer outline-none shadow-sm focus:outline-none" style="border: 1px solid #E0D5CA;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                        <!-- Badge Unread Counter -->
                        <span id="notif-badge" class="absolute top-0 right-0 -mt-1 -mr-1 h-4.5 w-4.5 text-[10px] font-bold text-white rounded-full flex items-center justify-center border border-white hidden" style="background: #D2691E;">0</span>
                    </button>

                    <div id="notif-dropdown" class="hidden absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-lg z-50 overflow-hidden" style="border: 1px solid #E0D5CA;">
                        <div class="px-4 py-3 border-b flex items-center justify-between" style="background: #FAF0E6; border-color: #E0D5CA;">
                            <h3 class="text-sm font-bold" style="color: #3D1B07;">Notifikasi</h3>
                            <button type="button" id="btn-notif-read-all" class="text-xs font-semibold cursor-pointer border-none bg-transparent focus:outline-none" style="color: #A1511E;" onmouseover="this.style.color='#7B3911'" onmouseout="this.style.color='#A1511E'">Tandai Semua Dibaca</button>
                        </div>
                        <div id="notif-list-container" class="max-h-[300px] overflow-y-auto divide-y" style="--tw-divide-color: #FAF0E6;">
                            <div class="p-4 text-center text-xs" style="color: #9C8B7E;">Memuat notifikasi...</div>
                        </div>
                    </div>
                </div>

            </div>
        </header>
        <div class="p-10 flex-grow overflow-y-auto">
            @yield('content')
        </div>
    </main>
</div>

<!-- Logout Modal -->
<div id="modal-logout" class="fixed inset-0 z-[100] flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 modal-overlay font-sans [&.active]:opacity-100 [&.active]:pointer-events-auto" style="background: rgba(61, 27, 7, 0.4);">
    <div class="bg-white rounded-3xl p-8 max-w-sm w-full mx-4 shadow-2xl transform scale-95 transition-transform duration-300 modal-content relative overflow-hidden">
        <div class="text-center">
            <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6" style="background: #FAF0E6;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10" style="color: #7B3911;">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>
            </div>
            <h3 class="text-2xl font-bold mb-2" style="color: #3D1B07;">Keluar Sistem?</h3>
            <p class="mb-8" style="color: #6B5B4E;">Apakah Anda yakin ingin mengakhiri sesi dan keluar dari aplikasi?</p>
            <div class="mt-8 flex flex-col gap-3">
                <button type="button" onclick="document.getElementById('modal-logout').classList.remove('active'); document.getElementById('modal-logout').querySelector('.modal-content').classList.remove('scale-100')" class="w-full px-6 py-3 rounded-xl font-bold text-white transition-all cursor-pointer border-none outline-none" style="background: linear-gradient(135deg, #7B3911, #A1511E);" onmouseover="this.style.background='linear-gradient(135deg, #5A270B, #7B3911)'" onmouseout="this.style.background='linear-gradient(135deg, #7B3911, #A1511E)'">
                    Tidak, Tetap Disini
                </button>
                <button type="button" onclick="document.getElementById('form-logout').submit()" class="w-full px-6 py-3 rounded-xl font-bold bg-gray-100 text-gray-500 hover:bg-gray-200 transition-all cursor-pointer border-none outline-none">
                    Ya, Keluar
                </button>
            </div>
        </div>
    </div>
</div>

    <!-- Global Confirmation Modal -->
    <div id="modal-confirm" class="fixed inset-0 z-[100] flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 modal-overlay font-sans [&.active]:opacity-100 [&.active]:pointer-events-auto" style="background: rgba(61, 27, 7, 0.4);">
        <div class="bg-white rounded-2xl w-full max-w-md p-8 shadow-2xl relative transform scale-95 transition-transform duration-300 [&.active]:scale-100">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6 text-red-600">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>
            <h3 id="confirm-title" class="text-xl font-bold text-center uppercase tracking-tight" style="color: #3D1B07;">Konfirmasi</h3>
            <p id="confirm-message" class="text-center mt-3 text-base" style="color: #6B5B4E;">Apakah Anda yakin?</p>
            <div class="mt-10 flex justify-center gap-4">
                <button type="button" id="btn-confirm-no" class="min-w-[120px] px-6 py-3 rounded-xl font-bold text-white border-none cursor-pointer transition-all" style="background: linear-gradient(135deg, #7B3911, #A1511E);" onmouseover="this.style.background='linear-gradient(135deg, #5A270B, #7B3911)'" onmouseout="this.style.background='linear-gradient(135deg, #7B3911, #A1511E)'">Tidak</button>
                <button type="button" id="btn-confirm-yes" class="min-w-[120px] px-6 py-3 rounded-xl font-bold bg-gray-200 text-gray-600 hover:bg-gray-300 border-none cursor-pointer transition-all">Ya</button>
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

    // Dropdown toggles (Header Profile)
    const btnHeaderProfile = document.getElementById('btn-header-profile');
    const headerProfileDropdown = document.getElementById('header-profile-dropdown');

    if (btnHeaderProfile && headerProfileDropdown) {
        btnHeaderProfile.addEventListener('click', (e) => {
            e.stopPropagation();
            headerProfileDropdown.classList.toggle('hidden');
            const notifDropdown = document.getElementById('notif-dropdown');
            if (notifDropdown) notifDropdown.classList.add('hidden');
        });
    }

    document.addEventListener('click', (e) => {
        if (headerProfileDropdown && !headerProfileDropdown.contains(e.target) && e.target !== btnHeaderProfile && !btnHeaderProfile.contains(e.target)) {
            headerProfileDropdown.classList.add('hidden');
        }
    });

    // Set current date & time in header
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
