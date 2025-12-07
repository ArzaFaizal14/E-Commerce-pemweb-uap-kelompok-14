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
        $store = Auth::user()->store; // sudah dijamin ada & verified

        $orders = Transaction::with([
                'buyer',
                'transactionDetails.product',
            ])
            ->where('store_id', $store->id) // ownership: hanya pesanan ke toko ini
            ->latest()
            ->paginate(10);

        return view('seller.order_management', compact('orders', 'store'));
    }

    /**
     * Menampilkan detail pesanan lengkap.
     * View: order_detail.blade.php
     */
    public function show(Transaction $transaction)
    {
        $store = Auth::user()->store;

        // ownership check
        if ($transaction->store_id !== $store->id) {
            abort(403, 'Anda tidak berhak melihat pesanan ini.');
        }

        $transaction->load(['buyer', 'transactionDetails.product']);

        return view('seller.order_detail', compact('transaction'));
    }

    /**
     * Update status pembayaran pesanan (unpaid → paid).
     * Di DB: kolom payment_status enum('unpaid','paid')
     */
    public function updateStatus(Transaction $transaction)
    {
        $store = Auth::user()->store;

        if ($transaction->store_id !== $store->id) {
            abort(403, 'Anda tidak berhak mengubah status pesanan ini.');
        }

        // Kalau sudah paid, tidak perlu diubah lagi
        if ($transaction->payment_status === 'paid') {
            return redirect()
                ->route('seller.orders.index')
                ->with('info', 'Transaksi ini sudah ditandai sebagai paid.');
        }

        // Tandai sebagai sudah dibayar
        $transaction->update([
            'payment_status' => 'paid',
        ]);

        return redirect()
            ->route('seller.orders.index')
            ->with('success', 'Status pembayaran berhasil diperbarui menjadi paid.');
    }

    /**
     * Update nomor resi (tracking number)
     */
    public function updateTracking(Request $request, Transaction $transaction)
    {
        $store = Auth::user()->store;

        if ($transaction->store_id !== $store->id) {
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
