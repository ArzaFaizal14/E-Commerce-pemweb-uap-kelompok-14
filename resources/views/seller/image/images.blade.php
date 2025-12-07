<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/product_images.css') }}">
    @endpush

    <div class="container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="{{ route('seller.products.index') }}">Produk Saya</a>
            <span>/</span>
            <span>Kelola Gambar: {{ $product->name }}</span>
        </div>

        <div class="page-header">
            <div>
                <h1>Kelola Gambar Produk</h1>
                <p>{{ $product->name }}</p>
            </div>
            <a href="{{ route('seller.products.index') }}" class="btn btn-secondary">
                Kembali ke Produk
            </a>
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

        <!-- Product Info Card -->
        <div class="product-info-card">
            <div class="product-info-content">
                @php
                    $thumbnail = $product->productImages->first();
                @endphp
                
                @if($thumbnail)
                    <img 
                        src="{{ asset('storage/' . $thumbnail->image) }}" 
                        alt="{{ $product->name }}"
                        class="product-main-image"
                    >
                @else
                    <div class="no-image-placeholder">
                        <span>Belum ada gambar</span>
                    </div>
                @endif

                <div class="product-info-details">
                    <h3>{{ $product->name }}</h3>
                    <div class="product-meta">
                        <span class="meta-item">
                            <strong>Kategori:</strong> {{ $product->productCategory->name ?? 'Tanpa Kategori' }}
                        </span>
                        <span class="meta-item">
                            <strong>Harga:</strong> Rp {{ number_format($product->price, 0, ',', '.') }}
                        </span>
                        <span class="meta-item">
                            <strong>Stok:</strong> {{ $product->stock }}
                        </span>
                        <span class="meta-item">
                            <strong>Total Gambar:</strong> {{ $images->count() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Image Form -->
        <div class="add-image-section">
            <div class="section-card">
                <div class="section-card-header">
                    <h2>Upload Gambar Baru</h2>
                    <p>Tambahkan gambar produk untuk menarik pembeli</p>
                </div>
                
                <form method="POST" action="{{ route('seller.products.images.store', $product->id) }}" enctype="multipart/form-data" class="image-upload-form">
                    @csrf

                    <div class="form-row">
                        <div class="form-group">
                            <label for="image">Pilih Gambar <span class="required">*</span></label>
                            <div class="file-input-wrapper">
                                <input 
                                    type="file" 
                                    id="image" 
                                    name="image" 
                                    accept="image/*"
                                    required
                                    onchange="previewImage(event)"
                                >
                                <div id="imagePreview" class="image-preview"></div>
                            </div>
                            <small>Format: JPG, PNG. Maksimal 2MB</small>
                            @error('image')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
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
                <h2>Galeri Gambar Produk</h2>
                <span class="image-count">{{ $images->count() }} Gambar</span>
            </div>

            @if($images->count() > 0)
                <div class="images-grid">
                    @foreach($images as $image)
                        <div class="image-card">
                            <div class="image-wrapper">
                                <img 
                                    src="{{ asset('storage/' . $image->image) }}" 
                                    alt="Product Image"
                                    onclick="openImageModal('{{ asset('storage/' . $image->image) }}')"
                                >

                                <div class="image-overlay">
                                </div>
                            </div>

                            <div class="image-actions">
                                <div class="image-info">
                                    <span class="image-size">
                                        {{ number_format(Storage::disk('public')->size($image->image) / 1024, 0) }} KB
                                    </span>
                                    <span class="image-date">
                                        {{ $image->created_at->format('d M Y') }}
                                    </span>
                                </div>
                                <form 
                                    method="POST" 
                                    action="{{ route('seller.products.images.destroy', [$product->id, $image->id]) }}"
                                    onsubmit="return confirm('Yakin ingin menghapus gambar ini?')"
                                    class="delete-form"
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

                @if($images->count() < 5)
                    <div class="info-box">
                        <p><strong>Tips:</strong> Tambahkan minimal 3-5 gambar produk dengan sudut berbeda untuk meningkatkan kepercayaan pembeli.</p>
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-icon"></div>
                    <h3>Belum ada gambar untuk produk ini</h3>
                    <p>Upload gambar produk untuk menarik pembeli</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="modal">
        <span class="modal-close" onclick="closeImageModal()">&times;</span>
        <img class="modal-content" id="modalImage">
    </div>

    @push('scripts')
        <script>
            // Preview image before upload
            function previewImage(event) {
                const preview = document.getElementById('imagePreview');
                const file = event.target.files[0];
                
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                        preview.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                } else {
                    preview.innerHTML = '';
                    preview.style.display = 'none';
                }
            }

            // Open image in modal
            function openImageModal(imageSrc) {
                const modal = document.getElementById('imageModal');
                const modalImg = document.getElementById('modalImage');
                modal.style.display = 'flex';
                modalImg.src = imageSrc;
            }

            // Close modal
            function closeImageModal() {
                const modal = document.getElementById('imageModal');
                modal.style.display = 'none';
            }

            // Close modal when clicking outside
            window.onclick = function(event) {
                const modal = document.getElementById('imageModal');
                if (event.target === modal) {
                    closeImageModal();
                }
            }

            // Close modal with ESC key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeImageModal();
                }
            });
        </script>
    @endpush
</x-app-layout>