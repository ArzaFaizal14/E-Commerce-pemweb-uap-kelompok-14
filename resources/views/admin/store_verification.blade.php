<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @endpush

    <div class="container">
        <div class="page-header">
            <h1>Verifikasi Toko</h1>
            <p>Kelola persetujuan pendaftaran toko</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info">
                {{ session('info') }}
            </div>
        @endif

        <!-- Admin Navigation -->
        <div class="admin-tabs">
            <a href="{{ route('admin.stores.verifications.index') }}" class="tab-item active">
                Verifikasi Toko
            </a>
            <a href="{{ route('admin.users-stores.index') }}" class="tab-item">
                Manajemen User & Toko
            </a>
        </div>

        <!-- Pending Stores -->
        <div class="admin-section">
            <div class="section-header">
                <h2>Menunggu Verifikasi ({{ $pendingStores->total() }})</h2>
            </div>

            @if($pendingStores->count() > 0)
                <div class="stores-grid">
                    @foreach($pendingStores as $store)
                        <div class="store-verification-card">
                            <div class="store-card-header">
                                @if($store->logo)
                                    <img 
                                        src="{{ asset('storage/' . $store->logo) }}" 
                                        alt="{{ $store->name }}"
                                        class="store-logo"
                                    >
                                @endif
                                <div class="store-info">
                                    <h3>{{ $store->name }}</h3>
                                    <p>{{ $store->city }}</p>
                                </div>
                            </div>

                            <div class="store-details">
                                <div class="detail-item">
                                    <span class="label">Pemilik:</span>
                                    <span class="value">{{ $store->user->name }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="label">Email:</span>
                                    <span class="value">{{ $store->user->email }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="label">Telepon:</span>
                                    <span class="value">{{ $store->phone }}</span>
                                </div>
                                <div class="detail-item full-width">
                                    <span class="label">Alamat:</span>
                                    <span class="value">{{ $store->address }}, {{ $store->city }} {{ $store->postal_code }}</span>
                                </div>
                                <div class="detail-item full-width">
                                    <span class="label">Tentang:</span>
                                    <span class="value">{{ $store->about }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="label">Tanggal Daftar:</span>
                                    <span class="value">{{ $store->created_at->format('d M Y H:i') }}</span>
                                </div>
                            </div>

                            <div class="store-actions">
                                <form 
                                    method="POST" 
                                    action="{{ route('admin.stores.verify', $store->id) }}"
                                    style="display: inline;"
                                >
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-success">
                                        ✓ Verifikasi
                                    </button>
                                </form>

                                <form 
                                    method="POST" 
                                    action="{{ route('admin.stores.reject', $store->id) }}"
                                    onsubmit="return confirm('Yakin ingin menolak toko ini? Toko akan dihapus.')"
                                    style="display: inline;"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        ✗ Tolak
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pagination-wrapper">
                    {{ $pendingStores->links() }}
                </div>
            @else
                <div class="empty-state">
                    <h3>Tidak ada toko menunggu verifikasi</h3>
                    <p>Semua permohonan sudah diproses</p>
                </div>
            @endif
        </div>

        <!-- Verified Stores -->
        <div class="admin-section">
            <div class="section-header">
                <h2>Toko Terverifikasi ({{ $verifiedStores->total() }})</h2>
            </div>

            @if($verifiedStores->count() > 0)
                <div class="verified-stores-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Logo</th>
                                <th>Nama Toko</th>
                                <th>Pemilik</th>
                                <th>Kota</th>
                                <th>Telepon</th>
                                <th>Tanggal Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($verifiedStores as $store)
                                <tr>
                                    <td>
                                        @if($store->logo)
                                            <img 
                                                src="{{ asset('storage/' . $store->logo) }}" 
                                                alt="{{ $store->name }}"
                                                class="table-logo"
                                            >
                                        @endif
                                    </td>
                                    <td>{{ $store->name }}</td>
                                    <td>{{ $store->user->name }}</td>
                                    <td>{{ $store->city }}</td>
                                    <td>{{ $store->phone }}</td>
                                    <td>{{ $store->updated_at->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pagination-wrapper">
                    {{ $verifiedStores->links() }}
                </div>
            @else
                <div class="empty-state-small">
                    <p>Belum ada toko terverifikasi</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>