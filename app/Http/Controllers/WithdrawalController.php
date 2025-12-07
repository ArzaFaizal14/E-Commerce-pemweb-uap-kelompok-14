<?php

namespace App\Http\Controllers;

use App\Models\StoreBalance;
use App\Models\StoreBalanceHistory;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    /**
     * Withdrawal Page:
     *  - Tampilkan saldo toko
     *  - Form request withdrawal
     *  - Riwayat withdrawal
     */
    public function index()
    {
        $store = Auth::user()->store; // sudah dijamin ada & verified oleh middleware

        // Ambil atau buat saldo toko
        $balance = StoreBalance::firstOrCreate(
            ['store_id' => $store->id],
            ['balance' => 0]
        );

        // Riwayat withdrawal milik saldo toko ini
        $withdrawals = Withdrawal::where('store_balance_id', $balance->id)
            ->latest()
            ->paginate(10);

        // Ambil data bank terakhir (kalau ada) untuk prefill form
        $lastWithdrawal = Withdrawal::where('store_balance_id', $balance->id)
            ->latest()
            ->first();

        return view('seller.store_withdrawal', [
            'store'          => $store,
            'balance'        => $balance,
            'withdrawals'    => $withdrawals,
            'lastWithdrawal' => $lastWithdrawal,
        ]);
    }

    /**
     * Request withdrawal baru.
     */
    public function store(Request $request)
    {
        $store = Auth::user()->store; // sudah dijamin oleh middleware

        $balance = StoreBalance::firstOrCreate(
            ['store_id' => $store->id],
            ['balance' => 0]
        );

        $validated = $request->validate([
            'amount'              => ['required', 'numeric', 'min:1'],
            'bank_account_name'   => ['required', 'string', 'max:255'],
            'bank_account_number' => ['required', 'string', 'max:255'],
            'bank_name'           => ['required', 'string', 'max:255'],
        ]);

        // Cek saldo cukup
        if ($validated['amount'] > $balance->balance) {
            return back()
                ->withErrors(['amount' => 'Saldo tidak mencukupi untuk penarikan.'])
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Buat withdrawal dengan status pending
            $withdrawal = Withdrawal::create([
                'store_balance_id'    => $balance->id,
                'amount'              => $validated['amount'],
                'bank_account_name'   => $validated['bank_account_name'],
                'bank_account_number' => $validated['bank_account_number'],
                'bank_name'           => $validated['bank_name'],
                'status'              => 'pending',
            ]);

            // Kurangi saldo sekarang (anggap saldo ditahan saat pending)
            $balance->update([
                'balance' => $balance->balance - $validated['amount'],
            ]);

            // Catat di store_balance_histories
            StoreBalanceHistory::create([
                'store_balance_id' => $balance->id,
                'type'             => 'withdraw',
                'reference_id'     => (string) $withdrawal->id,
                'reference_type'   => Withdrawal::class,
                'amount'           => $validated['amount'],
                'remarks'          => 'Request withdrawal #' . $withdrawal->id,
            ]);

            DB::commit();

            return redirect()
                ->route('seller.withdrawals.index')
                ->with('success', 'Permintaan withdrawal berhasil dibuat dan sedang diproses.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withErrors('Terjadi kesalahan saat membuat permintaan withdrawal.')
                ->withInput();
        }
    }
}
