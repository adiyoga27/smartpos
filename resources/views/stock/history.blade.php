@extends('layouts.app')

@section('title', 'Riwayat Stok Produk')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('dashboard') }}" class="hover:text-primary-600">Dashboard</a></li>
            <li><span class="mx-1">/</span></li>
            <li><a href="{{ route('stock.adjustments.index') }}" class="hover:text-primary-600">Stok</a></li>
            <li><span class="mx-1">/</span></li>
            <li class="text-gray-700 dark:text-gray-200 font-medium">Riwayat Stok</li>
        </ol>
    </nav>
@endsection

@section('content')
<div>
    @if($selectedProduct ?? false)
        {{-- Product Detail History View --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Riwayat: {{ $selectedProduct->name }}</h2>
                <p class="text-sm text-gray-500">SKU: {{ $selectedProduct->sku ?? '-' }}</p>
            </div>
            <a href="{{ route('stock.history') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700">
                <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Daftar
            </a>
        </div>

        {{-- Stock per Location --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Stok Saat Ini</h3>
            <div class="flex flex-wrap gap-3">
                @foreach($locations as $loc)
                    @php
                        $locStock = \App\Models\VariationLocationDetail::whereIn('variation_id', $selectedProduct->variations->pluck('id'))
                            ->where('location_id', $loc->id)
                            ->sum('qty_available');
                    @endphp
                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-700 rounded-lg text-center min-w-[80px]">
                        <div class="text-xs text-gray-500">{{ $loc->name }}</div>
                        <div class="text-lg font-bold {{ $locStock > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($locStock, 2) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- History Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Riwayat Pergerakan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">Tanggal</th>
                            <th class="px-4 py-3 text-left font-medium">Tipe</th>
                            <th class="px-4 py-3 text-left font-medium">Referensi</th>
                            <th class="px-4 py-3 text-left font-medium">Lokasi</th>
                            <th class="px-4 py-3 text-right font-medium">Qty</th>
                            <th class="px-4 py-3 text-right font-medium">Total Harga</th>
                            <th class="px-4 py-3 text-left font-medium">User</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($history as $item)
                            @php $tx = $item->transaction; @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                    {{ $tx?->transaction_date?->format('d/m/Y H:i') ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                        {{ ($tx->type ?? '') === 'sell' ? 'bg-red-100 text-red-700' : '' }}
                                        {{ ($tx->type ?? '') === 'purchase' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ ($tx->type ?? '') === 'stock_adjustment' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                        {{ ($tx->type ?? '') === 'stock_transfer' ? 'bg-purple-100 text-purple-700' : '' }}">
                                        {{ match($tx->type ?? '') {
                                            'sell' => 'Penjualan',
                                            'purchase' => 'Pembelian',
                                            'stock_adjustment' => 'Adjustment',
                                            'stock_transfer' => 'Transfer',
                                            default => $tx->type ?? '-'
                                        } }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($tx)
                                        @if($tx->type === 'sell')
                                            <a href="{{ route('sales.show', $tx->id) }}" class="text-primary-600 hover:underline">{{ $tx->invoice_no ?? '#'.$tx->id }}</a>
                                        @elseif($tx->type === 'stock_adjustment')
                                            <a href="{{ route('stock.adjustments.show', $tx->id) }}" class="text-primary-600 hover:underline">Adj #{{ $tx->id }}</a>
                                        @else
                                            <span class="text-gray-600">{{ $tx->invoice_no ?? '#'.$tx->id }}</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $tx?->location?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-right font-medium
                                    {{ ($tx->type ?? '') === 'sell' ? 'text-red-600' : '' }}
                                    {{ in_array($tx->type ?? '', ['purchase']) ? 'text-green-600' : '' }}
                                    {{ ($tx->type ?? '') === 'stock_adjustment' && $item->quantity > 0 ? 'text-green-600' : '' }}
                                    {{ ($tx->type ?? '') === 'stock_adjustment' && $item->quantity < 0 ? 'text-red-600' : '' }}
                                    {{ ($tx->type ?? '') === 'stock_transfer' ? 'text-purple-600' : '' }}">
                                    {{ $item->quantity > 0 ? '+' : '' }}{{ rtrim(rtrim(number_format($item->quantity, 4), '0'), '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">
                                    {{ $item->unit_price > 0 ? 'Rp '.number_format($item->unit_price * abs($item->quantity), 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">
                                    {{ $tx?->creator?->name ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Belum ada riwayat pergerakan stok untuk produk ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                {{ $history->appends(['product_id' => $productId])->links() }}
            </div>
        </div>
    @else
        {{-- Product List View --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Riwayat Pergerakan Stok</h2>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
            <form method="GET" action="{{ route('stock.history') }}" class="flex gap-2">
                <div class="relative flex-1">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari produk berdasarkan nama, SKU, atau barcode..." class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700">
                    <i class="fa-solid fa-search mr-1"></i> Cari
                </button>
                @if($search ?? false)
                    <a href="{{ route('stock.history') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium w-12"></th>
                            <th class="px-4 py-3 text-left font-medium">Produk</th>
                            <th class="px-4 py-3 text-left font-medium">SKU</th>
                            <th class="px-4 py-3 text-left font-medium">Satuan</th>
                            <th class="px-4 py-3 text-right font-medium">Total Stok</th>
                            <th class="px-4 py-3 text-center font-medium w-20">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($products as $product)
                            @php
                                $totalStock = $product->variations->flatMap->locationDetails->sum('qty_available');
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                <td class="px-4 py-3">
                                    @if($product->image)
                                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-10 h-10 rounded object-cover bg-gray-100 dark:bg-gray-600">
                                    @else
                                        <div class="w-10 h-10 rounded bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-gray-400">
                                            <i class="fa-solid fa-box text-xs"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-800 dark:text-gray-200 font-medium">{{ $product->name }}</td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 font-mono text-xs">{{ $product->sku ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $product->unit?->short_name ?? '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <span class="font-semibold {{ $totalStock > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($totalStock, 2) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('stock.history', ['product_id' => $product->id]) }}" class="inline-flex items-center px-3 py-1.5 bg-primary-600 text-white text-xs font-medium rounded-lg hover:bg-primary-700">
                                        <i class="fa-solid fa-eye mr-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <i class="fa-solid fa-boxes-stacked text-3xl text-gray-300 mb-2 block"></i>
                                    Produk tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                {{ $products->appends(['search' => $search ?? ''])->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
