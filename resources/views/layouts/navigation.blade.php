<style>
/* ======================
    TOP NAV - GLOBAL STYLE
====================== */
.topnav {
    background: #ffffff;
    border-bottom: 1px solid #e6e6e6;
    padding: 0;
    position: sticky;
    top: 0;
    z-index: 50;
    width: 100%;
}

.topnav-inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 14px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* ======================
    LEFT SECTION
====================== */
.topnav-left {
    display: flex;
    align-items: center;
    gap: 24px;
}

/* LOGO */
.topnav-logo {
    font-size: 20px;
    font-weight: 600;
    color: #222;
    text-decoration: none;
}

/* DESKTOP LINKS */
.topnav-links-desktop {
    display: flex;
    gap: 16px;
}

.topnav-link {
    text-decoration: none;
    color: #444;
    font-size: 15px;
    padding: 6px 10px;
    border-radius: 6px;
    transition: 0.2s ease;
}

.topnav-link:hover {
    background: #f1f1f1;
}

.topnav-link.is-active {
    font-weight: 600;
    background: #e9e9e9;
}

/* ======================
    RIGHT SECTION (USER)
====================== */
.topnav-right-desktop {
    display: flex;
    align-items: center;
}

/* USER BUTTON */
.topnav-user-trigger {
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 15px;
    display: flex;
    align-items: center;
    gap: 6px;
    color: #333;
    transition: 0.2s;
}

.topnav-user-trigger:hover {
    background: #f1f1f1;
}

/* USER DROPDOWN */
.topnav-user {
    position: relative;
}

.topnav-user-menu {
    position: absolute;
    top: 46px;
    right: 0;
    background: #fff;
    border: 1px solid #e6e6e6;
    border-radius: 10px;
    width: 160px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    display: none;
    flex-direction: column;
    overflow: hidden;
    animation: fadeDown 0.2s ease;
}

.topnav-user-menu.is-open {
    display: flex;
}

.topnav-user-item {
    padding: 12px 14px;
    text-decoration: none;
    color: #333;
    font-size: 15px;
    transition: 0.15s;
    text-align: left;
    display: block;
    width: 100%;
}

.topnav-user-item:hover {
    background: #f5f5f5;
}

.topnav-logout {
    color: #b10000 !important;
}

