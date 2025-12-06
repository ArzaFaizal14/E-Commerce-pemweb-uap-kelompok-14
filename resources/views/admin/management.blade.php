<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @endpush

    <div class="container">
        <div class="page-header">
            <h1>Manajemen User & Toko</h1>
            <p>Kelola semua user dan toko dalam sistem</p>
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

        <!-- Admin Navigation -->
        <div class="admin-tabs">
            <a href="{{ route('admin.stores.verifications.index') }}" class="tab-item">
                Verifikasi Toko
            </a>
            <a href="{{ route('admin.users-stores.index') }}" class="tab-item active">
                Manajemen User & Toko
            </a>
        </div>

        <!-- Users Management -->
        <div class="admin-section">
            <div class="section-header">
                <h2>Daftar User ({{ $users->total() }})</h2>
            </div>

            @if($users->count() > 0)
                <div class="management-table">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Punya Toko</th>
                                <th>Tanggal Daftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge badge-{{ $user->role }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user->store)
                                            <span class="badge badge-success">Ya</span>
                                        @else
                                            <span class="badge badge-secondary">Tidak</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('d M Y') }}</td>
                                    <td>
                                        @if($user->role !== 'admin')
                                            <form 
                                                method="POST" 
                                                action="{{ route('admin.users.destroy', $user->id) }}"
                                                onsubmit="return confirm('Yakin ingin menghapus user ini? Toko miliknya (jika ada) juga akan terhapus.')"
                                                style="display: inline;"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pagination-wrapper">
                    {{ $users->links() }}
                </div>
            @else
                <div class="empty-state-small">
                    <p>Tidak ada user</p>
                </div>
            @endif
        </div>

        <!-- Stores Management -->
        <div class="admin-section">
            <div class="section-header">
                <h2>Daftar Toko ({{ $stores->total() }})</h2>
            </div>

            @if($stores->count() > 0)
                <div class="management-table">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Logo</th>
                                <th>Nama Toko</th>
                                <th>Pemilik</th>
                                <th>Kota</th>
                                <th>Status</th>
                                <th>Tanggal Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stores as $store)
                                <tr>
                                    <td>{{ $store->id }}</td>
                                    <td>
                                        @if($store->logo)
                                            <img 
                                                src="{{ asset('storage/' . $store->logo) }}" 
                                                alt="{{ $store->name }}"
                                                class="table-logo-small"
                                            >
                                        @endif
                                    </td>
                                    <td>{{ $store->name }}</td>
                                    <td>{{ $store->user->name }}</td>
                                    <td>{{ $store->city }}</td>
                                    <td>
                                        @if($store->is_verified)
                                            <span class="badge badge-verified">Terverifikasi</span>
                                        @else
                                            <span class="badge badge-pending">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $store->created_at->format('d M Y') }}</td>
                                    <td>
                                        <form 
                                            method="POST" 
                                            action="{{ route('admin.stores.destroy', $store->id) }}"
                                            onsubmit="return confirm('Yakin ingin menghapus toko ini? User pemilik tidak akan terhapus.')"
                                            style="display: inline;"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pagination-wrapper">
                    {{ $stores->links() }}
                </div>
            @else
                <div class="empty-state-small">
                    <p>Tidak ada toko</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>