<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Login khusus Panel Admin — halaman & URL-nya sengaja terpisah dari login
 * peserta (/masuk), supaya tidak tercampur satu form dan tidak nongol di
 * menu publik biasa. Tetap satu tabel users & satu guard yang sama; yang
 * beda cuma pintu masuknya, dan di sini kredensial yang valid tapi BUKAN
 * milik akun admin ditolak dengan pesan generik yang sama seperti kredensial
 * salah — supaya halaman ini tidak bisa dipakai buat mengecek/mengonfirmasi
 * akun peserta mana yang ada (privilege/role tidak bocor lewat pesan error).
 */
class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);

        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = 'admin-login|'.Str::transliterate(Str::lower($credentials['email'])).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', ['seconds' => $seconds]),
            ]);
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey);

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $user = Auth::user();

        if (! $user->isAdmin() || ! $user->is_active) {
            Auth::logout();
            RateLimiter::hit($throttleKey);

            // Sengaja pakai pesan yang sama persis dengan kredensial salah
            // (bukan "akun ini bukan admin") supaya form ini tidak bisa
            // dipakai buat menebak akun mana saja yang punya akses admin.
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        return redirect()
            ->intended(route('admin.dashboard'))
            ->with('notice', "Berhasil masuk sebagai admin. Selamat bekerja, {$user->name}!");
    }
}
