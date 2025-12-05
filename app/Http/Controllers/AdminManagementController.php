<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminManagementController extends Controller
{
    /**
     * User & Store Management Page
     */
    public function index()
    {
        // Extra safety (di luar middleware role:admin)
        if (! Auth::user() || Auth::user()->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat mengakses halaman ini.');
        }

        $users = User::with('store')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stores = Store::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('user_store_management', compact('users', 'stores'));
    }

    /**
     * Hapus user (dan toko miliknya jika ada).
     */
    public function destroyUser(User $user)
    {
        $admin = Auth::user();

        if (! $admin || $admin->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat menghapus user.');
        }

        // Jangan biarkan admin menghapus dirinya sendiri
        if ($admin->id === $user->id) {
            return redirect()
                ->route('admin.users-stores.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Jika user punya store, hapus juga store-nya
        if ($user->store) {
            $store = $user->store;

            // Hapus logo fisik jika ada
            if ($store->logo) {
                Storage::disk('public')->delete($store->logo);
            }

            $store->delete();
        }

        $user->delete();

        return redirect()
            ->route('admin.users-stores.index')
            ->with('success', 'User (dan toko miliknya jika ada) berhasil dihapus.');
    }

    /**
     * Hapus store saja (tanpa menghapus user).
     */
    public function destroyStore(Store $store)
    {
        $admin = Auth::user();

        if (! $admin || $admin->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat menghapus store.');
        }

        // Hapus logo fisik jika ada
        if ($store->logo) {
            Storage::disk('public')->delete($store->logo);
        }

        $store->delete();

        return redirect()
            ->route('admin.users-stores.index')
            ->with('success', 'Store berhasil dihapus.');
    }
}
