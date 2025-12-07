<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/store_balance.css') }}">
    @endpush

    <div class="container">
        <!-- Navigation Tabs -->
        <div class="seller-tabs">
            <a href="{{ route('seller.products.index') }}" class="tab-item">
                Produk Saya
            </a>
            <a href="{{ route('seller.categories.index') }}" class="tab-item">
                Kategori Produk
            </a>
            <a href="{{ route('seller.orders.index') }}" class="tab-item">
                Pesanan
            </a>
            <a href="{{ route('store.balance.index') }}" class="tab-item active">
                Saldo Toko
            </a>
            <a href="{{ route('seller.withdrawals.index') }}" class="tab-item">
                Penarikan Dana
            </a>
        </div>

        <!-- Balance Overview -->
        <div class="balance-overview">
            <div class="balance-card">\
<div class="balance-icon">💰</div>
            <div class="balance-content">
                <div class="balance-label">Saldo Tersedia</div>
                <div class="balance-amount">
                    Rp {{ number_format($balance->balance, 0, ',', '.') }}
                </div>
            </div>
            <div class="balance-actions">
                <a href="{{ route('seller.withdrawals.index') }}" class="btn btn-primary">
                    Tarik Dana
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <h2>Riwayat Saldo</h2>
        <form method="GET" action="{{ route('store.balance.index') }}" class="filter-form">
            <select name="type" onchange="this.form.submit()">
                <option value="">Semua Transaksi</option>
                <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>
                    Pemasukan
                </option>
                <option value="withdraw" {{ request('type') === 'withdraw' ? 'selected' : '' }}>
                    Penarikan
                </option>
            </select>
        </form>
    </div>

    <!-- Balance History -->
    <div class="history-container">
        @if($histories->count() > 0)
            <div class="history-list">
                @foreach($histories as $history)
                    <div class="history-item history-{{ $history->type }}">
                        <div class="history-icon">
                            @if($history->type === 'income')
                                ↓
                            @else
                                ↑
                            @endif
                        </div>
                        <div class="history-content">
                            <div class="history-title">
                                @if($history->type === 'income')
                                    Pemasukan
                                @else
                                    Penarikan Dana
                                @endif
                            </div>
                            <div class="history-remarks">
                                {{ $history->remarks }}
                            </div>
                            <div class="history-date">
                                {{ $history->created_at->format('d M Y H:i') }}
                            </div>
                        </div>
                        <div class="history-amount history-amount-{{ $history->type }}">
                            @if($history->type === 'income')
                                +
                            @else
                                -
                            @endif
                            Rp {{ number_format($history->amount, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $histories->links() }}
            </div>
        @else
            <div class="empty-state">
                <h3>Belum ada riwayat transaksi</h3>
                <p>Riwayat pemasukan dan penarikan akan muncul di sini</p>
            </div>
        @endif
    </div>
</div>
</x-app-layout>