<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Tampilkan list semua transaksi milik buyer yang sedang login.
     */
    public function index()
    {
        $buyer = Auth::user()->buyer; // dijamin ada oleh middleware

        $transactions = Transaction::with([
                'store',
                'transactionDetails.product.productImages',
            ])
            ->where('buyer_id', $buyer->id)  // ownership check
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('riwayat', compact('transactions'));
    }

    /**
     * Tampilkan detail satu transaksi berdasarkan code.
     */
    public function show(string $code)
    {
        $buyer = Auth::user()->buyer;

        $transaction = Transaction::with([
                'store',
                'transactionDetails.product.productImages',
            ])
            ->where('code', $code)
            ->where('buyer_id', $buyer->id) // ownership check
            ->firstOrFail();

        return view('riwayat_detail', compact('transaction'));
    }

    /**
     * Bayar transaksi - update status dari unpaid ke paid
     */
    public function pay(Transaction $transaction)
    {
        $buyer = Auth::user()->buyer;

        // Ownership check
        if ($transaction->buyer_id !== $buyer->id) {
            abort(403, 'Anda tidak berhak membayar transaksi ini.');
        }

        // Hanya bisa bayar jika status unpaid
        if ($transaction->payment_status !== 'unpaid') {
            return redirect()
                ->route('transactions.index')
                ->with('error', 'Transaksi ini tidak dapat dibayar.');
        }

        // Update status menjadi paid
        $transaction->update([
            'payment_status' => 'paid'
        ]);

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Pembayaran berhasil! Pesanan Anda akan segera diproses oleh penjual.');
    }
}