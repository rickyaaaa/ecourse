<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman kelola profil pengguna yang sedang masuk.
     *
     * Route ini dilindungi middleware 'auth' (lihat routes/web.php), jadi
     * $request->user() dijamin ada — data diri (nama, email) sudah asli.
     */
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Terima submit form ubah data diri (nama & email).
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $user->fill($validated);
        $user->save();

        return redirect()
            ->route('profile.edit')
            ->with('notice', 'Profil berhasil diperbarui.');
    }

    /**
     * Terima submit form ubah kata sandi. Aturan 'current_password' bawaan
     * Laravel otomatis mencocokkan dengan kata sandi pengguna yang sedang
     * masuk (guard 'web').
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => $validated['password'],
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('notice', 'Kata sandi berhasil diubah.');
    }
}
