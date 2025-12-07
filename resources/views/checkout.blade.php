<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
    @endpush

    <div class="container">
        <div class="page-header">
            <h1>Checkout</h1>
            <p>Lengkapi informasi pengiriman Anda</p>
        </div>

        {{-- Display validation errors --}}
        @if($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="checkout-container">
            <!-- ringkasan produk -->
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
                                Rp {{ number_format($product->price, 0, ',', '.') }} / pcs
                            </div>
                            <div class="product-meta">
                                <span>Berat: {{ $product->weight }} gram</span>
                                <span>Stok: {{ $product->stock }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- form checkout -->
            <div class="checkout-section">
                <div class="section-card">
                    <h2>Informasi Pengiriman</h2>

                    <form method="POST" action="{{ route('checkout.store', $product->id) }}" class="checkout-form" id="checkoutForm">
                        @csrf

                        <!-- jumlah -->
                        <div class="form-group">
                            <label for="quantity">Jumlah <span class="required">*</span></label>
                            <input 
                                type="number" 
                                id="quantity" 
                                name="quantity" 
                                value="{{ old('quantity', 1) }}"
                                min="1"
                                max="{{ $product->stock }}"
                                step="1"
                                required
                            >
                            <small>Maksimal: {{ $product->stock }} pcs</small>
                            @error('quantity')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- alamat -->
                        <div class="form-group">
                            <label for="address">Alamat Lengkap <span class="required">*</span></label>
                            <textarea 
                                id="address" 
                                name="address" 
                                rows="3" 
                                required
                                placeholder="Masukkan alamat lengkap pengiriman (nama jalan, nomor rumah, RT/RW)"
                            >{{ old('address') }}</textarea>
                            @error('address')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- kota -->
                        <div class="form-group">
                            <label for="city">Kota <span class="required">*</span></label>
                            <input 
                                type="text" 
                                id="city" 
                                name="city" 
                                value="{{ old('city') }}"
                                required
                                placeholder="Contoh: Surabaya"
                            >
                            @error('city')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- kode Pos -->
                        <div class="form-group">
                            <label for="postal_code">Kode Pos <span class="required">*</span></label>
                            <input 
                                type="text" 
                                id="postal_code" 
                                name="postal_code" 
                                value="{{ old('postal_code') }}"
                                required
                                placeholder="Contoh: 60119"
                            >
                            @error('postal_code')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- kurir -->
                        <div class="form-group">
                            <label for="shipping">Kurir <span class="required">*</span></label>
                            <select id="shipping" name="shipping" required>
                                <option value="">Pilih kurir pengiriman</option>
                                <option value="JNE" {{ old('shipping') == 'JNE' ? 'selected' : '' }}>JNE</option>
                                <option value="J&T" {{ old('shipping') == 'J&T' ? 'selected' : '' }}>J&T Express</option>
                                <option value="SiCepat" {{ old('shipping') == 'SiCepat' ? 'selected' : '' }}>SiCepat</option>
                                <option value="AnterAja" {{ old('shipping') == 'AnterAja' ? 'selected' : '' }}>AnterAja</option>
                                <option value="Pos Indonesia" {{ old('shipping') == 'Pos Indonesia' ? 'selected' : '' }}>Pos Indonesia</option>
                            </select>
                            @error('shipping')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- jenis layanan -->
                        <div class="form-group">
                            <label for="shipping_type">Jenis Layanan <span class="required">*</span></label>
                            <select id="shipping_type" name="shipping_type" required>
                                <option value="">Pilih jenis layanan</option>
                                <option value="regular" data-cost="10000" {{ old('shipping_type') == 'regular' ? 'selected' : '' }}>
                                    Regular (3-5 hari)
                                </option>
                                <option value="express" data-cost="25000" {{ old('shipping_type') == 'express' ? 'selected' : '' }}>
                                    Express (1-2 hari)
                                </option>
                                <option value="cargo" data-cost="15000" {{ old('shipping_type') == 'cargo' ? 'selected' : '' }}>
                                    Cargo (5-7 hari)
                                </option>
                            </select>
                            @error('shipping_type')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Hidden shipping cost input -->
                        <input type="hidden" id="shipping_cost" name="shipping_cost" value="0">

                        <!-- Total Summary -->
                        <div class="order-summary">
                            <h3>Ringkasan Pembayaran</h3>
                            <div class="summary-row">
                                <span>Subtotal Produk (<span id="qtyDisplay">1</span> item):</span>
                                <span id="subtotalDisplay">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            </div>
                            <div class="summary-divider"></div>
                            <div class="summary-row summary-total">
                                <span>Total Pembayaran:</span>
                                <span id="totalPriceDisplay">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-actions">
                            <a href="{{ route('products.show', $product->slug) }}" class="btn btn-secondary">
                                Batal
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
            const productPrice = {{ $product->price }};
            const maxStock = {{ $product->stock }};

            const qtyInput = document.getElementById('quantity');
            const shippingTypeSelect = document.getElementById('shipping_type');
            const shippingSelect = document.getElementById('shipping');
            qtyInput.addEventListener('input', function() {
                let value = parseInt(this.value) || 1;
                
                if (value < 1) {
                    this.value = 1;
                } else if (value > maxStock) {
                    this.value = maxStock;
                }
                
                calculateTotal();
            });

            qtyInput.addEventListener('change', function() {
                let value = parseInt(this.value) || 1;
                
                if (value < 1) {
                    this.value = 1;
                } else if (value > maxStock) {
                    this.value = maxStock;
                }
                
                calculateTotal();
            });
            shippingSelect.addEventListener('change', function() {
                shippingTypeSelect.value = '';
                calculateTotal();
            });
            shippingTypeSelect.addEventListener('change', function() {
                calculateTotal();
            });

            function calculateTotal() {
                const quantity = parseInt(qtyInput.value) || 1;
                const selectedOption = shippingTypeSelect.options[shippingTypeSelect.selectedIndex];
                const shippingCost = parseInt(selectedOption.getAttribute('data-cost')) || 0;

                const subtotal = productPrice * quantity;
                const total = subtotal + shippingCost;
                document.getElementById('qtyDisplay').textContent = quantity;
                document.getElementById('subtotalDisplay').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
                document.getElementById('shippingCostDisplay').textContent = 'Rp ' + shippingCost.toLocaleString('id-ID');
                document.getElementById('totalPriceDisplay').textContent = 'Rp ' + total.toLocaleString('id-ID');
                document.getElementById('shipping_cost').value = shippingCost;
            }
            document.addEventListener('DOMContentLoaded', function() {
                calculateTotal();
            });
        </script>
    @endpush
</x-app-layout>