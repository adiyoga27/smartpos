@extends('layouts.app')

@section('title', 'Daftar Pembelian')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('dashboard') }}" class="hover:text-primary-600"><i class="fa-solid fa-home mr-1"></i> Dashboard</a></li>
            <li><span class="mx-1">/</span></li>
            <li class="text-gray-700 dark:text-gray-200 font-medium">Daftar Pembelian</li>
        </ol>
    </nav>
@endsection

@section('content')
<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
            <i class="fa-solid fa-truck mr-2 text-primary-500"></i> Daftar Pembelian
        </h2>
        <a href="{{ route('purchases.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
            <i class="fa-solid fa-plus mr-2"></i> Pembelian Baru
        </a>
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
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">No. Referensi</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor ref..." class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm py-2 px-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('purchases.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
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
                        <th class="text-left px-4 py-3">No. Ref</th>
                        <th class="text-left px-4 py-3">Tanggal</th>
                        <th class="text-left px-4 py-3">Supplier</th>
                        <th class="text-right px-4 py-3">Total</th>
                        <th class="text-center px-4 py-3">Status Bayar</th>
                        <th class="text-center px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($purchases as $purchase)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3 font-medium text-primary-600 dark:text-primary-400">{{ $purchase->ref_no ?: '-' }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $purchase->transaction_date?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $purchase->contact?->supplier_business_name ?? $purchase->contact?->full_name ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-gray-200">Rp {{ number_format($purchase->final_total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($purchase->payment_status === 'paid')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-50 text-success-600 dark:bg-success-600/20">
                                        <i class="fa-solid fa-circle-check mr-1"></i> Lunas
                                    </span>
                                @elseif($purchase->payment_status === 'partial')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-warning-50 text-warning-600 dark:bg-warning-600/20">
                                        <i class="fa-solid fa-circle-half-stroke mr-1"></i> Sebagian
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-danger-50 text-danger-600 dark:bg-danger-600/20">
                                        <i class="fa-solid fa-circle-exclamation mr-1"></i> Belum Lunas
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('purchases.show', $purchase) }}" class="p-1.5 text-primary-600 hover:text-primary-700 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded transition-colors" title="Lihat">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                                <i class="fa-solid fa-truck text-3xl mb-2 block"></i>
                                <p>Belum ada data pembelian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($purchases->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
