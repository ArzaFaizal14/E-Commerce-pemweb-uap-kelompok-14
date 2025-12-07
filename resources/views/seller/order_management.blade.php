<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/order_management.css') }}">
    @endpush

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
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
                                <h3>{{ $order->transaction_code }}</h3>
                                <span class="order-date">
                                    {{ $order->created_at->format('d M Y H:i') }}
                                </span>
                            </div>
                            <div class="order-status">
                                <span class="status-badge status-{{ $order->status }}">
                                    @switch($order->status)
                                        @case('pending')
                                            Menunggu Diproses
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
                                            {{ ucfirst($order->status) }}
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
                                            {{ $detail->quantity }} x Rp {{ number_format($detail->price, 0, ',', '.') }}
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
                                <span class="value">{{ $order->address }}</span>
                            </div>
                            <div class="shipping-row">
                                <span class="label">Jenis Pengiriman:</span>
                                <span class="value">{{ ucfirst($order->shipping_type) }}</span>
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
                            <span class="label">Total Pesanan:</span>
                            <span class="value">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </div>

                        <!-- Order Actions -->
                        <div class="order-actions">
                            <a 
                                href="{{ route('seller.orders.show', $order->id) }}" 
                                class="btn btn-secondary btn-sm"
                            >
                                Lihat Detail
                            </a>

                            @if($order->status === 'pending')
                                <form 
                                    method="POST" 
                                    action="{{ route('seller.orders.status.update', $order->id) }}"
                                    style="display: inline;"
                                >
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="processed">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        Proses Pesanan
                                    </button>
                                </form>
                            @endif

                            @if($order->status === 'processed')
                                <button 
                                    type="button" 
                                    class="btn btn-primary btn-sm"
                                    onclick="showTrackingModal({{ $order->id }})"
                                >
                                    Kirim Pesanan
                                </button>
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

    <!-- Tracking Number Modal -->
    <div id="trackingModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Input Nomor Resi</h2>
                <span class="modal-close" onclick="closeTrackingModal()">&times;</span>
            </div>
            <form id="trackingForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="tracking_number">Nomor Resi <span class="required">*</span></label>
                        <input 
                            type="text" 
                            id="tracking_number" 
                            name="tracking_number" 
                            required
                            placeholder="Masukkan nomor resi pengiriman"
                        >
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeTrackingModal()">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Kirim Pesanan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function showTrackingModal(orderId) {
                const modal = document.getElementById('trackingModal');
                const form = document.getElementById('trackingForm');
                form.action = `/seller/orders/${orderId}/tracking`;
                modal.style.display = 'flex';
            }

            function closeTrackingModal() {
                const modal = document.getElementById('trackingModal');
                modal.style.display = 'none';
                document.getElementById('tracking_number').value = '';
            }

            // Close modal when clicking outside
            window.onclick = function(event) {
                const modal = document.getElementById('trackingModal');
                if (event.target === modal) {
                    closeTrackingModal();
                }
            }
        </script>
    @endpush
</x-app-layout>