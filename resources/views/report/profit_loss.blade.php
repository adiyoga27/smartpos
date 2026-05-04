@extends('layouts.app')

@section('title', 'Laporan Laba Rugi')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('dashboard') }}" class="hover:text-primary-600"><i class="fa-solid fa-home mr-1"></i> Dashboard</a></li>
            <li><span class="mx-1">/</span></li>
            <li class="text-gray-700 dark:text-gray-200 font-medium">Laporan Laba Rugi</li>
        </ol>
    </nav>
@endsection

@section('content')
<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
            <i class="fa-solid fa-chart-pie mr-2 text-primary-500"></i> Laporan Laba Rugi
        </h2>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-primary-50 dark:bg-primary-600/20 flex items-center justify-center">
                    <i class="fa-solid fa-coins text-primary-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Penjualan</p>
                    <p class="text-lg font-bold text-gray-800 dark:text-gray-100">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-warning-50 dark:bg-warning-600/20 flex items-center justify-center">
                    <i class="fa-solid fa-cart-shopping text-warning-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Pembelian</p>
                    <p class="text-lg font-bold text-gray-800 dark:text-gray-100">Rp {{ number_format($totalPurchases, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-danger-50 dark:bg-danger-600/20 flex items-center justify-center">
                    <i class="fa-solid fa-file-invoice-dollar text-danger-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Biaya</p>
                    <p class="text-lg font-bold text-gray-800 dark:text-gray-100">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-success-50 dark:bg-success-600/20 flex items-center justify-center">
                    <i class="fa-solid fa-arrow-trend-up text-success-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Laba Kotor</p>
                    <p class="text-lg font-bold text-success-600">Rp {{ number_format($grossProfit, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-primary-50 dark:bg-primary-600/20 flex items-center justify-center">
                    <i class="fa-solid fa-chart-simple text-primary-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Laba Bersih</p>
                    <p class="text-lg font-bold {{ $netProfit >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                        Rp {{ number_format($netProfit, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-3 items-end">
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Dari Tanggal</label>
                <input type="date" name="from_date" value="{{ request('from_date', $fromDate ?? '') }}" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm py-2 px-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Sampai Tanggal</label>
                <input type="date" name="to_date" value="{{ request('to_date', $toDate ?? '') }}" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm py-2 px-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('reports.profit-loss') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Daily Breakdown Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-3 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Rincian Harian</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="text-left px-4 py-3">Tanggal</th>
                        <th class="text-right px-4 py-3">Penjualan</th>
                        <th class="text-right px-4 py-3">Pembelian</th>
                        <th class="text-right px-4 py-3">Biaya</th>
                        <th class="text-right px-4 py-3">Laba</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($dailyData as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($row['date'])->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-right text-gray-800 dark:text-gray-200">Rp {{ number_format($row['sales'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-gray-800 dark:text-gray-200">Rp {{ number_format($row['purchases'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-gray-800 dark:text-gray-200">Rp {{ number_format($row['expenses'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-semibold {{ $row['profit'] >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                                Rp {{ number_format($row['profit'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-gray-400">
                                <i class="fa-solid fa-chart-pie text-3xl mb-2 block"></i>
                                <p>Tidak ada data laba rugi pada periode ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
