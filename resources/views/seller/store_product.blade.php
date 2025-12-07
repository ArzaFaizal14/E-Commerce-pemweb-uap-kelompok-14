<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/store_dashboard.css') }}">
    @endpush

    <div class="container">
        <!-- Navigation Tabs -->
        <div class="seller-tabs">
            <a href="{{ route('seller.products.index') }}" class="tab-item active">
                Produk Saya
            </a>
            <a href="{{ route('seller.categories.index') }}" class="tab-item">
                Kategori Produk
            </a>
            <a href="{{ route('seller.orders.index') }}" class="tab-item">
                Pesanan
            </a>
            <a href="{{ route('store.balance.index') }}" class="tab-item">
                Saldo Toko
            </a>
            <a href="{{ route('seller.withdrawals.index') }}" class="tab-item">
                Penarikan Dana
            </a>
        </div>

        <!-- Store Header -->
        <div class="store-header">
            
            <div class="store-header-content">
                @if($store->logo)
                    <img 
                        src="{{ asset('storage/' . $store->logo) }}" 
                        alt="{{ $store->name }}"
                        class="store-logo-large"
                    >
                @endif
                <div class="store-header-info">
                    <h1>{{ $store->name }}</h1>
                    <p>{{ $store->city }}</p>
                    @if($store->is_verified)
                        <span class="badge badge-verified">Terverifikasi</span>
                    @else
                        <span class="badge badge-pending">Menunggu Verifikasi</span>
                    @endif
                </div>
            </div>

            <div class="store-header-actions">
                <a href="{{ route('store.profile.edit') }}" class="btn btn-secondary">
                    Edit Profil Toko
                </a>
            </div>
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

        <!-- Quick Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"></div>
                <div class="stat-content">
                    <div class="stat-value">{{ $products->total() }}</div>
                    <div class="stat-label">Total Produk</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"></div>
                <div class="stat-content">
                    <div class="stat-value">{{ $products->where('stock', '>', 0)->count() }}</div>
                    <div class="stat-label">Produk Tersedia</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"></div>
                <div class="stat-content">
                    <div class="stat-value">{{ $products->where('stock', '<=', 5)->count() }}</div>
                    <div class="stat-label">Stok Menipis</div>
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <div class="products-section">
            <div class="section-header">
                <h2>Daftar Produk</h2>
                <a href="{{ route('seller.products.create') }}" class="btn btn-primary">
                    + Tambah Produk
                </a>
            </div>

            @if($products->count() > 0)
                <div class="products-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Kondisi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>
                                        <div class="product-cell">
                                            @php
                                                $thumbnail = $product->productImages->first();
                                            @endphp
                                            
                                            @if($thumbnail)
                                                <img 
                                                    src="{{ asset('storage/' . $thumbnail->image) }}" 
                                                    alt="{{ $product->name }}"
                                                    class="product-thumb"
                                                >
                                            @endif
                                            <span>{{ $product->name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $product->productCategory->name ?? '-' }}</td>
                                    <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="stock-badge {{ $product->stock <= 5 ? 'stock-low' : '' }}">
                                            {{ $product->stock }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($product->condition === 'new')
                                            <span class="badge badge-new">Baru</span>
                                        @else
                                            <span class="badge badge-second">Bekas</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a 
                                                href="{{ route('seller.products.images', $product->id) }}" 
                                                class="btn-action btn-images"
                                                title="Kelola Gambar"
                                            >
                                                ⛶
                                            </a>
                                            <a 
                                                href="{{ route('seller.products.edit', $product->id) }}" 
                                                class="btn-action btn-edit"
                                                title="Edit"
                                            >
                                                ✐
                                            </a>
                                            <form 
                                                method="POST" 
                                                action="{{ route('seller.products.destroy', $product->id) }}"
                                                onsubmit="return confirm('Yakin ingin menghapus produk ini?')"
                                                style="display: inline;"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button 
                                                    type="submit" 
                                                    class="btn-action btn-delete"
                                                    title="Hapus"
                                                >
                                                    X
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-wrapper">
                    {{ $products->links() }}
                </div>
            @else
                <div class="empty-state">
                    <h3>Belum ada produk</h3>
                    <p>Mulai tambahkan produk untuk toko Anda</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>