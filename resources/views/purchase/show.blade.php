@extends('layouts.app')

@section('title', 'Detail Pembelian #' . ($purchase->ref_no ?: $purchase->id))

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('dashboard') }}" class="hover:text-primary-600"><i class="fa-solid fa-home mr-1"></i> Dashboard</a></li>
            <li><span class="mx-1">/</span></li>
            <li><a href="{{ route('purchases.index') }}" class="hover:text-primary-600">Pembelian</a></li>
            <li><span class="mx-1">/</span></li>
            <li class="text-gray-700 dark:text-gray-200 font-medium">#{{ $purchase->ref_no ?: $purchase->id }}</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Invoice Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $purchase->location?->name ?? 'SmartPOS' }}</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $purchase->location?->address }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">No. Referensi</p>
                <p class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $purchase->ref_no ?: '-' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Tanggal</p>
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $purchase->transaction_date?->format('d M Y, H:i') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Supplier</p>
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $purchase->contact?->supplier_business_name ?? $purchase->contact?->full_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</p>
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
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Lokasi</p>
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $purchase->location?->name ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Items Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-4">
        <div class="px-6 py-3 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Item Pembelian</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="text-left px-4 py-3">Produk</th>
                        <th class="text-center px-4 py-3 w-20">Qty</th>
                        <th class="text-right px-4 py-3 w-32">Harga Beli</th>
                        <th class="text-right px-4 py-3 w-32">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($purchase->items as $item)
                        <tr>
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                                <p class="font-medium">{{ $item->product?->name ?? 'Produk #'.$item->product_id }}</p>
                                @if($item->variation?->name)
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->variation->name }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">Rp {{ number_format($item->purchase_price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-gray-200">Rp {{ number_format($item->quantity * $item->purchase_price, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-400">Tidak ada item.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Totals --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-4">
        <div class="space-y-2">
            @php
                $purchaseSubtotal = $purchase->items->sum(fn($i) => $i->quantity * $i->purchase_price);
            @endphp
            <div class="flex justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                <span class="font-medium text-gray-800 dark:text-gray-200">Rp {{ number_format($purchaseSubtotal, 0, ',', '.') }}</span>
            </div>
            @if($purchase->discount_amount > 0)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Diskon {{ $purchase->discount_type === 'percentage' ? '('.$purchase->discount_amount.'%)' : '' }}</span>
                    <span class="font-medium text-danger-600">-Rp {{ number_format($purchase->discount_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            @if($purchase->tax_amount > 0)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Pajak {{ $purchase->tax?->name ? '('.$purchase->tax->name.')' : '' }}</span>
                    <span class="font-medium text-gray-800 dark:text-gray-200">Rp {{ number_format($purchase->tax_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="flex justify-between text-base font-bold border-t border-gray-200 dark:border-gray-600 pt-2 mt-2">
                <span class="text-gray-800 dark:text-gray-100">Grand Total</span>
                <span class="text-primary-600 dark:text-primary-400">Rp {{ number_format($purchase->final_total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Payment Info --}}
    @if($purchase->payments->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Informasi Pembayaran</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="text-left px-4 py-3">Metode</th>
                            <th class="text-right px-4 py-3">Jumlah</th>
                            <th class="text-left px-4 py-3">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($purchase->payments as $payment)
                            <tr>
                                <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                                    @if($payment->method === 'cash')
                                        <i class="fa-solid fa-money-bill-wave mr-1.5 text-success-500"></i> Tunai
                                    @elseif($payment->method === 'bank_transfer')
                                        <i class="fa-solid fa-building-columns mr-1.5 text-warning-500"></i> Transfer Bank
                                    @else
                                        <i class="fa-solid fa-circle-dollar mr-1.5 text-gray-500"></i> {{ ucfirst($payment->method) }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-gray-200">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $payment->paid_on?->format('d M Y, H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="mt-4 flex gap-2">
        <a href="{{ route('purchases.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>
</div>
@endsection
