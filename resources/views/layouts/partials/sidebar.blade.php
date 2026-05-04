@php
    $menuItems = [
        ['label' => 'Dashboard', 'icon' => 'fa-home', 'route' => 'dashboard', 'active' => 'dashboard*', 'permission' => 'dashboard.view'],
        ['label' => 'Master Data', 'icon' => 'fa-database', 'route' => null, 'active' => 'categories*|brands*|units*|tax-rates*', 'children' => [
            ['label' => 'Kategori', 'route' => 'categories.index', 'permission' => 'categories.view'],
            ['label' => 'Merek', 'route' => 'brands.index', 'permission' => 'brands.view'],
            ['label' => 'Satuan', 'route' => 'units.index', 'permission' => 'units.view'],
            ['label' => 'Pajak', 'route' => 'tax-rates.index', 'permission' => 'tax-rates.view'],
        ]],
        ['label' => 'Produk', 'icon' => 'fa-box', 'route' => 'products.index', 'active' => 'products*', 'permission' => 'products.view'],
        ['label' => 'Kontak', 'icon' => 'fa-users', 'route' => null, 'active' => 'contacts*|customer-groups*', 'children' => [
            ['label' => 'Pelanggan & Supplier', 'route' => 'contacts.index', 'permission' => 'contacts.view'],
            ['label' => 'Grup Pelanggan', 'route' => 'customer-groups.index', 'permission' => 'customer-groups.view'],
        ]],
        ['label' => 'POS / Penjualan', 'icon' => 'fa-cash-register', 'route' => null, 'active' => 'pos*|sales*', 'children' => [
            ['label' => 'Kasir POS', 'route' => 'pos.index', 'permission' => 'pos.view'],
            ['label' => 'Daftar Penjualan', 'route' => 'sales.index', 'permission' => 'sales.view'],
            ['label' => 'Draft / Tunda', 'route' => 'sales.drafts', 'permission' => 'sales.view'],
        ]],
        ['label' => 'Pembelian', 'icon' => 'fa-truck', 'route' => null, 'active' => 'purchases*', 'children' => [
            ['label' => 'Pembelian Baru', 'route' => 'purchases.create', 'permission' => 'purchases.create'],
            ['label' => 'Daftar Pembelian', 'route' => 'purchases.index', 'permission' => 'purchases.view'],
        ]],
        ['label' => 'Stok', 'icon' => 'fa-boxes-stacked', 'route' => null, 'active' => 'stock*', 'children' => [
            ['label' => 'Stok Adjustment', 'route' => 'stock.adjustments.index', 'permission' => 'stock.view'],
            ['label' => 'Transfer Stok', 'route' => 'stock.transfers.index', 'permission' => 'stock.view'],
            ['label' => 'Riwayat Stok', 'route' => 'stock.history', 'permission' => 'stock.view'],
        ]],
        ['label' => 'Biaya', 'icon' => 'fa-money-bill', 'route' => null, 'active' => 'expenses*|expense-categories*', 'children' => [
            ['label' => 'Daftar Biaya', 'route' => 'expenses.index', 'permission' => 'expenses.view'],
            ['label' => 'Kategori Biaya', 'route' => 'expense-categories.index', 'permission' => 'expense-categories.view'],
        ]],
        ['label' => 'Laporan', 'icon' => 'fa-chart-bar', 'route' => null, 'active' => 'reports*', 'children' => [
            ['label' => 'Laporan Penjualan', 'route' => 'reports.sales', 'permission' => 'reports.view'],
            ['label' => 'Laporan Pembelian', 'route' => 'reports.purchases', 'permission' => 'reports.view'],
            ['label' => 'Laporan Stok', 'route' => 'reports.stock', 'permission' => 'reports.view'],
            ['label' => 'Laba Rugi', 'route' => 'reports.profit-loss', 'permission' => 'reports.view'],
            ['label' => 'Laporan Pajak', 'route' => 'reports.tax', 'permission' => 'reports.view'],
        ]],
        ['label' => 'Kas Register', 'icon' => 'fa-money-bill-wave', 'route' => 'cash-register.index', 'active' => 'cash-register*', 'permission' => 'cash-register.view'],
        ['label' => 'Akuntansi', 'icon' => 'fa-calculator', 'route' => null, 'active' => 'accounts*|account-types*', 'children' => [
            ['label' => 'Akun', 'route' => 'accounts.index', 'permission' => 'accounts.view'],
            ['label' => 'Tipe Akun', 'route' => 'account-types.index', 'permission' => 'account-types.view'],
            ['label' => 'Transaksi Akun', 'route' => 'accounts.transactions.index', 'permission' => 'accounts.view'],
        ]],
        ['label' => 'Pengguna', 'icon' => 'fa-user-gear', 'route' => null, 'active' => 'users*|roles*', 'children' => [
            ['label' => 'Pengguna', 'route' => 'users.index', 'permission' => 'users.view'],
            ['label' => 'Role & Izin', 'route' => 'roles.index', 'permission' => 'roles.view'],
        ]],
        ['label' => 'Pengaturan', 'icon' => 'fa-gear', 'route' => null, 'active' => 'settings*', 'children' => [
            ['label' => 'Bisnis', 'route' => 'settings.business', 'permission' => 'settings.view'],
            ['label' => 'Lokasi', 'route' => 'settings.locations', 'permission' => 'settings.view'],
            ['label' => 'Invoice Layout', 'route' => 'settings.invoice-layouts', 'permission' => 'settings.view'],
            ['label' => 'Sistem', 'route' => 'settings.system', 'permission' => 'settings.view'],
        ]],
    ];

    $menu = [];
    foreach ($menuItems as $item) {
        if (!empty($item['children'])) {
            $visibleChildren = array_filter($item['children'], function ($child) {
                return auth()->user()->can($child['permission']);
            });
            if (!empty($visibleChildren)) {
                $item['children'] = array_values($visibleChildren);
                $menu[] = $item;
            }
        } elseif (auth()->user()->can($item['permission'])) {
            $menu[] = $item;
        }
    }

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
