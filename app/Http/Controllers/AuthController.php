<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman masuk (login).
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Terima submit form login: validasi kredensial (dengan rate limiting
     * ala Laravel Breeze), buat sesi, lalu arahkan ke halaman yang tadinya
     * dituju (atau dasbor kalau tidak ada).
     */
    public function login(Request $request): RedirectResponse
    {
        // Email disamakan ke huruf kecil sebelum dicocokkan — kolom email di
        // database dibandingkan case-sensitive, jadi tanpa ini pengguna yang
        // emailnya ke-auto-capitalize keyboard HP saat mendaftar jadi tidak
        // bisa login lagi walau kata sandinya benar (lihat User::email()).
        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);

        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($credentials['email']).'|'.$request->ip());

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

        if (! Auth::user()->is_active) {
            Auth::logout();
            RateLimiter::hit($throttleKey);

            throw ValidationException::withMessages([
                'email' => 'Akun ini telah dinonaktifkan. Hubungi admin untuk info lebih lanjut.',
            ]);
        }

        if (Auth::user()->isAdmin()) {
            Auth::logout();

            // Kredensialnya benar dan ini memang akunnya sendiri, jadi aman
            // memberi tahu ini akun admin — bukan info yang bocor ke pihak
            // lain. Halaman login peserta sengaja tidak dipakai buat masuk
            // sebagai admin; arahkan ke halaman login admin yang terpisah.
            throw ValidationException::withMessages([
                'email' => 'Ini akun admin. Silakan masuk lewat halaman login khusus admin.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        return redirect()
            ->intended(route('dashboard.index'))
            ->with('notice', 'Berhasil masuk. Selamat belajar!');
    }

    /**
     * Keluar dari akun (logout).
     */
    public function logout(Request $request): RedirectResponse
    {
        // Admin yang logout dari Panel Admin diarahkan balik ke halaman
        // login admin (bukan beranda publik) — konsisten dengan pintu masuk
        // yang mereka pakai (lihat Admin\AuthController).
        $wasAdmin = Auth::user()?->isAdmin() ?? false;

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route($wasAdmin ? 'admin.login' : 'courses.index')
            ->with('notice', 'Kamu berhasil keluar.');
    }

    /**
     * Tampilkan halaman pendaftaran akun.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Terima submit form pendaftaran: validasi, buat akun (role default
     * 'pelajar'), lalu langsung masukkan (login) pengguna.
     */
    public function register(Request $request): RedirectResponse
    {
        // Normalisasi sebelum validasi juga, supaya aturan unique:users,email
        // membandingkan huruf kecil vs huruf kecil (mencegah akun duplikat
        // seperti "Ricky@Gmail.com" vs "ricky@gmail.com").
        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->intended(route('dashboard.index'))
            ->with('notice', "Akun berhasil dibuat. Selamat datang, {$user->name}!");
    }

    /**
     * Tampilkan halaman lupa kata sandi.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Terima submit form lupa kata sandi: kirim tautan reset ke email lewat
     * password broker bawaan Laravel (tabel password_reset_tokens). Tautan
     * di email otomatis mengarah ke route 'password.reset' (halaman atur
     * kata sandi baru) karena nama route-nya sudah sesuai konvensi Laravel.
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);

        $request->validate(['email' => ['required', 'string', 'email']]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('notice', trans($status))
            : back()->withErrors(['email' => trans($status)]);
    }

    /**
     * Tampilkan halaman atur kata sandi baru — halaman yang dituju tautan
     * reset dari email (berisi token). $token & ?email= sekadar diteruskan
     * ke form untuk sekarang; validasi token sungguhan menyusul di task
     * backend Fase 3.
     */
    public function showResetPasswordForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    /**
     * Terima submit form atur kata sandi baru: validasi token lewat
     * password broker, lalu simpan kata sandi baru kalau token valid.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);

        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => $password])->save();
            },
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('notice', trans($status))
            : back()->withErrors(['email' => trans($status)]);
    }
}
