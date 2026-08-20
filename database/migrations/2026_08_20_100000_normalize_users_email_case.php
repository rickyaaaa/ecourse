<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Turunkan huruf semua email yang sudah tersimpan jadi huruf kecil.
 *
 * Sebelum User::email() (mutator huruf kecil otomatis) ditambahkan, akun
 * yang emailnya sempat ke-auto-capitalize keyboard HP saat mendaftar bisa
 * tersimpan campur huruf besar/kecil. Karena kolom email dibandingkan
 * case-sensitive (khususnya di SQLite), itu bikin pengguna tidak bisa login
 * lagi dengan ejaan huruf kecil yang biasa mereka ketik, dan malah membuat
 * akun duplikat kalau mereka "daftar ulang". Migrasi ini menyamakan data
 * lama; mutator model mencegah masalah yang sama terjadi lagi ke depannya.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->select('id', 'email')->orderBy('id')->each(function (object $user): void {
            $lower = Str::lower(trim($user->email));

            if ($lower === $user->email) {
                return;
            }

            // Kalau versi huruf kecilnya sudah dipakai user lain (duplikat
            // asli, bukan cuma beda kapitalisasi), lewati saja daripada
            // melanggar unique constraint — butuh peninjauan manual.
            $clash = DB::table('users')->where('email', $lower)->where('id', '!=', $user->id)->exists();

            if ($clash) {
                return;
            }

            DB::table('users')->where('id', $user->id)->update(['email' => $lower]);
        });
    }

    public function down(): void
    {
        // Tidak ada rollback yang bermakna — data huruf besar/kecil asli
        // tidak disimpan.
    }
};
