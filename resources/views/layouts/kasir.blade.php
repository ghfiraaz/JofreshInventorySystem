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
<div class="flex w-full">
    <aside id="sidebar" class="w-[260px] flex-shrink-0 bg-blue-50 flex flex-col z-10 border-r border-blue-200 shadow-[0_2px_4px_rgba(30,58,138,0.05)] transition-all duration-300 overflow-hidden">
        <div class="px-6 py-6 text-center border-b border-blue-200 flex justify-center">
            <div class="jis-box-sidebar">
                <h2>J I S</h2>
            </div>
        </div>

        <div class="flex items-center px-6 py-7 border-b border-blue-200">
            <div class="w-11 h-11 rounded-full bg-blue-300 text-blue-900 flex items-center justify-center font-bold text-[1.1rem] mr-4">K</div>
            <div>
                <div class="font-bold text-base">{{ Auth::user()->name }}</div>
                <div class="text-slate-600 text-sm">Kasir</div>
            </div>
        </div>

        <ul class="px-4 py-6 flex-grow flex flex-col gap-2">
            <a href="{{ url('/kasir') }}" class="flex items-center px-5 py-3.5 rounded-xl font-medium transition-all duration-200 text-base {{ Request::is('kasir') ? 'bg-blue-100 text-blue-900 font-semibold' : 'text-slate-600 hover:bg-blue-100 hover:text-blue-900 hover:translate-x-1.5' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px] mr-3.5 transition-transform duration-400">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                </svg>
                Dashboard
            </a>
            <a href="{{ url('/kasir/transaksi') }}" class="flex items-center px-5 py-3.5 rounded-xl font-medium transition-all duration-200 text-base {{ Request::is('kasir/transaksi') ? 'bg-blue-100 text-blue-900 font-semibold' : 'text-slate-600 hover:bg-blue-100 hover:text-blue-900 hover:translate-x-1.5' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px] mr-3.5 transition-transform duration-400">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                </svg>
                Transaksi Baru
            </a>
            <a href="{{ url('/kasir/riwayat') }}" class="flex items-center px-5 py-3.5 rounded-xl font-medium transition-all duration-200 text-base {{ Request::is('kasir/riwayat') ? 'bg-blue-100 text-blue-900 font-semibold' : 'text-slate-600 hover:bg-blue-100 hover:text-blue-900 hover:translate-x-1.5' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px] mr-3.5 transition-transform duration-400">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Riwayat Transaksi
            </a>
        </ul>

        <div class="px-5 pb-5">
            <form action="{{ url('/logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center w-full px-5 py-3.5 bg-transparent border border-blue-200 rounded-xl cursor-pointer font-medium text-base text-slate-600 transition-all duration-200 hover:bg-red-50 hover:text-red-600 hover:border-red-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25" />
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-grow flex flex-col w-full min-w-0 transition-all duration-300">
        <header class="h-20 px-10 flex items-center justify-between bg-slate-50 border-b border-blue-200 flex-shrink-0">
            <div class="flex items-center gap-4">
                <button id="sidebar-toggle" class="p-2 -ml-2 rounded-lg text-slate-500 hover:bg-blue-100/50 hover:text-blue-900 transition-colors cursor-pointer border-none bg-transparent outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <h2 class="text-2xl font-bold">@yield('title')</h2>
            </div>
            <div class="text-slate-600 text-sm" id="header-date"></div>
        </header>
        <div class="p-10 flex-grow overflow-y-auto">
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
