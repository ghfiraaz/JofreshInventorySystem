<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller Autentikasi
 * Mengelola proses login, logout, dan redirect berdasarkan role pengguna.
 */
class AuthController extends Controller
{
    /**
     * Menampilkan halaman login.
     * Jika pengguna sudah login, langsung redirect sesuai role.
     */
    public function showLogin()
    {
        // Cek apakah pengguna sudah login
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }
        return view('auth.login');
    }

    /**
     * Memproses login pengguna.
     * Validasi email dan password, lalu redirect sesuai role jika berhasil.
     */
    public function login(Request $request)
    {
        // Validasi input login
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Cek apakah akun terdaftar di database
        $user = \App\Models\User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Akun tidak terdaftar'])->withInput($request->only('email'));
        }

        // Percobaan login dengan kredensial
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Regenerasi session untuk keamanan
            $request->session()->regenerate();
            return $this->redirectByRole(Auth::user()->role);
        }

        // Jika password salah
        return back()->withErrors(['password' => 'Kata sandi salah'])->withInput($request->only('email'));
    }

    /**
     * Memproses logout pengguna.
     * Hapus session dan redirect ke halaman login.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /**
     * Redirect pengguna berdasarkan role.
     * Superadmin → /dashboard, Admin → /admin/dashboard, Kasir → /kasir.
     */
    private function redirectByRole(string $role)
    {
        return match ($role) {
            'Superadmin' => redirect('/dashboard'),
            'Admin'      => redirect('/admin/dashboard'),
            'Kasir'      => redirect('/kasir'),
            default      => redirect('/'),
        };
    }
}
