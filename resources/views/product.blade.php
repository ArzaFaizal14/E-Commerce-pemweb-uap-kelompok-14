<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/product.css') }}">
    @endpush

    <div class="container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}">Beranda</a>
            <span>/</span>
            <span>{{ $product->name }}</span>
        </div>

        <div class="product-detail">
            <!-- Product Images Section -->
            <div class="product-images-section">
                <div class="main-image">
                    @php
                        $thumbnail = $product->productImages->where('is_thumbnail', true)->first() 
                                  ?? $product->productImages->first();
                    @endphp
                    
                    @if($thumbnail)
                        <img 
                            src="{{ asset('storage/' . $thumbnail->image) }}" 
                            alt="{{ $product->name }}"
                            id="mainImage"
                        >
                    @else
                        <div class="no-image-large">Tidak ada gambar</div>
                    @endif
                </div>

                @if($product->productImages->count() > 1)
                    <div class="thumbnail-images">
                        @foreach($product->productImages as $image)
                            <div class="thumbnail-item">
                                <img 
                                    src="{{ asset('storage/' . $image->image) }}" 
                                    alt="{{ $product->name }}"
                                    onclick="changeMainImage('{{ asset('storage/' . $image->image) }}')"
                                >
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Product Info Section -->
            <div class="product-info-section">
                <div class="product-header">
                    <h1>{{ $product->name }}</h1>
                    
                    <div class="product-badges">
                        @if($product->condition === 'new')
                            <span class="badge badge-new">Baru</span>
                        @else
                            <span class="badge badge-second">Bekas</span>
                        @endif
                    </div>
                </div>

                <div class="product-price-box">
                    <div class="price">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </div>
                </div>

                <div class="product-meta-info">
                    <div class="meta-item">
                        <span class="meta-label">Kategori:</span>
                        <span class="meta-value">
                            {{ $product->productCategory->name ?? 'Tanpa Kategori' }}
                        </span>
                    </div>

                    <div class="meta-item">
                        <span class="meta-label">Kondisi:</span>
                        <span class="meta-value">
                            {{ $product->condition === 'new' ? 'Baru' : 'Bekas' }}
                        </span>
                    </div>

                    <div class="meta-item">
                        <span class="meta-label">Stok:</span>
                        <span class="meta-value">{{ $product->stock }}</span>
                    </div>

                    <div class="meta-item">
                        <span class="meta-label">Berat:</span>
                        <span class="meta-value">{{ $product->weight }} gram</span>
                    </div>
                </div>

                <!-- Store Info -->
                <div class="store-info-box">
                    <h3>Informasi Toko</h3>
                    <div class="store-details">
                        @if($product->store->logo)
                            <img 
                                src="{{ asset('storage/' . $product->store->logo) }}" 
                                alt="{{ $product->store->name }}"
                                class="store-logo"
                            >
                        @endif
                        <div class="store-text">
                            <div class="store-name">{{ $product->store->name }}</div>
                            <div class="store-location">{{ $product->store->city }}</div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                @if($product->stock > 0)
                    <div class="product-actions">
                        <a 
                            href="{{ route('checkout.show', $product->id) }}" 
                            class="btn btn-primary btn-buy"
                        >
                            Beli Sekarang
                        </a>
                    </div>
                @else
                    <div class="out-of-stock">
                        <p>Produk ini sedang tidak tersedia</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Product Description -->
        <div class="product-description-section">
            <div class="section-card">
                <h2>Deskripsi Produk</h2>
                <div class="description-content">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </div>
        </div>

        <!-- Product Reviews -->
        <div class="product-reviews-section">
            <div class="section-card">
                <h2>Ulasan Produk</h2>
                
                @if($product->productReviews->count() > 0)
                    <div class="reviews-list">
                        @foreach($product->productReviews as $review)
                            <div class="review-item">
                                <div class="review-header">
                                    <div class="reviewer-name">
                                        {{ $review->buyer->user->name ?? 'Anonymous' }}
                                    </div>
                                    <div class="review-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <span class="star filled">★</span>
                                            @else
                                                <span class="star">☆</span>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                <div class="review-date">
                                    {{ $review->created_at->format('d M Y') }}
                                </div>
                                <div class="review-comment">
                                    {{ $review->comment }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="no-reviews">
                        <p>Belum ada ulasan untuk produk ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function changeMainImage(imageUrl) {
                document.getElementById('mainImage').src = imageUrl;
            }
        </script>
    @endpush
</x-app-layout>