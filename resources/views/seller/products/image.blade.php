<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/product_images.css') }}">
    @endpush

    <div class="container">
        <div class="page-header">
            <h1>Kelola Gambar Produk</h1>
            <p>{{ $product->name }}</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Add Image Form -->
        <div class="add-image-section">
            <div class="section-card">
                <h2>Tambah Gambar Baru</h2>
                
                <form method="POST" action="{{ route('seller.products.images.store', $product->id) }}" enctype="multipart/form-data" class="image-form">
                    @csrf

                    <div class="form-row">
                        <div class="form-group">
                            <label for="image">Pilih Gambar <span class="required">*</span></label>
                            <input 
                                type="file" 
                                id="image" 
                                name="image" 
                                accept="image/*"
                                required
                            >
                            <small>Format: JPG, PNG, max 2MB</small>
                            @error('image')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="checkbox-label">
                                <input 
                                    type="checkbox" 
                                    name="is_thumbnail" 
                                    value="1"
                                    {{ old('is_thumbnail') ? 'checked' : '' }}
                                >
                                Jadikan sebagai gambar utama
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            Upload Gambar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Existing Images -->
        <div class="images-grid-section">
            <div class="section-header">
                <h2>Gambar Produk ({{ $images->count() }})</h2>
            </div>

            @if($images->count() > 0)
                <div class="images-grid">
                    @foreach($images as $image)
                        <div class="image-card">
                            <div class="image-wrapper">
                                <img 
                                    src="{{ asset('storage/' . $image->image) }}" 
                                    alt="Product Image"
                                >
                                
                                @if($image->is_thumbnail)
                                    <span class="thumbnail-badge">Gambar Utama</span>
                                @endif
                            </div>

                            <div class="image-actions">
                                <form 
                                    method="POST" 
                                    action="{{ route('seller.products.images.destroy', [$product->id, $image->id]) }}"
                                    onsubmit="return confirm('Yakin ingin menghapus gambar ini?')"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <p>Belum ada gambar untuk produk ini</p>
                </div>
            @endif
        </div>

        <!-- Back Button -->
        <div class="back-section">
            <a href="{{ route('seller.products.index') }}" class="btn btn-secondary">
                Kembali ke Daftar Produk
            </a>
        </div>
    </div>
</x-app-layout>