<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoreRegistrationController extends Controller
{
    /**
     * Tampilkan form pendaftaran toko.
     */
    public function create()
    {
        $user = Auth::user();

        // Kalau user sudah punya store, jangan boleh daftar lagi
        if ($user->store) {
            return redirect()
                ->route('dashboard')
                ->with('info', 'Anda sudah memiliki toko.');
        }

        return view('store_registration');
    }

    /**
     * Proses simpan pendaftaran toko.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Cegah double store
        if ($user->store) {
            return redirect()
                ->route('dashboard')
                ->with('info', 'Anda sudah memiliki toko.');
        }

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'about'       => ['required', 'string'],
            'phone'       => ['required', 'string', 'max:255'],
            'city'        => ['required', 'string', 'max:255'],
            'address'     => ['required', 'string'],
            'postal_code' => ['required', 'string', 'max:255'],
            'logo'        => ['required', 'image', 'max:2048'], // logo wajib, bisa diubah kalau mau opsional
        ]);

        // Simpan logo ke storage
        $logoPath = $request->file('logo')->store('stores', 'public');

        $addressId = 'ADDR-' . Str::upper(Str::random(8));

        Store::create([
            'user_id'     => $user->id,
            'name'        => $validated['name'],
            'logo'        => $logoPath,
            'about'       => $validated['about'],
            'phone'       => $validated['phone'],
            'address_id'  => $addressId,
            'city'        => $validated['city'],
            'address'     => $validated['address'],
            'postal_code' => $validated['postal_code'],
            'is_verified' => 0, // default: menunggu verifikasi admin
        ]);

        return redirect()
            ->route('dashboard') // bisa nanti diganti ke route store dashboard
            ->with('success', 'Pendaftaran toko berhasil, menunggu verifikasi admin.');
    }
}
