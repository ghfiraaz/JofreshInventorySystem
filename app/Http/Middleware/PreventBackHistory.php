<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware Mencegah Riwayat Kembali (Back History)
 * Mencegah browser meng-cache halaman sehingga menekan tombol
 * "back" setelah logout akan memaksa reload (yang kemudian redirect ke login).
 */
class PreventBackHistory
{
    /**
     * Menangani request yang masuk.
     * Menambahkan header anti-cache pada response.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Set header untuk mencegah caching oleh browser
        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');

        return $response;
    }
}
