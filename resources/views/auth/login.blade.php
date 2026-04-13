@extends('layouts.app')

@section('title', 'Masuk - JoFresh')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center w-full bg-slate-50 p-6">
    
    <div class="z-10 relative w-full max-w-md">
        <div class="text-center mb-12 auth-logo mt-6 md:mt-0">
            <div class="jis-box">
                <h2>J I S</h2>
            </div>
            <p class="text-slate-600 font-medium tracking-wide">Pusat Manajemen Inventori Unggas JoFresh</p>
        </div>

        <div class="card flex-col items-stretch auth-card">
            <h3 class="text-blue-900 font-bold text-2xl mb-8 text-center">Masuk ke Dashboard</h3>

            @if(session('error'))
                <div class="flex items-center gap-2.5 bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-red-600 font-medium text-[0.95rem] mb-6 animate-pulse">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ url('/login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input type="email" id="email" name="email" class="form-control @error('email') input-error @enderror"
                        placeholder="contoh: owner@jofresh.com" value="{{ old('email') }}" required>
                </div>
                <div class="form-group mt-6">
                    <label for="password">Kata Sandi</label>
                    <input type="password" id="password" name="password" class="form-control"
                        placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary w-full mt-8 py-4 text-lg">Masuk Akun</button>
            </form>
        </div>
    </div>
</div>
@endsection
