<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>JIS Kasir - @yield('title')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 font-sans min-h-screen flex antialiased">
<div class="flex w-full min-h-screen">

    {{-- ===== SIDEBAR ===== --}}
    <aside class="w-[200px] flex-shrink-0 bg-white flex flex-col border-r border-gray-200">
        {{-- Logo --}}
        <div class="px-6 pt-6 pb-4">
            <h2 class="text-xl font-extrabold tracking-[8px] text-gray-800" style="letter-spacing:8px;">J I S</h2>
        </div>

        {{-- User Profile --}}
        <div class="flex items-center px-5 py-4 mx-3 mb-2">
            <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-800 flex items-center justify-center font-bold text-sm mr-3 flex-shrink-0">K</div>
            <div class="min-w-0">
                <div class="font-semibold text-sm text-gray-800 truncate">{{ Auth::user()->name }}</div>
                <div class="text-gray-400 text-xs">Kasir</div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="px-3 flex-grow flex flex-col gap-1">
            <a href="{{ url('/kasir/dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-[13px] font-medium transition-all duration-200 {{ Request::is('kasir/dashboard') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[18px] h-[18px] mr-3 flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                </svg>
                Dashboard
            </a>

            @php
                $isTransaksiOpen = Request::is('kasir/transaksi') || Request::is('kasir/riwayat') || Request::is('kasir/tagihan') || Request::is('kasir');
            @endphp
            <div class="mt-1">
                <button type="button" onclick="document.getElementById('transaksi-submenu').classList.toggle('hidden'); this.querySelector('.chevron').classList.toggle('rotate-180')" class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg text-[13px] font-medium transition-all duration-200 text-gray-600 hover:bg-gray-100 cursor-pointer border-none bg-transparent">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[18px] h-[18px] mr-3 flex-shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                        Transaksi
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 chevron transition-transform {{ $isTransaksiOpen ? 'rotate-180' : '' }}">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
                <div id="transaksi-submenu" class="pl-9 pr-2 mt-1 space-y-1 {{ $isTransaksiOpen ? '' : 'hidden' }}">
                    <a href="{{ url('/kasir/transaksi') }}" class="block px-3 py-2 rounded-lg text-xs font-medium transition-colors {{ Request::is('kasir/transaksi') || Request::is('kasir') ? 'bg-blue-50 text-blue-700' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50' }}">
                        Input Transaksi
                    </a>
                    <a href="{{ url('/kasir/tagihan') }}" class="block px-3 py-2 rounded-lg text-xs font-medium transition-colors {{ Request::is('kasir/tagihan') ? 'bg-blue-50 text-blue-700' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50' }}">
                        Daftar Belum Dibayar
                    </a>
                    <a href="{{ url('/kasir/riwayat') }}" class="block px-3 py-2 rounded-lg text-xs font-medium transition-colors {{ Request::is('kasir/riwayat') ? 'bg-blue-50 text-blue-700' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50' }}">
                        Riwayat Transaksi
                    </a>
                </div>
            </div>
        </nav>

        {{-- Logout --}}
        <div class="px-3 pb-5 mt-auto">
            <form action="{{ url('/logout') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin keluar?');">
                @csrf
                <button type="submit" class="flex items-center w-full px-4 py-2.5 bg-transparent border-none rounded-lg cursor-pointer text-[13px] font-medium text-gray-500 transition-all duration-200 hover:bg-red-50 hover:text-red-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[18px] h-[18px] mr-3 flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25" />
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- ===== MAIN CONTENT ===== --}}
    <main class="flex-grow flex flex-col w-full min-w-0">
        {{-- Header --}}
        <header class="h-16 px-8 flex items-center justify-between bg-white border-b border-gray-200 flex-shrink-0">
            <h2 class="text-lg font-bold text-gray-800">@yield('title')</h2>
            <div class="text-gray-400 text-sm" id="header-date"></div>
        </header>

        {{-- Content --}}
        <div class="p-8 flex-grow overflow-y-auto">
            @yield('content')
        </div>
    </main>
</div>

<script src="{{ asset('js/main.js') }}?v={{ time() }}"></script>
<script>
    const d = new Date();
    const days = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    const el = document.getElementById('header-date');
    if (el) el.textContent = `${days[d.getDay()]}, ${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
</script>
</body>
</html>
