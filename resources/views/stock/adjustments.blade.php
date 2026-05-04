@extends('layouts.app')

@section('title', 'Stok Opname / Adjustment')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('dashboard') }}" class="hover:text-primary-600">Dashboard</a></li>
            <li><span class="mx-1">/</span></li>
            <li class="text-gray-700 dark:text-gray-200 font-medium">Stok Adjustment</li>
        </ol>
    </nav>
@endsection

@section('content')
<div x-data="adjustmentList()">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Stok Adjustment</h2>
        <div class="flex gap-2">
            <a href="{{ route('stock.history') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700">
                <i class="fa-solid fa-clock-rotate-left mr-2"></i> Riwayat Stok
            </a>
            <button @click="openAddModal()" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700">
                <i class="fa-solid fa-plus mr-2"></i> Adjustment Baru
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <div class="p-4 flex items-end gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dari Tanggal</label>
                <input type="date" x-model="filters.date" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <button @click="applyFilter()" class="px-4 py-2 bg-primary-600 text-white text-sm rounded-lg hover:bg-primary-700">Filter</button>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Tanggal</th>
                        <th class="px-4 py-3 text-left font-medium">Lokasi</th>
                        <th class="px-4 py-3 text-left font-medium">Tipe</th>
                        <th class="px-4 py-3 text-left font-medium">Catatan</th>
                        <th class="px-4 py-3 text-center font-medium">Item</th>
                        <th class="px-4 py-3 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($adjustments ?? [] as $adj)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $adj->transaction_date->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $adj->location?->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if($adj->type === 'opening_stock')
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                Stok Awal
                            </span>
                            @else
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $adj->adjustment_type === 'abnormal' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' }}">
                                {{ $adj->adjustment_type === 'abnormal' ? 'Abnormal' : 'Normal' }}
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 max-w-[200px] truncate">{{ $adj->additional_notes ?? '-' }}</td>
                        <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">{{ $adj->items_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('stock.adjustments.show', $adj->id) }}" class="text-primary-600 hover:text-primary-800"><i class="fa-solid fa-eye"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">Belum ada adjustment stok.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            {{ ($adjustments ?? collect())->links() }}
        </div>
    </div>

    {{-- Add New Adjustment Modal --}}
    <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900/50" @click="showAddModal = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Adjustment Stok Baru</h3>
                    <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <form action="{{ route('stock.adjustments.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal</label>
                                <input type="date" name="date" x-model="form.date" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lokasi</label>
                                <select name="location_id" x-model="form.location_id" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">Pilih Lokasi</option>
                                    @foreach($locations ?? [] as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe Adjustment</label>
                                <select name="adjustment_type" x-model="form.adjustment_type" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="normal">Normal</option>
                                    <option value="abnormal">Abnormal</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan</label>
                            <textarea name="additional_notes" x-model="form.notes" rows="2" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Alasan adjustment..."></textarea>
                        </div>

                        {{-- Items Section --}}
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3">Item Stok</h4>
                            <div class="flex gap-2 mb-3">
                                <div class="flex-1 relative">
                                    <input type="text" x-model="itemSearch" @input.debounce.300="searchProducts()" @keydown.enter.prevent="barcodeScan()" placeholder="Cari produk (nama, SKU, atau barcode)..." class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                                    <div x-show="searchResults.length > 0" class="absolute z-10 w-full mt-1 max-h-48 overflow-y-auto bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 shadow-lg">
                                        <template x-for="product in searchResults" :key="product.id">
                                            <div @click="selectProduct(product)" class="px-3 py-2 cursor-pointer hover:bg-primary-50 dark:hover:bg-primary-900/20 text-sm text-gray-700 dark:text-gray-300 border-b border-gray-100 dark:border-gray-600 last:border-0">
                                                <div class="font-medium" x-text="product.name"></div>
                                                <div class="text-xs text-gray-400">
                                                    <span x-text="'SKU: ' + (product.sku || '-')"></span>
                                                    <span x-show="product.barcode"> | Barcode: <span x-text="product.barcode"></span></span>
                                                    <span> | Stok: <span x-text="formatNumber(product.qty_available || 0)"></span></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <div class="w-32">
                                    <input type="number" x-model="itemQty" placeholder="Qty (±)" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                                    <p class="text-xs text-gray-400 mt-0.5">Positif: tambah, Negatif: kurang</p>
                                </div>
                                <button type="button" @click="addItem()" class="px-4 py-2 bg-success-500 text-white text-sm rounded-lg hover:bg-success-600 flex-shrink-0">Tambah</button>
                            </div>
                            <table class="w-full text-sm" x-show="items.length > 0">
                                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium">Produk</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium w-24">Qty</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium w-20">Aksi</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium w-12"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr>
                                            <td class="px-3 py-2 text-gray-700 dark:text-gray-300 text-sm">
                                                <span x-text="item.product_name"></span>
                                                <input type="hidden" :name="'items[' + index + '][product_id]'" :value="item.product_id">
                                                <input type="hidden" :name="'items[' + index + '][variation_id]'" :value="item.variation_id">
                                            </td>
                                            <td class="px-3 py-2 text-right">
                                                <input type="number" :name="'items[' + index + '][quantity]'" x-model.number="item.quantity" step="any" class="w-20 text-right rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-2 py-1 focus:ring-primary-500 focus:border-primary-500">
                                            </td>
                                            <td class="px-3 py-2 text-center">
                                                <span class="text-xs font-medium px-2 py-0.5 rounded-full" x-bind:class="item.quantity > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" x-text="item.quantity > 0 ? '+ Stok' : '- Stok'"></span>
                                            </td>
                                            <td class="px-3 py-2 text-right">
                                                <button type="button" @click="items.splice(index, 1)" class="text-red-500 hover:text-red-700"><i class="fa-solid fa-xmark"></i></button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                            <p x-show="items.length === 0" class="text-sm text-gray-400 dark:text-gray-500 text-center py-3">Belum ada item. Cari produk di atas.</p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">Batal</button>
                        <button type="submit" class="px-6 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700">Simpan Adjustment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function adjustmentList() {
    return {
        showAddModal: false,
        filters: { date: '' },
        form: { date: '{{ date('Y-m-d') }}', location_id: '', adjustment_type: 'normal', notes: '' },
        itemSearch: '',
        itemQty: '',
        items: [],
        searchResults: [],
        selectedProduct: null,

        openAddModal() {
            this.showAddModal = true;
            this.items = [];
            this.itemSearch = '';
            this.itemQty = '';
            this.searchResults = [];
            this.selectedProduct = null;
        },

        async searchProducts() {
            if (this.itemSearch.length < 2) { this.searchResults = []; return; }
            try {
                const resp = await fetch('{{ route('product.search') }}?q=' + encodeURIComponent(this.itemSearch));
                this.searchResults = await resp.json();
            } catch (e) {
                this.searchResults = [];
            }
        },

        selectProduct(product) {
            this.selectedProduct = product;
            this.itemSearch = product.name;
            this.searchResults = [];
        },

        barcodeScan() {
            if (this.searchResults.length === 1) {
                this.selectProduct(this.searchResults[0]);
                if (this.itemQty === '' || this.itemQty === null) {
                    this.itemQty = 1;
                }
                this.addItem();
            }
        },

        addItem() {
            if (!this.selectedProduct || this.itemQty === '' || this.itemQty === null) return;
            this.items.push({
                product_id: this.selectedProduct.id,
                variation_id: this.selectedProduct.variation_id,
                product_name: this.selectedProduct.name,
                quantity: parseFloat(this.itemQty) || 0,
            });
            this.itemSearch = '';
            this.itemQty = '';
            this.selectedProduct = null;
            this.searchResults = [];
        },

        formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(parseFloat(num) || 0);
        },

        applyFilter() {
            const params = new URLSearchParams();
            if (this.filters.date) params.set('from_date', this.filters.date);
            window.location.href = '{{ route('stock.adjustments.index') }}?' + params.toString();
        }
    }
}
</script>
@endpush
