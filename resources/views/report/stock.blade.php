@extends('layouts.app')

@section('title', 'Laporan Stok')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('dashboard') }}" class="hover:text-primary-600"><i class="fa-solid fa-home mr-1"></i> Dashboard</a></li>
            <li><span class="mx-1">/</span></li>
            <li class="text-gray-700 dark:text-gray-200 font-medium">Laporan Stok</li>
        </ol>
    </nav>
@endsection

@section('content')
<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
            <i class="fa-solid fa-boxes-stacked mr-2 text-primary-500"></i> Laporan Stok
        </h2>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="text-left px-4 py-3">Produk</th>
                        <th class="text-left px-4 py-3">SKU</th>
                        <th class="text-left px-4 py-3">Kategori</th>
                        <th class="text-center px-4 py-3">Satuan</th>
                        <th class="text-center px-4 py-3">Stok Saat Ini</th>
                        <th class="text-center px-4 py-3">Alert Qty</th>
                        <th class="text-center px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($products as $product)
                        @php
                            $stockQty = $product->variations->sum(function($variation) {
                                if ($variation->relationLoaded('locationDetails')) {
                                    return $variation->locationDetails->sum('qty_available');
                                }
                                return 0;
                            });
                            $alertQty = $product->alert_quantity ?? 0;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">{{ $product->name }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $product->sku }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $product->category?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">{{ $product->unit?->name ?? 'Pcs' }}</td>
                            <td class="px-4 py-3 text-center font-semibold {{ $stockQty <= $alertQty ? 'text-danger-600' : 'text-gray-800 dark:text-gray-200' }}">
                                {{ number_format($stockQty, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">{{ $alertQty }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($stockQty <= 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-danger-50 text-danger-600 dark:bg-danger-600/20">
                                        <i class="fa-solid fa-xmark mr-1"></i> Habis
                                    </span>
                                @elseif($stockQty <= $alertQty)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-warning-50 text-warning-600 dark:bg-warning-600/20">
                                        <i class="fa-solid fa-triangle-exclamation mr-1"></i> Stok Rendah
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-50 text-success-600 dark:bg-success-600/20">
                                        <i class="fa-solid fa-circle-check mr-1"></i> Tersedia
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                                <i class="fa-solid fa-box text-3xl mb-2 block"></i>
                                <p>Tidak ada data stok.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
