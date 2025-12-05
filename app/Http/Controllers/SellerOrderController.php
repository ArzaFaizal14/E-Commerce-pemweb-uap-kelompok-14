<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerOrderController extends Controller
{
    /**
     * Tampilkan daftar pesanan untuk toko seller.
     * View: order_management.blade.php
     */
    public function index()
    {
        $user  = Auth::user();
        $store = $user->store;

        // hanya seller terverifikasi yang boleh mengakses
        if (! $store || ! $store->is_verified) {
            abort(403, 'Toko Anda belum terverifikasi.');
        }

        $orders = Transaction::with([
                'buyer',
                'transactionDetails.product',
            ])
            ->where('store_id', $store->id)
            ->latest()
            ->paginate(10);

        return view('order_management', compact('orders', 'store'));
    }

    /**
     * Menampilkan detail pesanan lengkap.
     * View opsional: order_detail.blade.php
     */
    public function show(Transaction $transaction)
    {
        $user  = Auth::user();
        $store = $user->store;

        if (! $store || ! $store->is_verified || $transaction->store_id !== $store->id) {
            abort(403, 'Anda tidak berhak melihat pesanan ini.');
        }

        $transaction->load(['buyer', 'transactionDetails.product']);

        return view('order_detail', compact('transaction'));
    }

    /**
     * Update status pesanan (pending → processed → shipped → completed)
     */
    public function updateStatus(Request $request, Transaction $transaction)
    {
        $user  = Auth::user();
        $store = $user->store;

        if (! $store || ! $store->is_verified || $transaction->store_id !== $store->id) {
            abort(403, 'Anda tidak berhak mengubah status pesanan ini.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:pending,processed,shipped,completed,cancelled'],
        ]);

        $transaction->update(['status' => $validated['status']]);

        return redirect()
            ->route('seller.orders.index')
            ->with('success', 'Status pesanan berhasil diperbarui.');
    }

    /**
     * Update nomor resi (tracking number)
     */
    public function updateTracking(Request $request, Transaction $transaction)
    {
        $user  = Auth::user();
        $store = $user->store;

        if (! $store || ! $store->is_verified || $transaction->store_id !== $store->id) {
            abort(403, 'Anda tidak berhak mengubah nomor resi.');
        }

        $validated = $request->validate([
            'tracking_number' => ['required', 'string', 'max:255'],
        ]);

        $transaction->update(['tracking_number' => $validated['tracking_number']]);

        return redirect()
            ->route('seller.orders.index')
            ->with('success', 'Nomor resi berhasil diperbarui.');
    }
}
