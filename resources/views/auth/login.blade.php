@extends('layouts.app')

@section('title', 'Masuk - JoFresh')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center w-full p-4" style="background: linear-gradient(135deg, #FAF8F5 0%, #FAF0E6 50%, #FFF8F0 100%);">
    
    <div class="z-10 relative w-full max-w-sm">
        <div class="text-center mb-5 mt-4 md:mt-0">
            <div class="flex justify-center mb-4">
                <img src="{{ asset('images/logo-jofresh.png') }}" alt="JoFresh Logo" class="w-[160px] h-auto object-contain drop-shadow-lg">
            </div>
            <p class="font-bold text-[10px] tracking-widest uppercase" style="color: #7B3911; opacity: 0.85;">Pusat Manajemen Inventori Unggas JoFresh</p>
        </div>

        <div class="bg-white rounded-2xl px-6 py-7 flex flex-col items-stretch" style="border: 1px solid #E0D5CA; box-shadow: 0 8px 32px rgba(123, 57, 17, 0.08);">
            <h3 class="font-bold text-xl mb-5 text-center" style="color: #7B3911;">Masuk ke Dashboard</h3>

            @if(session('error'))
                <div class="flex items-center gap-2 bg-red-50 border border-red-200 rounded-lg px-3 py-2.5 text-red-600 font-medium text-sm mb-5 animate-pulse">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 flex-shrink-0">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ url('/login') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block mb-1.5 font-medium uppercase tracking-wider text-[10px]" style="color: #3D1B07;">Alamat Email</label>
                    <input type="email" id="email" name="email" class="w-full px-3.5 py-2.5 rounded-lg text-sm bg-white outline-none transition-all duration-200 @error('email') input-error @enderror"
                        style="border: 1px solid #E0D5CA;" placeholder="contoh: owner@jofresh.com" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="password" class="block mb-1.5 font-medium uppercase tracking-wider text-[10px]" style="color: #3D1B07;">Kata Sandi</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" class="w-full px-3.5 py-2.5 pr-10 rounded-lg text-sm bg-white outline-none transition-all duration-200 @error('password') input-error @enderror"
                            style="border: 1px solid #E0D5CA;" placeholder="••••••••" required>
                        <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 hover:text-slate-600 focus:outline-none transition-colors" style="color: #9C8B7E;">
                            <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <svg id="eye-slash-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 hidden">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <div class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary w-full mt-5 py-2.5 text-sm">Masuk Akun</button>
            </form>
        </div>

        <p class="text-center mt-5 text-[10px]" style="color: #9C8B7E;">&copy; {{ date('Y') }} JoFresh — Elevating Freshness Every Day.</p>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');
    const eyeSlashIcon = document.getElementById('eye-slash-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.add('hidden');
        eyeSlashIcon.classList.remove('hidden');
    } else {
        passwordInput.type = 'password';
        eyeSlashIcon.classList.add('hidden');
        eyeIcon.classList.remove('hidden');
    }
}
</script>
@endsection
