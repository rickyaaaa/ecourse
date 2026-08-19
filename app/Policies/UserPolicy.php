<?php

namespace App\Policies;

use App\Models\User;

/**
 * Otorisasi untuk aksi "Kelola Peserta" di panel admin. Middleware 'admin'
 * (lihat routes/web.php) sudah memastikan hanya admin yang bisa mencapai
 * controller-nya sama sekali; policy ini menambah lapisan kedua khusus
 * untuk aksi per-baris (ubah status/hapus) supaya seorang admin tidak
 * bisa menonaktifkan/menghapus admin lain (atau dirinya sendiri) lewat
 * halaman ini.
 */
class UserPolicy
{
    /**
     * Admin boleh melihat daftar peserta.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Admin boleh menambah peserta baru.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Admin boleh mengubah status (aktif/nonaktif) peserta — tapi bukan
     * sesama admin, termasuk dirinya sendiri.
     */
    public function update(User $user, User $model): bool
    {
        return $user->isAdmin() && ! $model->isAdmin();
    }

    /**
     * Sama seperti update: admin tidak boleh menghapus sesama admin.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->isAdmin() && ! $model->isAdmin();
    }
}
