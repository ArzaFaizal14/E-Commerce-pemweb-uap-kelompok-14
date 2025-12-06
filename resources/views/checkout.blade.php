<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
    @endpush

    <div class="container">
        <div class="page-header">
            <h1>Checkout</h1>
            <p>Lengkapi informasi pengiriman Anda</p>
        </div>

        <div class="checkout-container">
            <!-- Product Summary -->
            <div class="checkout-section">
                <div class="section-card">
                    <h2>Ringkasan Produk</h2>
                    
                    <div class="product-checkout-item">
                        <div class="product-checkout-image">
                            @php
                                $thumbnail = $product->productImages->where('is_thumbnail', true)->first() 
                                          ?? $product->productImages->first();
                            @endphp
                            
                            @if($thumbnail)
                                <img 
                                    src="{{ asset('storage/' . $thumbnail->image) }}" 
                                    alt="{{ $product->name }}"
                                >
                            @endif
                        </div>

                        <div class="product-checkout-info">
                            <h3>{{ $product->name }}</h3>
                            <p>{{ $product->productCategory->name ?? 'Tanpa Kategori' }}</p>
                            <div class="product-checkout-price">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <div class="product-checkout-details">
                        <div class="detail-row">
                            <span>Jumlah:</span>
                            <span>{{ $quantity }}</span>
                        </div>
                        <div class="detail-row">
                            <span>Subtotal:</span>
                            <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Checkout Form -->
            <div class="checkout-section">
                <div class="section-card">
                    <h2>Informasi Pengiriman</h2>

                    <form method="POST" action="{{ route('checkout.store', $product->id) }}" class="checkout-form">
                        @csrf

                        <input type="hidden" name="quantity" value="{{ $quantity }}">

                        <!-- Address -->
                        <div class="form-group">
                            <label for="address">Alamat Lengkap <span class="required">*</span></label>
                            <textarea 
                                id="address" 
                                name="address" 
                                rows="3" 
                                required
                                placeholder="Masukkan alamat lengkap pengiriman"
                            >{{ old('address') }}</textarea>
                            @error('address')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Shipping Type -->
                        <div class="form-group">
                            <label for="shipping_type">Jenis Pengiriman <span class="required">*</span></label>
                            <select id="shipping_type" name="shipping_type" required>
                                <option value="">Pilih jenis pengiriman</option>
                                <option value="regular" {{ old('shipping_type') == 'regular' ? 'selected' : '' }}>
                                    Regular (3-5 hari)
                                </option>
                                <option value="express" {{ old('shipping_type') == 'express' ? 'selected' : '' }}>
                                    Express (1-2 hari)
                                </option>
                                <option value="cargo" {{ old('shipping_type') == 'cargo' ? 'selected' : '' }}>
                                    Cargo (5-7 hari)
                                </option>
                            </select>
                            @error('shipping_type')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Shipping Cost -->
                        <div class="form-group">
                            <label for="shipping_cost">Biaya Pengiriman</label>
                            <input 
                                type="number" 
                                id="shipping_cost" 
                                name="shipping_cost" 
                                min="0" 
                                step="1000"
                                value="{{ old('shipping_cost', 0) }}"
                                placeholder="0"
                            >
                            @error('shipping_cost')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                            <small>Masukkan biaya pengiriman (opsional)</small>
                        </div>

                        <!-- Total Summary -->
                        <div class="order-summary">
                            <div class="summary-row">
                                <span>Subtotal Produk:</span>
                                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="summary-row">
                                <span>Biaya Pengiriman:</span>
                                <span id="shippingCostDisplay">Rp 0</span>
                            </div>
                            <div class="summary-row summary-total">
                                <span>Total:</span>
                                <span id="totalPriceDisplay">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-actions">
                            <a href="{{ route('products.show', $product->slug) }}" class="btn btn-secondary">
                                Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Buat Pesanan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const subtotal = {{ $subtotal }};
            const shippingCostInput = document.getElementById('shipping_cost');
            const shippingCostDisplay = document.getElementById('shippingCostDisplay');
            const totalPriceDisplay = document.getElementById('totalPriceDisplay');

            shippingCostInput.addEventListener('input', function() {
                const shippingCost = parseFloat(this.value) || 0;
                const total = subtotal + shippingCost;

                shippingCostDisplay.textContent = 'Rp ' + shippingCost.toLocaleString('id-ID');
                totalPriceDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');
            });
        </script>
    @endpush
</x-app-layout>