<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/store_registration.css') }}">
    @endpush

    <div class="container">
        <div class="page-header">
            <h1>Daftar Sebagai Penjual</h1>
            <p>Mulai berjualan dan kembangkan bisnis Anda</p>
        </div>

        @if(session('info'))
            <div class="alert alert-info">
                {{ session('info') }}
            </div>
        @endif

        <div class="registration-container">
            <div class="registration-card">
                <form method="POST" action="{{ route('store.registration.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Store Name -->
                    <div class="form-group">
                        <label for="name">Nama Toko <span class="required">*</span></label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="{{ old('name') }}"
                            required
                            placeholder="Masukkan nama toko Anda"
                        >
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Store Logo -->
                    <div class="form-group">
                        <label for="logo">Logo Toko <span class="required">*</span></label>
                        <input 
                            type="file" 
                            id="logo" 
                            name="logo" 
                            accept="image/*"
                            required
                        >
                        <small>Format: JPG, PNG, max 2MB</small>
                        @error('logo')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Store About -->
                    <div class="form-group">
                        <label for="about">Tentang Toko <span class="required">*</span></label>
                        <textarea 
                            id="about" 
                            name="about" 
                            rows="4" 
                            required
                            placeholder="Ceritakan tentang toko Anda"
                        >{{ old('about') }}</textarea>
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
                            value="{{ old('phone') }}"
                            required
                            placeholder="Contoh: 08123456789"
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
                            value="{{ old('city') }}"
                            required
                            placeholder="Contoh: Jakarta"
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
                            placeholder="Masukkan alamat lengkap toko"
                        >{{ old('address') }}</textarea>
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
                            value="{{ old('postal_code') }}"
                            required
                            placeholder="Contoh: 12345"
                        >
                        @error('postal_code')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Info Box -->
                    <div class="info-box">
                        <strong>Catatan:</strong>
                        <p>Setelah mendaftar, toko Anda akan direview oleh admin. Anda akan dapat mulai berjualan setelah toko disetujui.</p>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Daftar Toko
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>