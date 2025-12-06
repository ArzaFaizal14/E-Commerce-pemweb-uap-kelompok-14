<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @endpush

    @php
        $isAdmin = optional(auth()->user())->role === 'admin';
    @endphp

    <div class="container">
        <!-- Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning">
                {{ session('warning') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info">
                {{ session('info') }}
            </div>
        @endif
        @if($isAdmin)
            <div class="page-header">
                <h1>Selamat datang, anda adalah admin!</h1>
                <p>Silahkan akses halaman admin yang tersedia.</p>
            </div>
        @else
            <!-- Page Header -->
            <div class="page-header">
                <h1>Katalog Produk</h1>
                <p>Temukan produk terbaik untuk kebutuhan Anda</p>
            </div>

            <!-- Filter & Search Section -->
            <div class="filter-section">
                <form method="GET" action="{{ route('dashboard') }}" class="filter-form">
                    <!-- Search Bar -->
                    <div class="search-group">
                        <input 
                            type="text" 
                            name="q" 
                            placeholder="Cari produk..." 
                            value="{{ request('q') }}"
                        >
                        <button type="submit">Cari</button>
                    </div>

                    <!-- Category Filter -->
                    <div class="filter-group">
                        <label for="category">Kategori:</label>
                        <select name="category" id="category">
                            <option value="">Semua Kategori</option>
                            @foreach($categories ?? [] as $category)
                                <option 
                                    value="{{ $category->id }}" 
                                    {{ request('category') == $category->id ? 'selected' : '' }}
                                >
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort Filter -->
                    <div class="filter-group">
                        <label for="sort">Urutkan:</label>
                        <select name="sort" id="sort">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>
                                Terbaru
                            </option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                                Harga Terendah
                            </option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                                Harga Tertinggi
                            </option>
                        </select>
                    </div>

                    <button type="submit" class="btn-filter">Filter</button>
                </form>
            </div>

            <!-- Products Grid -->
            <div class="products-container">
                @if($products->count() > 0)
                    <div class="products-grid">
                        @foreach($products as $product)
                            <div class="product-card">
                                <a href="{{ route('products.show', $product->slug) }}" class="product-link">
                                    <!-- Product Image -->
                                    <div class="product-image">
                                        @php
                                            $thumbnail = $product->productImages->where('is_thumbnail', true)->first() 
                                                      ?? $product->productImages->first();
                                        @endphp
                                        
                                        @if($thumbnail)
                                            <img 
                                                src="{{ asset('storage/' . $thumbnail->image) }}" 
                                                alt="{{ $product->name }}"
                                            >
                                        @else
                                            <div class="no-image">Tidak ada gambar</div>
                                        @endif

                                        <!-- Product Badge -->
                                        @if($product->condition === 'new')
                                            <span class="badge badge-new">Baru</span>
                                        @else
                                            <span class="badge badge-second">Bekas</span>
                                        @endif
                                    </div>

                                    <!-- Product Info -->
                                    <div class="product-info">
                                        <h3 class="product-name">{{ $product->name }}</h3>
                                        
                                        <div class="product-category">
                                            {{ $product->productCategory->name ?? 'Tanpa Kategori' }}
                                        </div>

                                        <div class="product-price">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </div>

                                        <div class="product-meta">
                                            <span class="product-stock">
                                                Stok: {{ $product->stock }}
                                            </span>
                                            <span class="product-weight">
                                                {{ $product->weight }}g
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-wrapper">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="empty-state">
                        <h3>Tidak ada produk ditemukan</h3>
                        <p>Coba gunakan kata kunci atau filter lain</p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>
