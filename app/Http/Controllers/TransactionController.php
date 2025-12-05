<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Tampilkan list semua transaksi milik buyer yang sedang login.
     */
    public function index()
    {
        $user  = Auth::user();
        $buyer = $user->buyer; 

        if (! $buyer) {
            abort(403, 'Buyer tidak ditemukan untuk user ini.');
        }

        $transactions = Transaction::with([
                'store',                       // toko pemilik transaksi
                'transactionDetails.product',  // produk per transaksi
            ])
            ->where('buyer_id', $buyer->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('riwayat', compact('transactions'));
    }

    /**
     * Tampilkan detail satu transaksi berdasarkan code.
     */
    public function show(string $code)
    {
        $user  = Auth::user();
        $buyer = $user->buyer;

        if (! $buyer) {
            abort(403, 'Buyer tidak ditemukan untuk user ini.');
        }

        $transaction = Transaction::with([
                'store',
                'transactionDetails.product',
            ])
            ->where('code', $code)
            ->where('buyer_id', $buyer->id)
            ->firstOrFail();

        return view('riwayat_detail', compact('transaction'));
        // kalau kamu hanya punya riwayat.blade.php, bisa sementara:
        return view('riwayat', compact('transaction'));
    }
}
