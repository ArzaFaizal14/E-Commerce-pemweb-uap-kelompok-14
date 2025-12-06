<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/seller_product_form.css') }}">
    @endpush

    <div class="container">
        <div class="page-header">
            <h1>Tambah Produk Baru</h1>
            <p>Lengkapi informasi produk yang akan dijual</p>
        </div>

        <div class="form-container">
            <div class="form-card">
                <form method="POST" action="{{ route('seller.products.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Product Name -->
                    <div class="form-group">
                        <label for="name">Nama Produk <span class="required">*</span></label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="{{ old('name') }}"
                            required
                            placeholder="Contoh: Sepatu Nike Air Max"
                        >
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div class="form-group">
                        <label for="product_category_id">Kategori <span class="required">*</span></label>
                        <select id="product_category_id" name="product_category_id" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option 
                                    value="{{ $category->id }}"
                                    {{ old('product_category_id') == $category->id ? 'selected' : '' }}
                                >
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_category_id')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="5"
                            placeholder="Jelaskan detail produk Anda"
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Condition -->
                    <div class="form-group">
                        <label for="condition">Kondisi <span class="required">*</span></label>
                        <select id="condition" name="condition" required>
                            <option value="">Pilih Kondisi</option>
                            <option value="new" {{ old('condition') == 'new' ? 'selected' : '' }}>
                                Baru
                            </option>
                            <option value="second" {{ old('condition') == 'second' ? 'selected' : '' }}>
                                Bekas
                            </option>
                        </select>
                        @error('condition')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div class="form-group">
                        <label for="price">Harga <span class="required">*</span></label>
                        <input 
                            type="number" 
                            id="price" 
                            name="price" 
                            value="{{ old('price') }}"
                            min="0"
                            step="1000"
                            required
                            placeholder="Contoh: 500000"
                        >
                        @error('price')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Stock -->
                    <div class="form-group">
                        <label for="stock">Stok <span class="required">*</span></label>
                        <input 
                            type="number" 
                            id="stock" 
                            name="stock" 
                            value="{{ old('stock') }}"
                            min="0"
                            required
                            placeholder="Contoh: 10"
                        >
                        @error('stock')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Weight -->
                    <div class="form-group">
                        <label for="weight">Berat (gram) <span class="required">*</span></label>
                        <input 
                            type="number" 
                            id="weight" 
                            name="weight" 
                            value="{{ old('weight') }}"
                            min="0"
                            required
                            placeholder="Contoh: 500"
                        >
                        @error('weight')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Product Image -->
                    <div class="form-group">
                        <label for="image">Gambar Produk</label>
                        <input 
                            type="file" 
                            id="image" 
                            name="image" 
                            accept="image/*"
                        >
                        <small>Format: JPG, PNG, max 2MB. Gambar tambahan bisa ditambahkan setelah produk dibuat.</small>
                        @error('image')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <a href="{{ route('seller.products.index') }}" class="btn btn-secondary">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Simpan Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>