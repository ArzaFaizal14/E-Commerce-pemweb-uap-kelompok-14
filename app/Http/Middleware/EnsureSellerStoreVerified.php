<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureSellerStoreVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user  = Auth::user();
        $store = $user->store;

        if (! $store) {
            return redirect()
                ->route('store.registration.create') // misal form daftar toko
                ->with('info', 'Anda belum memiliki toko. Silakan daftar terlebih dahulu.');
        }

        if (! $store->is_verified) {
            return redirect()
                ->route('seller.products.index') // misal halaman produk seller
                ->with('info', 'Toko Anda belum terverifikasi oleh admin.');
        }

        return $next($request);
    }
}
