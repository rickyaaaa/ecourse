<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Sesi/token CSRF kedaluwarsa (mis. tab dibiarkan lama, atau sesi
        // sempat direset) sebelumnya nampilin halaman stock "419 PAGE
        // EXPIRED" yang gelap & tidak jelas. Balikin saja ke halaman
        // sebelumnya dengan pesan yang jelas — form-nya masih ada, tinggal
        // isi ulang & submit lagi.
        //
        // Catatan: TokenMismatchException DIUBAH jadi HttpException(419, ...)
        // polos oleh Handler::prepareException() sebelum renderable callback
        // manapun dicek (lihat vendor Handler.php), jadi closure ini harus
        // type-hint HttpException + cek status code-nya, bukan
        // TokenMismatchException — kalau tidak, closure ini tidak akan
        // pernah kepanggil sama sekali.
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() !== 419) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sesi kamu sudah berakhir. Silakan coba lagi.'], 419);
            }

            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->with('error', 'Sesi kamu sudah berakhir (mungkin karena halaman dibiarkan terbuka lama). Silakan coba lagi.');
        });
    })->create();
