<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Halaman publik sekarang pakai Bootstrap (template Escul), bukan
        // Tailwind — pagination default Laravel pakai view Tailwind, yang
        // tanpa CSS Tailwind ter-load jadi tampil sebagai ikon panah
        // raksasa tak bergaya. Pindah ke view bootstrap-5 bawaan Laravel.
        Paginator::useBootstrapFive();

        // Tersedia sebagai $anchor('id') di semua view (bukan cuma
        // layouts.escul) — dipakai untuk tautan ke section beranda
        // (#katalog, #keunggulan, dst) yang bisa dipanggil dari halaman
        // manapun, tidak cuma dari dalam layout itu sendiri.
        View::share('anchor', function (string $id) {
            $onHome = request()->routeIs('courses.index');

            return ($onHome ? '' : route('courses.index')) . '#' . $id;
        });
    }
}
