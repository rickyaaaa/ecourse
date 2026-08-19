<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Halaman kelola peserta di panel admin — CRUD asli ke tabel users
 * (Eloquent, role 'pelajar'). Menggantikan versi sebelumnya yang masih
 * pakai data tiruan (App\Support\MockData::participants).
 */
class ParticipantController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);

        $participants = User::query()
            ->where('role', 'pelajar')
            ->withCount([
                'enrollments as courses_enrolled',
                'enrollments as courses_completed' => fn ($query) => $query->where('status', 'completed'),
            ])
            ->orderBy('name')
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'joined_at' => $user->created_at->toDateString(),
                'courses_enrolled' => $user->courses_enrolled,
                'courses_completed' => $user->courses_completed,
                'is_active' => $user->is_active,
            ]);

        return view('admin.participants.index', [
            'participants' => $participants,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'pelajar',
            'is_active' => true,
        ]);

        return redirect()->route('admin.participants.index')->with('notice', 'Peserta baru berhasil ditambahkan.');
    }

    public function toggleStatus(User $participant): RedirectResponse
    {
        $this->authorize('update', $participant);

        $participant->update(['is_active' => ! $participant->is_active]);

        $message = $participant->is_active ? 'Peserta berhasil diaktifkan.' : 'Peserta berhasil dinonaktifkan.';

        return redirect()->route('admin.participants.index')->with('notice', $message);
    }

    public function destroy(User $participant): RedirectResponse
    {
        $this->authorize('delete', $participant);

        $participant->delete();

        return redirect()->route('admin.participants.index')->with('notice', 'Peserta berhasil dihapus.');
    }
}
