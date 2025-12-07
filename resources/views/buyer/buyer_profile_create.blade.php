<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/buyer_profile.css') }}">
    @endpush

    <div class="buyer-profile-page">
        <div class="buyer-profile-container">
            <div class="buyer-profile-card">
                <div class="buyer-profile-intro">
                    <h3>Sebelum Melanjutkan</h3>
                    <p>Silakan lengkapi informasi profil pembeli Anda untuk melanjutkan berbelanja.</p>
                </div>

                <form method="POST" action="{{ route('buyer.profile.store') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- Phone Number --}}
                    <div class="form-group">
                        <label for="phone_number">Nomor Telepon</label>
                        <input
                            id="phone_number"
                            name="phone_number"
                            type="text"
                            value="{{ old('phone_number') }}"
                            placeholder="Contoh: 08123456789"
                            required
                        >
                        @error('phone_number')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Profile Picture --}}
                    <div class="form-group">
                        <label for="profile_picture">Foto Profil (Opsional)</label>
                        <input
                            id="profile_picture"
                            name="profile_picture"
                            type="file"
                            accept="image/*"
                        >
                        <p class="helper-text">Format: JPG, PNG. Maksimal 2MB</p>
                        @error('profile_picture')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit">Simpan Profil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>