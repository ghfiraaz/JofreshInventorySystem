<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware Cek Role Pengguna
 * Memastikan pengguna yang login memiliki role yang sesuai
 * untuk mengakses halaman/route tertentu.
 */
class CheckRole
{
    /**
     * Menangani request yang masuk.
     * Memeriksa apakah pengguna sudah login dan memiliki role yang diizinkan.
     * 
     * @param string ...$roles Daftar role yang diizinkan (bisa lebih dari satu)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Jika pengguna belum login, redirect ke halaman login
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Jika pengguna tidak memiliki role yang diizinkan, tolak akses
        if (!in_array(Auth::user()->role, $roles)) {
            return redirect('/')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        return $next($request);
    }
}
