<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/store_withdrawal.css') }}">
    @endpush

    <div class="container">
        <div class="page-header">
            <h1>Penarikan Dana</h1>
            <p>Ajukan penarikan saldo toko Anda</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Navigation Tabs -->
        <div class="seller-tabs">
            <a href="{{ route('seller.products.index') }}" class="tab-item">
                Produk Saya
            </a>
            <a href="{{ route('seller.orders.index') }}" class="tab-item">
                Pesanan
            </a>
            <a href="{{ route('seller.balance.index') }}" class="tab-item">
                Saldo Toko
            </a>
            <a href="{{ route('seller.withdrawals.index') }}" class="tab-item active">
                Penarikan Dana
            </a>
        </div>

        <div class="withdrawal-container">
            <!-- Balance Summary -->
            <div class="withdrawal-card">
                <h2>Saldo Tersedia</h2>
                <div class="balance-display">
                    <div class="balance-amount-large">
                        Rp {{ number_format($balance->balance, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <!-- Withdrawal Form -->
            <div class="withdrawal-card">
                <h2>Ajukan Penarikan Dana</h2>

                <form method="POST" action="{{ route('seller.withdrawals.store') }}">
                    @csrf

                    <!-- Amount -->
                    <div class="form-group">
                        <label for="amount">Jumlah Penarikan <span class="required">*</span></label>
                        <input 
                            type="number" 
                            id="amount" 
                            name="amount" 
                            value="{{ old('amount') }}"
                            min="1"
                            max="{{ $balance->balance }}"
                            step="1000"
                            required
                            placeholder="Masukkan jumlah yang ingin ditarik"
                        >
                        <small>Maksimal: Rp {{ number_format($balance->balance, 0, ',', '.') }}</small>
                        @error('amount')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Bank Account Name -->
                    <div class="form-group">
                        <label for="bank_account_name">Nama Pemilik Rekening <span class="required">*</span></label>
                        <input 
                            type="text" 
                            id="bank_account_name" 
                            name="bank_account_name" 
                            value="{{ old('bank_account_name', $lastWithdrawal->bank_account_name ?? '') }}"
                            required
                            placeholder="Contoh: John Doe"
                        >
                        @error('bank_account_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Bank Account Number -->
                    <div class="form-group">
                        <label for="bank_account_number">Nomor Rekening <span class="required">*</span></label>
                        <input 
                            type="text" 
                            id="bank_account_number" 
                            name="bank_account_number" 
                            value="{{ old('bank_account_number', $lastWithdrawal->bank_account_number ?? '') }}"
                            required
                            placeholder="Contoh: 1234567890"
                        >
                        @error('bank_account_number')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Bank Name -->
                    <div class="form-group">
                        <label for="bank_name">Nama Bank <span class="required">*</span></label>
                        <input 
                            type="text" 
                            id="bank_name" 
                            name="bank_name" 
                            value="{{ old('bank_name', $lastWithdrawal->bank_name ?? '') }}"
                            required
                            placeholder="Contoh: Bank Mandiri"
                        >
                        @error('bank_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="info-box">
                        <strong>Catatan:</strong>
                        <p>Permintaan penarikan akan diproses dalam 1-3 hari kerja. Saldo akan dipotong langsung setelah pengajuan.</p>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            Ajukan Penarikan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Withdrawal History -->
            <div class="withdrawal-card">
                <h2>Riwayat Penarikan</h2>

                @if($withdrawals->count() > 0)
                    <div class="withdrawal-list">
                        @foreach($withdrawals as $withdrawal)
                            <div class="withdrawal-item">
                                <div class="withdrawal-header">
                                    <div class="withdrawal-info">
                                        <h4>Penarikan #{{ $withdrawal->id }}</h4>
                                        <div class="withdrawal-date">
                                            {{ $withdrawal->created_at->format('d M Y H:i') }}
                                        </div>
                                    </div>
                                    <div class="withdrawal-status">
                                        <span class="status-badge status-{{ $withdrawal->status }}">
                                            @switch($withdrawal->status)
                                                @case('pending')
                                                    Menunggu
                                                    @break
                                                @case('approved')
                                                    Disetujui
                                                    @break
                                                @case('rejected')
                                                    Ditolak
                                                    @break
                                                @case('completed')
                                                    Selesai
                                                    @break
                                                @default
                                                    {{ ucfirst($withdrawal->status) }}
                                            @endswitch
                                        </span>
                                    </div>
                                </div>

                                <div class="withdrawal-details">
                                    <div class="detail-row">
                                        <span class="label">Jumlah:</span>
                                        <span class="value">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="label">Bank:</span>
                                        <span class="value">{{ $withdrawal->bank_name }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="label">No. Rekening:</span>
                                        <span class="value">{{ $withdrawal->bank_account_number }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="label">Nama Pemilik:</span>
                                        <span class="value">{{ $withdrawal->bank_account_name }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-wrapper">
                        {{ $withdrawals->links() }}
                    </div>
                @else
                    <div class="empty-state-small">
                        <p>Belum ada riwayat penarikan</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>