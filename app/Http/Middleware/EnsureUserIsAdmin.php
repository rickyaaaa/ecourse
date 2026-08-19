<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Batasi akses grup route panel admin hanya untuk pengguna dengan role
 * 'admin' yang akunnya masih aktif. Dipasang setelah middleware 'auth'
 * (lihat routes/web.php), jadi $request->user() dijamin ada di sini.
 *
 * Cek is_active di sini (bukan cuma saat login) supaya sesi admin yang
 * akunnya dinonaktifkan setelah login tetap langsung ditolak di request
 * berikutnya, bukan menunggu sesi kedaluwarsa.
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user?->isAdmin()) {
            abort(403, 'Halaman ini khusus untuk admin.');
        }

        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            abort(403, 'Akun ini telah dinonaktifkan.');
        }

        return $next($request);
    }
}
