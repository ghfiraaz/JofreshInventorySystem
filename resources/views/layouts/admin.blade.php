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
    <aside class="w-[260px] bg-blue-50 flex flex-col z-10 border-r border-blue-200 shadow-[0_2px_4px_rgba(30,58,138,0.05)]">
        <div class="px-6 py-9 text-center border-b border-blue-200">
            <h2 class="font-serif tracking-[8px] mb-1 text-[1.75rem] font-bold">J I S</h2>
            <span class="text-[0.7rem] tracking-[1px] text-slate-600">JoFresh Inventory System</span>
        </div>

        <div class="flex items-center px-6 py-7 border-b border-blue-200">
            <div class="w-11 h-11 rounded-full bg-blue-300 text-blue-900 flex items-center justify-center font-bold text-[1.1rem] mr-4">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div>
                <div class="font-bold text-base">{{ Auth::user()->name }}</div>
                <div class="text-slate-600 text-sm">Admin JoFresh</div>
            </div>
        </div>

        <ul class="px-4 py-6 flex-grow flex flex-col gap-2">
            <a href="{{ url('/admin/produk') }}" class="flex items-center px-5 py-3.5 rounded-xl font-medium transition-all duration-200 text-base {{ Request::is('admin/produk') ? 'bg-blue-100 text-blue-900 font-semibold' : 'text-slate-600 hover:bg-blue-100 hover:text-blue-900 hover:translate-x-1.5' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px] mr-3.5 transition-transform duration-400">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                </svg>
                Produk
            </a>
            <a href="{{ url('/admin/stok-masuk') }}" class="flex items-center px-5 py-3.5 rounded-xl font-medium transition-all duration-200 text-base {{ Request::is('admin/stok-masuk') ? 'bg-blue-100 text-blue-900 font-semibold' : 'text-slate-600 hover:bg-blue-100 hover:text-blue-900 hover:translate-x-1.5' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px] mr-3.5 transition-transform duration-400">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                </svg>
                Stok Masuk
            </a>
            <a href="{{ url('/admin/mitra') }}" class="flex items-center px-5 py-3.5 rounded-xl font-medium transition-all duration-200 text-base {{ Request::is('admin/mitra') ? 'bg-blue-100 text-blue-900 font-semibold' : 'text-slate-600 hover:bg-blue-100 hover:text-blue-900 hover:translate-x-1.5' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px] mr-3.5 transition-transform duration-400">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016 2.993 2.993 0 0 0 2.25-1.016 3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                </svg>
                Mitra
            </a>
            <a href="{{ url('/admin/pembayaran-mitra') }}" class="flex items-center px-5 py-3.5 rounded-xl font-medium transition-all duration-200 text-base {{ Request::is('admin/pembayaran-mitra') ? 'bg-blue-100 text-blue-900 font-semibold' : 'text-slate-600 hover:bg-blue-100 hover:text-blue-900 hover:translate-x-1.5' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px] mr-3.5 transition-transform duration-400">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                </svg>
                Pembayaran Mitra
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

    <main class="flex-grow flex flex-col max-w-[calc(100%-260px)]">
        <header class="h-20 px-10 flex items-center justify-between bg-slate-50 border-b border-blue-200 flex-shrink-0">
            <h2 class="text-2xl font-bold">@yield('title')</h2>
            <div class="text-slate-600 text-sm" id="header-date"></div>
        </header>
        <div class="p-10 flex-grow overflow-y-auto">
            @yield('content')
        </div>
    </main>
</div>

<script src="{{ asset('js/main.js') }}"></script>
<script>
    // Set current date in header
    const d = new Date();
    const days = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    document.getElementById('header-date').textContent = `${days[d.getDay()]}, ${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
</script>
</body>
</html>
