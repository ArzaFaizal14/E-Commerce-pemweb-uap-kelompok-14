<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/store_profile.css') }}">
    @endpush

    <div class="container">
        <div class="page-header">
            <h1>Profil Toko</h1>
            <p>Kelola informasi toko Anda</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="profile-container">
            <!-- Current Store Info -->
            <div class="profile-card">
                <h2>Informasi Toko Saat Ini</h2>
                
                <div class="current-logo">
                    @if($store->logo)
                        <img 
                            src="{{ asset('storage/' . $store->logo) }}" 
                            alt="{{ $store->name }}"
                        >
                    @else
                        <div class="no-logo">Tidak ada logo</div>
                    @endif
                </div>

                <div class="info-display">
                    <div class="info-row">
                        <span class="label">Nama Toko:</span>
                        <span class="value">{{ $store->name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Status:</span>
                        <span class="value">
                            @if($store->is_verified)
                                <span class="badge badge-verified">Terverifikasi</span>
                            @else
                                <span class="badge badge-pending">Menunggu Verifikasi</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="label">Kota:</span>
                        <span class="value">{{ $store->city }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Telepon:</span>
                        <span class="value">{{ $store->phone }}</span>
                    </div>
                </div>
            </div>

            <!-- Edit Form -->
            <div class="profile-card">
                <h2>Edit Profil Toko</h2>

                <form method="POST" action="{{ route('store.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Store Name -->
                    <div class="form-group">
                        <label for="name">Nama Toko <span class="required">*</span></label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="{{ old('name', $store->name) }}"
                            required
                        >
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Store Logo -->
                    <div class="form-group">
                        <label for="logo">Logo Toko Baru</label>
                        <input 
                            type="file" 
                            id="logo" 
                            name="logo" 
                            accept="image/*"
                        >
                        <small>Format: JPG, PNG, max 2MB. Kosongkan jika tidak ingin mengubah logo.</small>
                        @error('logo')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- About -->
                    <div class="form-group">
                        <label for="about">Tentang Toko <span class="required">*</span></label>
                        <textarea 
                            id="about" 
                            name="about" 
                            rows="4" 
                            required
                        >{{ old('about', $store->about) }}</textarea>
                        @error('about')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label for="phone">Nomor Telepon <span class="required">*</span></label>
                        <input 
                            type="text" 
                            id="phone" 
                            name="phone" 
                            value="{{ old('phone', $store->phone) }}"
                            required
                        >
                        @error('phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- City -->
                    <div class="form-group">
                        <label for="city">Kota <span class="required">*</span></label>
                        <input 
                            type="text" 
                            id="city" 
                            name="city" 
                            value="{{ old('city', $store->city) }}"
                            required
                        >
                        @error('city')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="form-group">
                        <label for="address">Alamat Lengkap <span class="required">*</span></label>
                        <textarea 
                            id="address" 
                            name="address" 
                            rows="3" 
                            required
                        >{{ old('address', $store->address) }}</textarea>
                        @error('address')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Postal Code -->
                    <div class="form-group">
                        <label for="postal_code">Kode Pos <span class="required">*</span></label>
                        <input 
                            type="text" 
                            id="postal_code" 
                            name="postal_code" 
                            value="{{ old('postal_code', $store->postal_code) }}"
                            required
                        >
                        @error('postal_code')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <a href="{{ route('seller.products.index') }}" class="btn btn-secondary">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Delete Store -->
            <div class="profile-card danger-zone">
                <h2>Menghapus toko akan menghapus semua produk dan data terkait. Tindakan ini tidak dapat dibatalkan.</h2>
                
                <form 
                    method="POST" 
                    action="{{ route('store.profile.destroy') }}"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus toko? Tindakan ini tidak dapat dibatalkan!')"
                >
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        Hapus Toko
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>