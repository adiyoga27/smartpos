@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="flex items-center gap-1 text-sm">
            <li><a href="{{ route('dashboard') }}" class="text-primary-600 dark:text-primary-400 font-medium">Dashboard</a></li>
        </ol>
    </nav>
@endsection

@section('content')
{{-- Stat cards row 1 --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Penjualan Hari Ini</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($todaySales ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 bg-success-50 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-cart-shopping text-success-600 dark:text-success-500 text-xl"></i>
            </div>
        </div>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-3">Rp {{ number_format($todaySalesAmount ?? 0, 0, ',', '.') }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pembelian Hari Ini</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($todayPurchases ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 bg-warning-50 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-truck text-warning-600 dark:text-warning-500 text-xl"></i>
            </div>
        </div>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-3">Rp {{ number_format($todayPurchasesAmount ?? 0, 0, ',', '.') }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Biaya Hari Ini</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($todayExpenses ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 bg-danger-50 dark:bg-red-900/20 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-money-bill text-danger-600 dark:text-danger-500 text-xl"></i>
            </div>
        </div>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-3">Rp {{ number_format($todayExpensesAmount ?? 0, 0, ',', '.') }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Penjualan Bulan Ini</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($monthSales ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 bg-primary-50 dark:bg-primary-900/20 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-chart-line text-primary-600 dark:text-primary-400 text-xl"></i>
            </div>
        </div>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-3">Rp {{ number_format($monthSalesAmount ?? 0, 0, ',', '.') }}</p>
    </div>
</div>

{{-- Stat cards row 2 --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Produk</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($totalProducts ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-box text-indigo-600 dark:text-indigo-400 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Pelanggan</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($totalCustomers ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 bg-teal-50 dark:bg-teal-900/20 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-users text-teal-600 dark:text-teal-400 text-xl"></i>
            </div>
        </div>
    </div>
</div>

{{-- Sales chart + tables grid --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Line chart --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Tren Penjualan 7 Hari</h3>
        <div class="relative h-64">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    {{-- Top products --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Top 5 Produk</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left py-2 pr-3 font-medium text-gray-500 dark:text-gray-400">#</th>
                        <th class="text-left py-2 pr-3 font-medium text-gray-500 dark:text-gray-400">Produk</th>
                        <th class="text-right py-2 font-medium text-gray-500 dark:text-gray-400">Terjual</th>
                        <th class="text-right py-2 pl-3 font-medium text-gray-500 dark:text-gray-400">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProducts ?? [] as $index => $product)
                        <tr class="border-b border-gray-100 dark:border-gray-700/50">
                            <td class="py-2.5 pr-3 text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                            <td class="py-2.5 pr-3 text-gray-900 dark:text-white font-medium">{{ $product->name }}</td>
                            <td class="py-2.5 text-right text-gray-600 dark:text-gray-400">{{ $product->quantity ?? 0 }}</td>
                            <td class="py-2.5 pl-3 text-right text-gray-900 dark:text-white">Rp {{ number_format($product->total ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-400 dark:text-gray-500">Belum ada data produk</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent sales --}}
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">5 Penjualan Terbaru</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left py-2 pr-3 font-medium text-gray-500 dark:text-gray-400">Invoice</th>
                        <th class="text-left py-2 pr-3 font-medium text-gray-500 dark:text-gray-400">Pelanggan</th>
                        <th class="text-left py-2 pr-3 font-medium text-gray-500 dark:text-gray-400 hidden sm:table-cell">Tanggal</th>
                        <th class="text-left py-2 pr-3 font-medium text-gray-500 dark:text-gray-400 hidden md:table-cell">Status</th>
                        <th class="text-right py-2 pl-3 font-medium text-gray-500 dark:text-gray-400">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSales ?? [] as $sale)
                        <tr class="border-b border-gray-100 dark:border-gray-700/50">
                            <td class="py-2.5 pr-3">
                                <a href="{{ route('sales.show', $sale->id) }}" class="text-primary-600 dark:text-primary-400 hover:underline font-medium">{{ $sale->invoice_no ?? '#' . $sale->id }}</a>
                            </td>
                            <td class="py-2.5 pr-3 text-gray-900 dark:text-white">{{ $sale->customer_name ?? 'Umum' }}</td>
                            <td class="py-2.5 pr-3 text-gray-500 dark:text-gray-400 hidden sm:table-cell">{{ isset($sale->created_at) ? $sale->created_at->format('d/m/Y H:i') : '-' }}</td>
                            <td class="py-2.5 pr-3 hidden md:table-cell">
                                @php
                                    $statusColor = match($sale->status ?? '') {
                                        'completed' => 'bg-success-50 text-success-600 dark:bg-green-900/20 dark:text-success-400',
                                        'pending' => 'bg-warning-50 text-warning-600 dark:bg-yellow-900/20 dark:text-warning-400',
                                        'cancelled' => 'bg-danger-50 text-danger-600 dark:bg-red-900/20 dark:text-danger-400',
                                        default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                                    };
                                @endphp
                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full {{ $statusColor }}">
                                    {{ ucfirst($sale->status ?? 'Unknown') }}
                                </span>
                            </td>
                            <td class="py-2.5 pl-3 text-right text-gray-900 dark:text-white font-medium">Rp {{ number_format($sale->total_amount ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400 dark:text-gray-500">Belum ada data penjualan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function() {
        const ctx = document.getElementById('salesChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels ?? []),
                datasets: [{
                    label: 'Penjualan (Rp)',
                    data: @json($chartData ?? []),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#9ca3af' }
                    },
                    y: {
                        grid: { color: 'rgba(156, 163, 175, 0.2)' },
                        ticks: {
                            color: '#9ca3af',
                            callback: function(value) {
                                if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
                                if (value >= 1000) return (value / 1000).toFixed(0) + 'K';
                                return value;
                            }
                        }
                    }
                }
            }
        });
    })();
</script>
@endpush
@endsection
