<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminManagementController extends Controller
{
    /**
     * User & Store Management Page
     * Akses dibatasi di route dengan middleware: auth + role:admin
     */
    public function index()
    {
        $users = User::with('store')
            ->orderBy('created_at', 'asc')
            ->paginate(15, ['*'], 'users_page');

        $stores = Store::with('user')
            ->orderBy('created_at', 'asc')
            ->paginate(15, ['*'], 'stores_page');

        return view('admin.management', compact('users', 'stores'));
    }

    /**
     * Hapus user (dan toko miliknya jika ada).
     */
    public function destroyUser(User $user)
    {
        $admin = Auth::user(); // sudah pasti admin karena lewat middleware

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
        // Admin sudah dijamin oleh middleware

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
