<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/order_detail.css') }}">
    @endpush

    <div class="container">
        <div class="page-header">
            <h1>Detail Pesanan</h1>
            <p>{{ $transaction->transaction_code }}</p>
        </div>

        <div class="detail-container">
            <!-- Order Status Card -->
            <div class="detail-card">
                <h2>Status Pesanan</h2>
                <div class="status-section">
                    <span class="status-badge-large status-{{ $transaction->status }}">
                        @switch($transaction->status)
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
                                {{ ucfirst($transaction->status) }}
                        @endswitch
                    </span>

                    <div class="status-timeline">
                        <div class="timeline-item {{ in_array($transaction->status, ['pending', 'processed', 'shipped', 'completed']) ? 'active' : '' }}">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <div class="timeline-title">Pesanan Dibuat</div>
                                <div class="timeline-date">{{ $transaction->created_at->format('d M Y H:i') }}</div>
                            </div>
                        </div>

                        <div class="timeline-item {{ in_array($transaction->status, ['processed', 'shipped', 'completed']) ? 'active' : '' }}">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <div class="timeline-title">Diproses</div>
                            </div>
                        </div>

                        <div class="timeline-item {{ in_array($transaction->status, ['shipped', 'completed']) ? 'active' : '' }}">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <div class="timeline-title">Dikirim</div>
                            </div>
                        </div>

                        <div class="timeline-item {{ $transaction->status === 'completed' ? 'active' : '' }}">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <div class="timeline-title">Selesai</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Update Status Form -->
                @if($transaction->status !== 'completed' && $transaction->status !== 'cancelled')
                    <div class="status-actions">
                        <h3>Update Status</h3>
                        <form method="POST" action="{{ route('seller.orders.status.update', $transaction->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <select name="status" required>
                                    <option value="">Pilih Status</option>
                                    <option value="pending" {{ $transaction->status === 'pending' ? 'selected' : '' }}>
                                        Menunggu Diproses
                                    </option>
                                    <option value="processed" {{ $transaction->status === 'processed' ? 'selected' : '' }}>
                                        Diproses
                                    </option>
                                    <option value="shipped" {{ $transaction->status === 'shipped' ? 'selected' : '' }}>
                                        Dikirim
                                    </option>
                                    <option value="completed" {{ $transaction->status === 'completed' ? 'selected' : '' }}>
                                        Selesai
                                    </option>
                                    <option value="cancelled">
                                        Dibatalkan
                                    </option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                Update Status
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Buyer Information -->
            <div class="detail-card">
                <h2>Informasi Pembeli</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Nama:</span>
                        <span class="info-value">{{ $transaction->buyer->user->name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ $transaction->buyer->user->email ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="detail-card">
                <h2>Informasi Pengiriman</h2>
                <div class="info-grid">
                    <div class="info-item full-width">
                        <span class="info-label">Alamat:</span>
                        <span class="info-value">{{ $transaction->address }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Jenis Pengiriman:</span>
                        <span class="info-value">{{ ucfirst($transaction->shipping_type) }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Biaya Pengiriman:</span>
                        <span class="info-value">Rp {{ number_format($transaction->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if($transaction->tracking_number)
                    <div class="tracking-section">
                        <span class="info-label">Nomor Resi:</span>
                        <span class="tracking-number">{{ $transaction->tracking_number }}</span>
                    </div>
                @else
                    @if($transaction->status === 'processed')
                        <div class="tracking-form-section">
                            <h3>Input Nomor Resi</h3>
                            <form method="POST" action="{{ route('seller.orders.tracking.update', $transaction->id) }}">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <input 
                                        type="text" 
                                        name="tracking_number" 
                                        required
                                        placeholder="Masukkan nomor resi"
                                    >
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    Simpan Nomor Resi
                                </button>
                            </form>
                        </div>
                    @endif
                @endif
            </div>

            <!-- Order Items -->
            <div class="detail-card">
                <h2>Produk Pesanan</h2>
                <div class="order-items-detail">
                    @foreach($transaction->transactionDetails as $detail)
                        <div class="item-detail">
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
                                    <span>Jumlah: {{ $detail->quantity }}</span>
                                    <span>Harga: Rp {{ number_format($detail->price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <div class="item-subtotal">
                                Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Summary -->
            <div class="detail-card">
                <h2>Ringkasan Pesanan</h2>
                <div class="summary-grid">
                    <div class="summary-row">
                        <span>Subtotal Produk:</span>
                        <span>Rp {{ number_format($transaction->transactionDetails->sum('subtotal'), 0, ',', '.') }}</span>
                    </div>
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
        </div>

        <!-- Back Button -->
        <div class="back-section">
            <a href="{{ route('seller.orders.index') }}" class="btn btn-secondary">
                Kembali ke Daftar Pesanan
            </a>
        </div>
    </div>
</x-app-layout>