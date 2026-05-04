@extends('layouts.app')

@section('title', 'Detail Adjustment')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('dashboard') }}" class="hover:text-primary-600">Dashboard</a></li>
            <li><span class="mx-1">/</span></li>
            <li><a href="{{ route('stock.adjustments.index') }}" class="hover:text-primary-600">Stok Adjustment</a></li>
            <li><span class="mx-1">/</span></li>
            <li class="text-gray-700 dark:text-gray-200 font-medium">#{{ $adjustment->id }}</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Detail Adjustment #{{ $adjustment->id }}</h2>
        <a href="{{ route('stock.adjustments.index') }}" class="text-sm text-primary-600 hover:text-primary-800">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500 dark:text-gray-400">Tanggal</span>
                <p class="font-medium text-gray-800 dark:text-gray-200">{{ $adjustment->transaction_date->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Lokasi</span>
                <p class="font-medium text-gray-800 dark:text-gray-200">{{ $adjustment->location?->name ?? '-' }}</p>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Tipe Adjustment</span>
                <p>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $adjustment->adjustment_type === 'abnormal' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                        {{ $adjustment->adjustment_type === 'abnormal' ? 'Abnormal' : 'Normal' }}
                    </span>
                </p>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Dibuat Oleh</span>
                <p class="font-medium text-gray-800 dark:text-gray-200">{{ $adjustment->creator?->name ?? '-' }}</p>
            </div>
            @if($adjustment->additional_notes)
                <div class="sm:col-span-2">
                    <span class="text-gray-500 dark:text-gray-400">Catatan</span>
                    <p class="font-medium text-gray-800 dark:text-gray-200">{{ $adjustment->additional_notes }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-semibold text-gray-800 dark:text-gray-100">Item Adjustment</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Produk</th>
                        <th class="px-4 py-3 text-left font-medium">Variasi</th>
                        <th class="px-4 py-3 text-right font-medium">Qty</th>
                        <th class="px-4 py-3 text-center font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($adjustment->items as $item)
                        <tr>
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                                <a href="{{ route('stock.history', ['product_id' => $item->product_id]) }}" class="hover:text-primary-600">
                                    {{ $item->product?->name ?? '-' }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $item->variation?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-right">
                                <span class="font-medium {{ $item->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $item->quantity > 0 ? '+' : '' }}{{ rtrim(rtrim(number_format($item->quantity, 4), '0'), '.') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $item->quantity > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $item->quantity > 0 ? 'Stok Masuk' : 'Stok Keluar' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">Tidak ada item.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
