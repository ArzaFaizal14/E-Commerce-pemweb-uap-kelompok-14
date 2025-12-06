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

        <div class="transactions-container">
            @if($transactions->count() > 0)
                @foreach($transactions as $transaction)
                    <div class="transaction-card">
                        <div class="transaction-header">
                            <div class="transaction-info">
                                <h3>{{ $transaction->transaction_code }}</h3>
                                <span class="transaction-date">
                                    {{ $transaction->created_at->format('d M Y H:i') }}
                                </span>
                            </div>
                            <div class="transaction-status">
                                <span class="status-badge status-{{ $transaction->status }}">
                                    @switch($transaction->status)
                                        @case('pending')
                                            Menunggu Pembayaran
                                            @break
                                        @case('processed')
                                            Diproses
                                            @break
                                        @case('shipped')
                                            Dikirim
                                            @break
                                        @case('completed')
                                            Selesai
                                            @break
                                        @case('cancelled')
                                            Dibatalkan
                                            @break
                                        @default
                                            {{ ucfirst($transaction->status) }}
                                    @endswitch
                                </span>
                            </div>
                        </div>

                        <!-- Store Info -->
                        <div class="transaction-store">
                            <span class="store-label">Toko:</span>
                            <span class="store-name">{{ $transaction->store->name }}</span>
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
                                        @endif
                                    </div>

                                    <div class="detail-product-info">
                                        <h4>{{ $detail->product->name }}</h4>
                                        <div class="detail-meta">
                                            <span>{{ $detail->quantity }} x Rp {{ number_format($detail->price, 0, ',', '.') }}</span>
                                        </div>
                                    </div>

                                    <div class="detail-subtotal">
                                        Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Shipping Info -->
                        <div class="transaction-shipping">
                            <div class="shipping-item">
                                <span class="shipping-label">Alamat:</span>
                                <span class="shipping-value">{{ $transaction->address }}</span>
                            </div>
                            <div class="shipping-item">
                                <span class="shipping-label">Jenis Pengiriman:</span>
                                <span class="shipping-value">{{ ucfirst($transaction->shipping_type) }}</span>
                            </div>
                            @if($transaction->tracking_number)
                                <div class="shipping-item">
                                    <span class="shipping-label">No. Resi:</span>
                                    <span class="shipping-value tracking-number">{{ $transaction->tracking_number }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Transaction Summary -->
                        <div class="transaction-summary">
                            <div class="summary-row">
                                <span>Biaya Pengiriman:</span>
                                <span>Rp {{ number_format($transaction->shipping_cost, 0, ',', '.') }}</span>
                            </div>
                            <div class="summary-row summary-total">
                                <span>Total:</span>
                                <span>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Pagination -->
                <div class="pagination-wrapper">
                    {{ $transactions->links() }}
                </div>
            @else
                <div class="empty-state">
                    <h3>Belum ada transaksi</h3>
                    <p>Anda belum memiliki riwayat transaksi</p>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        Mulai Belanja
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>