/* Fade Animation */
@keyframes fadeDown {
    from { opacity: 0; transform: translateY(-6px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ======================
    HAMBURGER (MOBILE)
====================== */
.topnav-menu-toggle {
    background: none;
    border: none;
    cursor: pointer;
    display: none; /* hidden on desktop */
    padding: 8px;
    flex-direction: column;
    align-items: center;
}

.topnav-menu-icon {
    width: 24px;
    height: 2.6px;
    background: #333;
    margin: 4px 0;
    border-radius: 3px;
    transition: 0.3s;
}

/* animasi saat hamburger open */
.topnav-menu-toggle.is-open .topnav-menu-icon:nth-child(1) {
    transform: translateY(6px) rotate(45deg);
}
.topnav-menu-toggle.is-open .topnav-menu-icon:nth-child(2) {
    opacity: 0;
}
.topnav-menu-toggle.is-open .topnav-menu-icon:nth-child(3) {
    transform: translateY(-6px) rotate(-45deg);
}

/* ======================
    MOBILE MENU
====================== */
.topnav-mobile {
    display: none;
    flex-direction: column;
    background: #fff;
    border-top: 1px solid #e6e6e6;
    padding: 10px 20px;
}

.topnav-mobile.is-open {
    display: flex;
}

.topnav-mobile-section {
    border-bottom: 1px solid #ececec;
    padding: 10px 0;
}

.topnav-mobile-link {
    text-decoration: none;
    color: #333;
    padding: 10px 0;
    display: block;
    font-size: 16px;
}

.topnav-mobile-link:hover {
    background: #f5f5f5;
}

.topnav-mobile-link.is-active {
    font-weight: 600;
}

/* USER (MOBILE) */
.topnav-mobile-user-info {
    margin-bottom: 10px;
}

.topnav-mobile-user-name {
    font-weight: 600;
    color: #222;
}

.topnav-mobile-user-email {
    font-size: 13px;
    color: #666;
}

/* Logout warna merah */
.topnav-logout {
    color: #b10000 !important;
}

/* ======================
    RESPONSIVE BREAKPOINT
====================== */
@media (max-width: 768px) {

    .topnav-links-desktop,
    .topnav-right-desktop {
        display: none;
    }

    .topnav-menu-toggle {
        display: flex;
    }
}
</style>

@php
    $isAdmin = optional(Auth::user())->role === 'admin';
@endphp

<nav class="topnav">
    <div class="topnav-inner">
        <div class="topnav-left">
            {{-- Logo --}}
            <a href="{{ route('dashboard') }}" class="topnav-logo">
                <span>{{ config('app.name', 'Laravel') }}</span>
            </a>

            {{-- Link utama (desktop) --}}
            <div class="topnav-links-desktop">
                @if($isAdmin)
                    {{-- Admin hanya lihat Dashboard + Admin --}}
                    <a
                        href="{{ route('dashboard') }}"
                        class="topnav-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}"
                    >
                        {{ __('Beranda') }}
                    </a>
                    <a
                        href="{{ route('admin.stores.verifications.index') }}"
                        class="topnav-link {{ request()->routeIs('admin.*') ? 'is-active' : '' }}"
                    >
                        {{ __('Admin') }}
                    </a>
                @else
                    {{-- User biasa: Dashboard, Riwayat, Toko Saya (tanpa Admin) --}}
                    <a
                        href="{{ route('dashboard') }}"
                        class="topnav-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}"
                    >
                        {{ __('Beranda') }}
                    </a>
                    <a
                        href="{{ route('transactions.index') }}"
                        class="topnav-link {{ request()->routeIs('transactions.index') ? 'is-active' : '' }}"
                    >
                        {{ __('Riwayat Belanja') }}
                    </a>
                    <a
                        href="{{ route('seller.products.index') }}"
                        class="topnav-link 
                            {{ request()->routeIs([
                                'seller.*',
                                'store.profile.*',
                                'store.balance.*'
                            ]) ? 'is-active' : '' }}"
                    >
                        {{ __('Toko Saya') }}
                    </a>
                @endif
            </div>
        </div>

        {{-- User dropdown (desktop) --}}
        <div class="topnav-right-desktop">
            <div class="topnav-user">
                <button type="button" class="topnav-user-trigger" id="userMenuButton">
                    <span class="topnav-user-name">{{ Auth::user()->name }}</span>
                    <span class="topnav-user-icon">▾</span>
                </button>

                <div class="topnav-user-menu" id="userMenu">
                    <a href="{{ route('profile.edit') }}" class="topnav-user-item">
                        {{ __('Profile') }}
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="topnav-user-item topnav-logout">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Tombol hamburger (mobile) --}}
        <button
            type="button"
            class="topnav-menu-toggle"
            id="mobileMenuButton"
            aria-label="Toggle navigation"
        >
            <span class="topnav-menu-icon"></span>
            <span class="topnav-menu-icon"></span>
            <span class="topnav-menu-icon"></span>
        </button>
    </div>

    {{-- Menu mobile --}}
    <div class="topnav-mobile" id="mobileMenu">
        <div class="topnav-mobile-section">
            @if($isAdmin)
                {{-- Admin: Dashboard + Admin --}}
                <a
                    href="{{ route('dashboard') }}"
                    class="topnav-mobile-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}"
                >
                    {{ __('Dashboard') }}
                </a>
                <a
                    href="{{ route('admin.stores.verifications.index') }}"
                    class="topnav-link {{ request()->routeIs('admin.*') ? 'is-active' : '' }}"
                >
                    {{ __('Admin') }}
                </a>
            @else
                {{-- User biasa: Dashboard, Riwayat, Toko Saya --}}
                <a
                    href="{{ route('dashboard') }}"
                    class="topnav-mobile-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}"
                >
                    {{ __('Dashboard') }}
                </a>
                <a
                    href="{{ route('transactions.index') }}"
                    class="topnav-mobile-link {{ request()->routeIs('transactions.index') ? 'is-active' : '' }}"
                >
                    {{ __('Riwayat Belanja') }}
                </a>
                <a
                    href="{{ route('seller.products.index') }}"
                    class="topnav-mobile-link 
                        {{ request()->routeIs([
                            'seller.*',
                            'store.profile.*',
                            'store.balance.*'
                        ]) ? 'is-active' : '' }}"
                >
                    {{ __('Toko Saya') }}
                </a>
            @endif
        </div>

        <div class="topnav-mobile-section topnav-mobile-user">
            <div class="topnav-mobile-user-info">
                <div class="topnav-mobile-user-name">{{ Auth::user()->name }}</div>
                <div class="topnav-mobile-user-email">{{ Auth::user()->email }}</div>
            </div>

            <a href="{{ route('profile.edit') }}" class="topnav-mobile-link">
                {{ __('Profile') }}
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="topnav-mobile-link topnav-logout">
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const userBtn = document.getElementById('userMenuButton');
    const userMenu = document.getElementById('userMenu');
    const mobileBtn = document.getElementById('mobileMenuButton');
    const mobileMenu = document.getElementById('mobileMenu');

    // Dropdown user (desktop)
    if (userBtn && userMenu) {
        userBtn.addEventListener('click', function () {
            userMenu.classList.toggle('is-open');
        });

        document.addEventListener('click', function (e) {
            if (!userBtn.contains(e.target) && !userMenu.contains(e.target)) {
                userMenu.classList.remove('is-open');
            }
        });
    }

    // Menu mobile
    if (mobileBtn && mobileMenu) {
        mobileBtn.addEventListener('click', function () {
            mobileMenu.classList.toggle('is-open');
            mobileBtn.classList.toggle('is-open');
        });
    }
});
</script>