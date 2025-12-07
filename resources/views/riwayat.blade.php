<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/riwayat_belanja.css') }}">
    @endpush

    <div class="container">
        <div class="page-header">
            <h1>Riwayat Transaksi</h1>
            <p>Lihat semua pesanan Anda</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <div class="transactions-container">
            @if($transactions->count() > 0)
                @foreach($transactions as $transaction)
                    <div class="transaction-card">
                        <div class="transaction-header">
                            <div class="transaction-info">
                                <h3>{{ $transaction->code }}</h3>
                                <span class="transaction-date">
                                    {{ $transaction->created_at->format('d M Y H:i') }}
                                </span>
                            </div>
                            <div class="transaction-status">
                                <span class="status-badge status-{{ $transaction->payment_status }}">
                                    @switch($transaction->payment_status)
                                        @case('unpaid')
                                            Belum Bayar
                                            @break
                                        @case('pending')
                                            Menunggu Konfirmasi
                                            @break
                                        @case('paid')
                                            Sudah Bayar
                                            @break
                                        @case('cancelled')
                                            Dibatalkan
                                            @break
                                        @case('refunded')
                                            Dikembalikan
                                            @break
                                        @default
                                            {{ ucfirst($transaction->payment_status) }}
                                    @endswitch
                                </span>
                            </div>
                        </div>

                        <!-- Store Info -->
                        <div class="transaction-store">
                            <div class="store-info-row">
                                <span class="store-label">Toko:</span>
                                <span class="store-name">{{ $transaction->store->name }}</span>
                            </div>
                            @if($transaction->tracking_number)
                                <div class="store-info-row">
                                    <span class="store-label">No. Resi:</span>
                                    <span class="tracking-number">{{ $transaction->tracking_number }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Transaction Details -->
                        <div class="transaction-details">
                            @foreach($transaction->transactionDetails as $detail)
                                <div class="detail-item">
                                    <div class="detail-product-image">
                                        @php
                                            $thumbnail = $detail->product->productImages
                                                ->where('is_thumbnail', true)->first() 
                                                ?? $detail->product->productImages->first();
                                        @endphp
                                        
                                        @if($thumbnail)
                                            <img 
                                                src="{{ asset('storage/' . $thumbnail->image) }}" 
                                                alt="{{ $detail->product->name }}"
                                            >
                                        @else
                                            <div class="no-image">No Image</div>
                                        @endif
                                    </div>

                                    <div class="detail-product-info">
                                        <h4>{{ $detail->product->name }}</h4>
                                        <div class="detail-meta">
                                            <span class="meta-qty">{{ $detail->qty }} x</span>
                                            <span class="meta-price">Rp {{ number_format($detail->product->price, 0, ',', '.') }}</span>
                                        </div>
                                    </div>

                                    <div class="detail-subtotal">
                                        <span class="subtotal-value">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Shipping Info -->
                        <div class="transaction-shipping">
                            <h4>Informasi Pengiriman</h4>
                            <div class="shipping-grid">
                                <div class="shipping-item">
                                    <span class="shipping-label">Alamat:</span>
                                    <span class="shipping-value">{{ $transaction->address }}, {{ $transaction->city }}, {{ $transaction->postal_code }}</span>
                                </div>
                                <div class="shipping-item">
                                    <span class="shipping-label">Kurir:</span>
                                    <span class="shipping-value">{{ $transaction->shipping }} - {{ ucfirst($transaction->shipping_type) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Transaction Summary -->
                        <div class="transaction-summary">
                            <h4>Ringkasan Pembayaran</h4>
                            <div class="summary-grid">
                                <div class="summary-row">
                                    <span>Subtotal Produk ({{ $transaction->transactionDetails->sum('qty') }} item):</span>
                                    <span>Rp {{ number_format($transaction->transactionDetails->sum('subtotal'), 0, ',', '.') }}</span>
                                </div>
                                @if($transaction->tax > 0)
                                    <div class="summary-row">
                                        <span>Pajak:</span>
                                        <span>Rp {{ number_format($transaction->tax, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                <div class="summary-divider"></div>
                                <div class="summary-row summary-total">
                                    <span>Total Pembayaran:</span>
                                    <span>Rp {{ number_format($transaction->grand_total ?? 0, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Transaction Actions -->
                        <div class="transaction-actions">
                            @if($transaction->payment_status === 'unpaid')
                                <form 
                                    method="POST" 
                                    action="{{ route('transactions.pay', $transaction->id) }}"
                                    style="display: inline-block;"
                                    onsubmit="return confirm('Konfirmasi pembayaran untuk transaksi ini?');"
                                >
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        Bayar Sekarang
                                    </button>
                                </form>
                            @endif

                            @if(in_array($transaction->payment_status, ['unpaid', 'pending']))
                                <form 
                                    method="POST" 
                                    action="{{ route('transactions.cancel', $transaction->id) }}"
                                    style="display: inline-block;"
                                    onsubmit="return confirm('Yakin ingin membatalkan transaksi ini? Stok produk akan dikembalikan.');"
                                >
                            @endif
                        </div>
                    </div>
                @endforeach

                <!-- Pagination -->
                <div class="pagination-wrapper">
                    {{ $transactions->links() }}
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon"></div>
                    <h3>Belum ada transaksi</h3>
                    <p>Anda belum memiliki riwayat transaksi</p>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        Mulai Belanja
                    </a>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    setTimeout(() => {
                        alert.style.transition = 'opacity 0.3s';
                        alert.style.opacity = '0';
                        setTimeout(() => alert.remove(), 300);
                    }, 5000);
                });
            });
        </script>
    @endpush
</x-app-layout>