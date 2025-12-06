<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreVerificationController extends Controller
{
    /**
     * Tampilkan daftar store untuk verifikasi.
     * Akses dibatasi di routes dengan middleware: auth + role:admin
     */
    public function index()
    {
        $pendingStores = Store::with('user')
            ->where('is_verified', 0)
            ->latest()
            ->paginate(10, ['*'], 'pending_page');

        $verifiedStores = Store::with('user')
            ->where('is_verified', 1)
            ->latest()
            ->paginate(10, ['*'], 'verified_page');

        return view('admin.store_verification', compact('pendingStores', 'verifiedStores'));
    }

    /**
     * Verifikasi (approve) satu store.
     */
    public function verify(Store $store)
    {
        // Kalau sudah terverifikasi, biarkan saja
        if ($store->is_verified) {
            return redirect()
                ->route('admin.stores.verifications.index')
                ->with('info', 'Toko ini sudah terverifikasi sebelumnya.');
        }

        $store->update([
            'is_verified' => 1,
        ]);

        return redirect()
            ->route('admin.stores.verifications.index')
            ->with('success', 'Toko berhasil diverifikasi.');
    }

    /**
     * Reject pengajuan store.
     * Implementasi sederhana: hapus store (member bisa daftar ulang jika mau).
     */
    public function reject(Store $store)
    {
        // Hapus logo fisik jika ada
        if ($store->logo) {
            Storage::disk('public')->delete($store->logo);
        }

        $store->delete();

        return redirect()
            ->route('admin.stores.verifications.index')
            ->with('success', 'Pengajuan toko berhasil ditolak dan dihapus.');
    }
}
