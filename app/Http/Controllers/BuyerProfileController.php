<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BuyerProfileController extends Controller
{
    /**
     * Tampilkan form untuk membuat / melengkapi profil buyer.
     */
    public function create()
    {
        $user = Auth::user();

        // Kalau sudah punya buyer, langsung arahkan ke dashboard / halaman lain
        if ($user->buyer) {
            return redirect()
                ->route('dashboard')
                ->with('info', 'Profil pembeli Anda sudah lengkap.');
        }

        return view('buyer_profile_create', compact('user'));
    }

    /**
     * Simpan profil buyer baru.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Kalau sudah punya buyer, tidak perlu buat lagi
        if ($user->buyer) {
            return redirect()
                ->route('dashboard')
                ->with('info', 'Profil pembeli Anda sudah ada.');
        }

        $validated = $request->validate([
            'phone_number'     => ['required', 'string', 'max:255'],
            'profile_picture'  => ['nullable', 'image', 'max:2048'],
        ]);

        $profilePicturePath = null;

        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $request->file('profile_picture')->store('buyers', 'public');
        }

        Buyer::create([
            'user_id'         => $user->id,
            'phone_number'    => $validated['phone_number'],
            'profile_picture' => $profilePicturePath,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Profil pembeli berhasil dibuat.');
    }

    /**
     * Update profil buyer yang sudah ada.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $buyer = $user->buyer;

        // Kalau belum punya buyer, redirect ke create
        if (!$buyer) {
            return redirect()
                ->route('buyer.profile.create')
                ->with('warning', 'Silakan buat profil pembeli terlebih dahulu.');
        }

        $validated = $request->validate([
            'phone_number'     => ['required', 'string', 'max:255'],
            'profile_picture'  => ['nullable', 'image', 'max:2048'],
        ]);

        // Update phone number
        $buyer->phone_number = $validated['phone_number'];

        // Update profile picture jika ada upload baru
        if ($request->hasFile('profile_picture')) {
            // Hapus foto lama jika ada
            if ($buyer->profile_picture) {
                Storage::disk('public')->delete($buyer->profile_picture);
            }

            // Simpan foto baru
            $buyer->profile_picture = $request->file('profile_picture')->store('buyers', 'public');
        }

        $buyer->save();

        return redirect()
            ->route('profile.edit')
            ->with('status', 'buyer-profile-updated');
    }

    /**
     * Hapus foto profil buyer.
     */
    public function deleteProfilePicture()
    {
        $user = Auth::user();
        $buyer = $user->buyer;

        if (!$buyer || !$buyer->profile_picture) {
            return redirect()
                ->route('profile.edit')
                ->with('error', 'Tidak ada foto profil untuk dihapus.');
        }

        // Hapus foto dari storage
        Storage::disk('public')->delete($buyer->profile_picture);

        // Set profile_picture menjadi null
        $buyer->profile_picture = null;
        $buyer->save();

        return redirect()
            ->route('profile.edit')
            ->with('status', 'profile-picture-deleted');
    }
}