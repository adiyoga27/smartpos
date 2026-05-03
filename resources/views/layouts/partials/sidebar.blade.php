@php
    $menu = [
        ['label' => 'Dashboard', 'icon' => 'fa-home', 'route' => 'dashboard', 'active' => 'dashboard*'],
        ['label' => 'Master Data', 'icon' => 'fa-database', 'route' => null, 'active' => 'categories*|brands*|units*|tax-rates*', 'children' => [
            ['label' => 'Kategori', 'route' => 'categories.index'],
            ['label' => 'Merek', 'route' => 'brands.index'],
            ['label' => 'Satuan', 'route' => 'units.index'],
            ['label' => 'Pajak', 'route' => 'tax-rates.index'],
        ]],
        ['label' => 'Produk', 'icon' => 'fa-box', 'route' => 'products.index', 'active' => 'products*'],
        ['label' => 'Kontak', 'icon' => 'fa-users', 'route' => null, 'active' => 'contacts*|customer-groups*', 'children' => [
            ['label' => 'Pelanggan & Supplier', 'route' => 'contacts.index'],
            ['label' => 'Grup Pelanggan', 'route' => 'customer-groups.index'],
        ]],
        ['label' => 'POS / Penjualan', 'icon' => 'fa-cash-register', 'route' => null, 'active' => 'pos*|sales*', 'children' => [
            ['label' => 'Kasir POS', 'route' => 'pos.index'],
            ['label' => 'Daftar Penjualan', 'route' => 'sales.index'],
            ['label' => 'Draft / Tunda', 'route' => 'sales.drafts'],
        ]],
        ['label' => 'Pembelian', 'icon' => 'fa-truck', 'route' => null, 'active' => 'purchases*', 'children' => [
            ['label' => 'Pembelian Baru', 'route' => 'purchases.create'],
            ['label' => 'Daftar Pembelian', 'route' => 'purchases.index'],
        ]],
        ['label' => 'Stok', 'icon' => 'fa-boxes-stacked', 'route' => null, 'active' => 'stock*', 'children' => [
            ['label' => 'Stok Adjustment', 'route' => 'stock.adjustments'],
            ['label' => 'Transfer Stok', 'route' => 'stock.transfers'],
        ]],
        ['label' => 'Biaya', 'icon' => 'fa-money-bill', 'route' => null, 'active' => 'expenses*|expense-categories*', 'children' => [
            ['label' => 'Biaya Baru', 'route' => 'expenses.index'],
            ['label' => 'Daftar Biaya', 'route' => 'expenses.index'],
            ['label' => 'Kategori Biaya', 'route' => 'expense-categories.index'],
        ]],
        ['label' => 'Laporan', 'icon' => 'fa-chart-bar', 'route' => null, 'active' => 'reports*', 'children' => [
            ['label' => 'Laporan Penjualan', 'route' => 'reports.sales'],
            ['label' => 'Laporan Pembelian', 'route' => 'reports.purchases'],
            ['label' => 'Laporan Stok', 'route' => 'reports.stock'],
            ['label' => 'Laba Rugi', 'route' => 'reports.profit-loss'],
            ['label' => 'Laporan Pajak', 'route' => 'reports.tax'],
        ]],
        ['label' => 'Kas Register', 'icon' => 'fa-money-bill-wave', 'route' => 'cash-register.index', 'active' => 'cash-register*'],
        ['label' => 'Akuntansi', 'icon' => 'fa-calculator', 'route' => null, 'active' => 'accounts*|account-types*', 'children' => [
            ['label' => 'Akun', 'route' => 'accounts.index'],
            ['label' => 'Tipe Akun', 'route' => 'account-types.index'],
            ['label' => 'Transaksi Akun', 'route' => 'accounts.transactions'],
        ]],
        ['label' => 'Pengguna', 'icon' => 'fa-user-gear', 'route' => null, 'active' => 'users*|roles*', 'children' => [
            ['label' => 'Pengguna', 'route' => 'users.index'],
            ['label' => 'Role & Izin', 'route' => 'roles.index'],
        ]],
        ['label' => 'Pengaturan', 'icon' => 'fa-gear', 'route' => null, 'active' => 'settings*', 'children' => [
            ['label' => 'Bisnis', 'route' => 'settings.business'],
            ['label' => 'Lokasi', 'route' => 'settings.locations'],
            ['label' => 'Invoice Layout', 'route' => 'settings.invoice-layouts'],
            ['label' => 'Sistem', 'route' => 'settings.system'],
        ]],
    ];

    function isMenuActive($activePattern) {
        if (!$activePattern) return false;
        $current = Route::currentRouteName();
        foreach (explode('|', $activePattern) as $pattern) {
            if (fnmatch($pattern, $current)) return true;
        }
        return false;
    }
@endphp

@foreach($menu as $item)
    @if(!empty($item['children']))
        @php $hasActive = isMenuActive($item['active']); @endphp
        <div x-data="{ open: {{ $hasActive ? 'true' : 'false' }} }" class="mb-0.5">
            <button x-on:click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 text-sm rounded-lg transition-colors {{ $hasActive ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                <span><i class="fa-solid {{ $item['icon'] }} w-5 mr-3 text-center"></i>{{ $item['label'] }}</span>
                <i class="fa-solid fa-chevron-down text-xs transition-transform" x-bind:class="open ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="open" x-transition class="ml-4 mt-0.5 space-y-0.5 border-l border-gray-200 dark:border-gray-700 pl-2">
                @foreach($item['children'] as $child)
                    <a href="{{ route($child['route']) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs($child['route'].'*') ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        {{ $child['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    @else
        @php $isActive = isMenuActive($item['active']); @endphp
        <a href="{{ route($item['route']) }}" class="flex items-center px-3 py-2.5 text-sm rounded-lg transition-colors {{ $isActive ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <i class="fa-solid {{ $item['icon'] }} w-5 mr-3 text-center"></i>{{ $item['label'] }}
        </a>
    @endif
@endforeach
