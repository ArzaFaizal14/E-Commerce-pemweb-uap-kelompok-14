<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Tampilkan form checkout untuk 1 produk.
     */
    public function create(Product $product)
    {
        // Untuk sekarang anggap quantity default = 1
        $quantity = 1;

        $subtotal = $product->price * $quantity;

        return view('checkout', [
            'product'  => $product,
            'quantity' => $quantity,
            'subtotal' => $subtotal,
        ]);
    }

    /**
     * Proses checkout: buat transaksi + transaction_details.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'address'        => ['required', 'string', 'max:255'],
            'shipping_type'  => ['required', 'string', 'max:100'],
            'shipping_cost'  => ['nullable', 'numeric', 'min:0'],
            'quantity'       => ['required', 'integer', 'min:1'],
        ]);

        $user  = Auth::user();
        $buyer = $user->buyer; // asumsi relasi buyer() sudah ada di model User

        if (! $buyer) {
            // fallback kalau entah kenapa belum ada buyer record
            abort(403, 'Buyer tidak ditemukan untuk user ini.');
        }

        $quantity      = (int) $request->quantity;
        $shippingCost  = (float) ($request->shipping_cost ?? 0);
        $productPrice  = (float) $product->price;
        $subtotal      = $productPrice * $quantity;
        $totalPrice    = $subtotal + $shippingCost;

        DB::beginTransaction();

        try {
            // Buat transaksi (header)
            $transaction = Transaction::create([
                'buyer_id'          => $buyer->id,
                'store_id'          => $product->store_id,
                'transaction_code'  => 'TRX-' . Str::upper(Str::random(8)),
                'address'           => $request->address,
                'shipping_type'     => $request->shipping_type,
                'shipping_cost'     => $shippingCost,
                'total_price'       => $totalPrice,
                'status'            => 'pending', // sesuaikan dengan enum/status di DB kalau berbeda
            ]);

            // Buat detail transaksi
            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'product_id'     => $product->id,
                'quantity'       => $quantity,
                'price'          => $productPrice,
                'subtotal'       => $subtotal,
            ]);

            // kurangi stok produk
            $product->decrement('stock', $quantity);

            DB::commit();

            return redirect()
                ->route('transactions.index')
                ->with('success', 'Checkout berhasil, transaksi sudah dibuat.');
        } catch (\Throwable $e) {
            DB::rollBack();

            // Untuk development, boleh dd($e->getMessage());
            return back()->withErrors('Terjadi kesalahan saat memproses checkout.');
        }
    }
}
