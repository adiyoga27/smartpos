@extends('layouts.app')

@section('title', 'Daftar Produk')

@section('breadcrumb')
    <h1 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Daftar Produk</h1>
@endsection

@section('content')
<div x-data="productIndex()" x-init="init()">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Semua Produk</h2>
        <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
            <i class="fa-solid fa-plus"></i> Tambah Produk
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form method="GET" action="{{ route('products.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk atau SKU..." class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            </div>
            <select name="category_id" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none min-w-[180px]">
                <option value="">Semua Kategori</option>
                @foreach($categories ?? [] as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i class="fa-solid fa-filter mr-1"></i> Filter
            </button>
            @if(request('search') || request('category_id'))
                <a href="{{ route('products.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors inline-flex items-center">
                    <i class="fa-solid fa-xmark mr-1"></i> Reset
                </a>
            @endif
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Gambar</th>
                        <th class="px-4 py-3 font-semibold">SKU</th>
                        <th class="px-4 py-3 font-semibold">Nama</th>
                        <th class="px-4 py-3 font-semibold">Kategori</th>
                        <th class="px-4 py-3 font-semibold">Merek</th>
                        <th class="px-4 py-3 font-semibold">Satuan</th>
                        <th class="px-4 py-3 font-semibold text-right">Stok</th>
                        <th class="px-4 py-3 font-semibold text-right">Harga Jual</th>
                        <th class="px-4 py-3 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($products ?? [] as $product)
                        @php
                            $totalStock = $product->total_stock ?? 0;
                            $defaultVariation = $product->variations->first();
                            $sellPrice = $defaultVariation ? $defaultVariation->default_sell_price : 0;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-3">
                                @if($product->image)
                                    <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="w-10 h-10 rounded object-cover">
                                @else
                                    <div class="w-10 h-10 rounded bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                        <i class="fa-solid fa-box text-gray-400 text-xs"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $product->sku }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $product->name }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $product->category?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $product->brand?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $product->unit?->actual_name ?? '-' }}</td>
                            <td class="px-4 py-3 text-right">
                                @if($product->enable_stock)
                                    <span class="font-semibold {{ $totalStock <= ($product->alert_quantity ?? 0) ? 'text-danger-600' : 'text-gray-700 dark:text-gray-200' }}">
                                        {{ number_format($totalStock, 0) }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">--</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-200">
                                Rp {{ number_format($sellPrice, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('products.edit', $product->id) }}" class="p-1.5 text-primary-600 hover:text-primary-700 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded transition-colors" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" x-on:click="confirmDelete({{ $product->id }}, '{{ addslashes($product->name) }}')" class="p-1.5 text-danger-500 hover:text-danger-600 hover:bg-danger-50 dark:hover:bg-red-900/20 rounded transition-colors" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                                <i class="fa-solid fa-box-open text-3xl mb-2 block"></i>
                                Belum ada produk. Klik <a href="{{ route('products.create') }}" class="text-primary-500 hover:underline">Tambah Produk</a> untuk memulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(!empty($products) && $products->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-show="showDeleteModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-on:click.self="showDeleteModal = false">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2">Konfirmasi Hapus</h3>
            <p class="text-gray-600 dark:text-gray-300 mb-4">Apakah Anda yakin ingin menghapus produk <strong x-text="deleteName"></strong>?</p>
            <div class="flex justify-end gap-3">
                <button x-on:click="showDeleteModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Batal</button>
                <form method="POST" x-bind:action="'{{ route('products.destroy', '__ID__') }}'.replace('__ID__', deleteId)">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-danger-500 hover:bg-danger-600 text-white text-sm font-medium rounded-lg transition-colors">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function productIndex() {
        return {
            showDeleteModal: false,
            deleteId: null,
            deleteName: '',
            confirmDelete(id, name) {
                this.deleteId = id;
                this.deleteName = name;
                this.showDeleteModal = true;
            }
        }
    }
</script>
@endpush
