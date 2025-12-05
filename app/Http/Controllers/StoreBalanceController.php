<?php

namespace App\Http\Controllers;

use App\Models\StoreBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreBalanceController extends Controller
{
    /**
     * Store Balance Page
     * Menampilkan:
     *  - saldo toko sekarang
     *  - riwayat saldo (income / withdraw)
     */
    public function index(Request $request)
    {
        $user  = Auth::user();
        $store = $user->store;

        // Hanya store yang sudah terverifikasi yang boleh akses
        if (! $store || ! $store->is_verified) {
            abort(403, 'Toko Anda belum terverifikasi.');
        }

        // Ambil atau buat record saldo untuk store ini
        $balance = StoreBalance::firstOrCreate(
            ['store_id' => $store->id],
            ['balance'  => 0]
        );

        // Ambil riwayat saldo (income / withdraw) terbaru
        $historiesQuery = $balance->storeBalanceHistories()->latest();

        // (opsional) filter berdasarkan type jika nanti dibutuhkan:
        if ($request->filled('type') && in_array($request->type, ['income', 'withdraw'])) {
            $historiesQuery->where('type', $request->type);
        }

        $histories = $historiesQuery->paginate(10);

        return view('store_balance', compact('store', 'balance', 'histories'));
    }
}
