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

            <div class="px-4 py-2 mt-2 text-[11px] font-bold tracking-wider text-gray-400 uppercase">
                Transaksi
            </div>
            
            <a href="{{ url('/kasir/transaksi') }}" class="flex items-center px-4 py-2.5 rounded-lg text-[13px] font-medium transition-all duration-200 {{ Request::is('kasir/transaksi') || Request::is('kasir') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[18px] h-[18px] mr-3 flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                </svg>
                Input Transaksi
            </a>
            <a href="{{ url('/kasir/riwayat') }}" class="flex items-center px-4 py-2.5 rounded-lg text-[13px] font-medium transition-all duration-200 {{ Request::is('kasir/riwayat') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[18px] h-[18px] mr-3 flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Riwayat Transaksi
            </a>
            <a href="{{ url('/kasir/tagihan') }}" class="flex items-center px-4 py-2.5 rounded-lg text-[13px] font-medium transition-all duration-200 {{ Request::is('kasir/tagihan') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[18px] h-[18px] mr-3 flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                </svg>
                Daftar Belum Dibayar
            </a>
        </nav>

        {{-- Logout --}}
        <div class="px-3 pb-5 mt-auto">
            <form action="{{ url('/logout') }}" method="POST">
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
