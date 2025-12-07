<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/order_management.css') }}">
    @endpush

    <div class="container">
        <div class="page-header">
            <h1>Manajemen Pesanan</h1>
            <p>Kelola pesanan masuk ke toko Anda</p>
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

        <!-- Navigation Tabs -->
        <div class="seller-tabs">
            <a href="{{ route('seller.products.index') }}" class="tab-item">
                Produk Saya
            </a>
            <a href="{{ route('seller.categories.index') }}" class="tab-item">
                Kategori Produk
            </a>
            <a href="{{ route('seller.orders.index') }}" class="tab-item active">
                Pesanan
            </a>
            <a href="{{ route('store.balance.index') }}" class="tab-item">
                Saldo Toko
            </a>
            <a href="{{ route('seller.withdrawals.index') }}" class="tab-item">
                Penarikan Dana
            </a>
        </div>

        <!-- Orders List -->
        <div class="orders-container">
            @if($orders->count() > 0)
                @foreach($orders as $order)
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-info">
                                <h3>{{ $order->code }}</h3>
                                <span class="order-date">
                                    {{ $order->created_at->format('d M Y H:i') }}
                                </span>
                            </div>
                            <div class="order-status">
                                <span class="status-badge status-{{ $order->payment_status }}">
                                    @switch($order->payment_status)
                                        @case('unpaid')
                                            Belum Bayar
                                            @break
                                        @case('pending')
                                            Menunggu Diproses
                                            @break
                                        @case('paid')
                                            Sudah Bayar
                                            @break
                                        @case('cancelled')
                                            Dibatalkan
                                            @break
                                        @default
                                            {{ ucfirst($order->payment_status) }}
                                    @endswitch
                                </span>
                            </div>
                        </div>

                        <!-- Buyer Info -->
                        <div class="buyer-info">
                            <strong>Pembeli:</strong> {{ $order->buyer->user->name ?? 'N/A' }}
                        </div>

                        <!-- Order Items -->
                        <div class="order-items">
                            @foreach($order->transactionDetails as $detail)
                                <div class="order-item">
                                    <div class="item-image">
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
                                    <div class="item-info">
                                        <h4>{{ $detail->product->name }}</h4>
                                        <div class="item-meta">
                                            {{ $detail->qty }} x Rp {{ number_format($detail->product->price, 0, ',', '.') }}
                                        </div>
                                    </div>
                                    <div class="item-subtotal">
                                        Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Shipping Info -->
                        <div class="shipping-info">
                            <div class="shipping-row">
                                <span class="label">Alamat Pengiriman:</span>
                                <span class="value">{{ $order->address }}, {{ $order->city }}, {{ $order->postal_code }}</span>
                            </div>
                            <div class="shipping-row">
                                <span class="label">Kurir:</span>
                                <span class="value">{{ $order->shipping }} - {{ ucfirst($order->shipping_type) }}</span>
                            </div>
                            @if($order->tracking_number)
                                <div class="shipping-row">
                                    <span class="label">No. Resi:</span>
                                    <span class="value tracking-number">{{ $order->tracking_number }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Order Total -->
                        <div class="order-total">
                            <div class="total-row">
                                <span class="label">
                                    Subtotal Produk ({{ $order->transactionDetails->sum('qty') }} item):
                                </span>
                                <span class="value">
                                    Rp {{ number_format($order->transactionDetails->sum('subtotal'), 0, ',', '.') }}
                                </span>
                            </div>
                            @if($order->tax > 0)
                                <div class="total-row">
                                    <span class="label">Pajak:</span>
                                    <span class="value">Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="total-divider"></div>
                            <div class="total-row total-grand">
                                <span class="label">Total Pesanan:</span>
                                <span class="value">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Order Actions -->
                        <div class="order-actions">
                            @if($order->payment_status === 'paid')
                                <form 
                                    method="POST" 
                                    action="{{ route('seller.orders.updateStatus', $order->id) }}"
                                    style="display: inline;"
                                    onsubmit="return confirm('Barang akan dikirim');"
                                >
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="processed">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        Proses Pengiriman
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach

                <!-- Pagination -->
                <div class="pagination-wrapper">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="empty-state">
                    <h3>Belum ada pesanan</h3>
                    <p>Pesanan akan muncul di sini ketika ada pembeli</p>
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