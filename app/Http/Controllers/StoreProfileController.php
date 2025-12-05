<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StoreProfileController extends Controller
{
    /**
     * Tampilkan form edit profil toko.
     */
    public function edit()
    {
        $user  = Auth::user();
        $store = $user->store;

        // Hanya bisa diakses jika toko sudah diverifikasi
        if (! $store || ! $store->is_verified) {
            abort(403, 'Toko Anda belum terverifikasi.');
        }

        return view('store_profile', compact('store'));
    }

    /**
     * Update profil toko (tanpa menghapus).
     */
    public function update(Request $request)
    {
        $user  = Auth::user();
        $store = $user->store;

        // Hanya bisa diakses jika toko sudah diverifikasi
        if (! $store || ! $store->is_verified) {
            abort(403, 'Toko Anda belum terverifikasi.');
        }

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'about'       => ['required', 'string'],
            'phone'       => ['required', 'string', 'max:255'],
            'city'        => ['required', 'string', 'max:255'],
            'address'     => ['required', 'string'],
            'postal_code' => ['required', 'string', 'max:255'],
            'logo'        => ['nullable', 'image', 'max:2048'],
        ]);

        // Update data profil toko
        $store->update([
            'name'        => $validated['name'],
            'about'       => $validated['about'],
            'phone'       => $validated['phone'],
            'city'        => $validated['city'],
            'address'     => $validated['address'],
            'postal_code' => $validated['postal_code'],
            // is_verified tetap tidak boleh disentuh oleh seller
        ]);

        // Jika ada upload logo baru
        if ($request->hasFile('logo')) {
            // Hapus logo lama
            if ($store->logo) {
                Storage::disk('public')->delete($store->logo);
            }

            $logoPath = $request->file('logo')->store('stores', 'public');

            $store->update([
                'logo' => $logoPath,
            ]);
        }

        return redirect()
            ->route('store.profile.edit')
            ->with('success', 'Profil toko berhasil diperbarui.');
    }

    /**
     * Hapus toko (store) milik user.
     */
    public function destroy()
    {
        $user  = Auth::user();
        $store = $user->store;

        // Hanya bisa diakses jika toko sudah diverifikasi
        if (! $store || ! $store->is_verified) {
            abort(403, 'Toko Anda belum terverifikasi.');
        }

        // Hapus logo fisik jika ada
        if ($store->logo) {
            Storage::disk('public')->delete($store->logo);
        }

        $store->delete();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Toko berhasil dihapus.');
    }
}
