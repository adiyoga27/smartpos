@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('dashboard') }}" class="hover:text-primary-600"><i class="fa-solid fa-home mr-1"></i> Dashboard</a></li>
            <li><span class="mx-1">/</span></li>
            <li class="text-gray-700 dark:text-gray-200 font-medium">Laporan Penjualan</li>
        </ol>
    </nav>
@endsection

@section('content')
<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
            <i class="fa-solid fa-chart-line mr-2 text-primary-500"></i> Laporan Penjualan
        </h2>
        <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            <i class="fa-solid fa-download mr-2"></i> Export
        </button>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-primary-50 dark:bg-primary-600/20 flex items-center justify-center">
                    <i class="fa-solid fa-coins text-primary-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Penjualan</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-gray-100">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-success-50 dark:bg-success-600/20 flex items-center justify-center">
                    <i class="fa-solid fa-circle-check text-success-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Dibayar</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-gray-100">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-3 items-end">
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Dari Tanggal</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm py-2 px-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Sampai Tanggal</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm py-2 px-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('report.sales') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="text-left px-4 py-3">No. Invoice</th>
                        <th class="text-left px-4 py-3">Tanggal</th>
                        <th class="text-left px-4 py-3">Pelanggan</th>
                        <th class="text-right px-4 py-3">Total</th>
                        <th class="text-center px-4 py-3">Status Bayar</th>
                        <th class="text-right px-4 py-3">Dibayar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3 font-medium text-primary-600 dark:text-primary-400">{{ $sale->invoice_no }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $sale->transaction_date?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $sale->contact?->full_name ?? 'Umum' }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-gray-200">Rp {{ number_format($sale->final_total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($sale->payment_status === 'paid')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-50 text-success-600 dark:bg-success-600/20">
                                        <i class="fa-solid fa-circle-check mr-1"></i> Lunas
                                    </span>
                                @elseif($sale->payment_status === 'partial')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-warning-50 text-warning-600 dark:bg-warning-600/20">
                                        <i class="fa-solid fa-circle-half-stroke mr-1"></i> Sebagian
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-danger-50 text-danger-600 dark:bg-danger-600/20">
                                        <i class="fa-solid fa-circle-exclamation mr-1"></i> Belum Lunas
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">
                                Rp {{ number_format($sale->payments->sum('amount'), 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                                <i class="fa-solid fa-receipt text-3xl mb-2 block"></i>
                                <p>Tidak ada data penjualan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sales->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $sales->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
