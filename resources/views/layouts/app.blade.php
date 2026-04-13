<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>JIS - @yield('title')</title>
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
    @if(Request::is('/'))
        @yield('content')
    @else
        <div class="flex w-full">
            <aside id="sidebar" class="w-[260px] flex-shrink-0 bg-blue-50 flex flex-col z-10 border-r border-blue-200 shadow-[0_2px_4px_rgba(30,58,138,0.05)] transition-all duration-300 overflow-hidden">
                <div class="px-6 py-6 text-center border-b border-blue-200 flex justify-center">
                    <div class="jis-box-sidebar">
                        <h2>J I S</h2>
                    </div>
                </div>
                
                <div class="flex items-center px-6 py-7 border-b border-blue-200">
                    <div class="w-11 h-11 rounded-full bg-blue-300 text-blue-900 flex items-center justify-center font-bold text-[1.1rem] mr-4">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                    <div>
                        <div class="font-bold text-base">{{ Auth::user()->name }}</div>
                        <div class="text-slate-600 text-sm">Owner JoFresh</div>
                    </div>
                </div>

                <ul class="px-4 py-6 flex-grow flex flex-col gap-2">
                    <a href="{{ url('/dashboard') }}" class="flex items-center px-5 py-3.5 rounded-xl font-medium transition-all duration-200 text-base {{ Request::is('dashboard') ? 'bg-blue-100 text-blue-900 font-semibold' : 'text-slate-600 hover:bg-blue-100 hover:text-blue-900 hover:translate-x-1.5' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px] mr-3.5 transition-transform duration-400">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ url('/transactions') }}" class="flex items-center px-5 py-3.5 rounded-xl font-medium transition-all duration-200 text-base {{ Request::is('transactions') ? 'bg-blue-100 text-blue-900 font-semibold' : 'text-slate-600 hover:bg-blue-100 hover:text-blue-900 hover:translate-x-1.5' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px] mr-3.5 transition-transform duration-400">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Riwayat Transaksi
                    </a>

                    <a href="{{ url('/users') }}" class="flex items-center px-5 py-3.5 rounded-xl font-medium transition-all duration-200 text-base {{ Request::is('users') ? 'bg-blue-100 text-blue-900 font-semibold' : 'text-slate-600 hover:bg-blue-100 hover:text-blue-900 hover:translate-x-1.5' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px] mr-3.5 transition-transform duration-400">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                        Kelola Pengguna
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
    @endif
    <!-- Global Confirmation Modal -->
    <div id="modal-confirm" class="fixed inset-0 bg-slate-900/50 z-[100] flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 modal-overlay font-sans">
        <div class="bg-white rounded-2xl w-full max-w-md p-8 shadow-2xl relative transform scale-95 transition-transform duration-300">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6 text-red-600">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>
            <h3 id="confirm-title" class="text-xl font-bold text-slate-800 text-center uppercase tracking-tight">Konfirmasi</h3>
            <p id="confirm-message" class="text-slate-600 text-center mt-3 text-base">Apakah Anda yakin?</p>
            <div class="mt-10 flex justify-center gap-4">
                <button type="button" class="btn btn-outline min-w-[120px]" id="btn-confirm-no">Tidak</button>
                <button type="button" class="btn bg-red-600 text-white hover:bg-red-700 shadow-[0_4px_14px_rgba(220,38,38,0.4)] min-w-[120px]" id="btn-confirm-yes">Ya</button>
            </div>
        </div>
    </div>

    <!-- Main JS -->
    <script src="{{ asset('js/main.js') }}?v={{ time() }}"></script>
    <script>
        const _d = new Date();
        const _days = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
        const _months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        const _el = document.getElementById('header-date');
        if (_el) _el.textContent = `${_days[_d.getDay()]}, ${_d.getDate()} ${_months[_d.getMonth()]} ${_d.getFullYear()}`;
    </script>
</body>
</html>
