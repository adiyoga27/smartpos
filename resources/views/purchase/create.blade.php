@extends('layouts.app')

@section('title', 'Pembelian Baru')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('dashboard') }}" class="hover:text-primary-600"><i class="fa-solid fa-home mr-1"></i> Dashboard</a></li>
            <li><span class="mx-1">/</span></li>
            <li><a href="{{ route('purchases.index') }}" class="hover:text-primary-600">Pembelian</a></li>
            <li><span class="mx-1">/</span></li>
            <li class="text-gray-700 dark:text-gray-200 font-medium">Baru</li>
        </ol>
    </nav>
@endsection

@section('content')
<div x-data="purchaseForm()" x-cloak>
    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="max-w-4xl mx-auto mb-4 p-4 bg-danger-50 border border-red-200 rounded-lg">
            <div class="flex items-center gap-2 text-red-700 font-medium text-sm mb-2">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>Terjadi kesalahan. Silakan periksa kembali:</span>
            </div>
            <ul class="list-disc list-inside text-sm text-red-600 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('purchases.store') }}" x-on:submit="validateForm($event)" class="max-w-4xl mx-auto space-y-4">
        @csrf

        {{-- Header Info --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-4">Informasi Pembelian</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Supplier <span class="text-red-500">*</span></label>
                    <select name="contact_id" required class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm py-2 px-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('contact_id') border-danger-500 @enderror">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('contact_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->supplier_business_name ?: $supplier->full_name }}</option>
                        @endforeach
                    </select>
                    @error('contact_id')<p class="mt-1 text-xs text-danger-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Lokasi <span class="text-red-500">*</span></label>
                    <select name="location_id" required class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm py-2 px-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('location_id') border-danger-500 @enderror">
                        <option value="">-- Pilih Lokasi --</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                        @endforeach
                    </select>
                    @error('location_id')<p class="mt-1 text-xs text-danger-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">No. Referensi</label>
                    <input type="text" name="ref_no" value="{{ old('ref_no') }}" placeholder="No. referensi / invoice supplier" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm py-2 px-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('ref_no') border-danger-500 @enderror">
                    @error('ref_no')<p class="mt-1 text-xs text-danger-500">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Item Search & Add --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 @error('items') border-danger-500 @enderror">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">Item Pembelian</h3>
            @error('items')<p class="text-xs text-danger-500 mb-2">{{ $message }}</p>@enderror
            @error('items.0')<p class="text-xs text-danger-500 mb-2">Item pembelian tidak valid. Pastikan semua item memiliki produk, jumlah, dan harga yang benar.</p>@enderror
            <div class="flex gap-2 mb-4">
                <div class="relative flex-1">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" x-model="productSearch" x-on:keyup.debounce.300ms="searchProducts()" x-on:keydown.enter.prevent="handleScanOrSearch()" placeholder="Ketik minimal 2 huruf untuk mencari produk..." class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>

            {{-- Search Results Dropdown --}}
            <div x-show="productResults.length > 0" class="mb-4 border border-gray-200 dark:border-gray-600 rounded-lg max-h-48 overflow-y-auto bg-white dark:bg-gray-700 shadow-sm">
                <template x-for="product in productResults" :key="product.id">
                    <div x-on:click="addItem(product)" class="px-3 py-2 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 border-b border-gray-100 dark:border-gray-700 last:border-0">
                        <p class="text-sm text-gray-800 dark:text-gray-200 font-medium" x-text="product.name"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="'SKU: ' + (product.sku || '-') + ' | Harga Beli: Rp ' + formatNumber(product.purchase_price || 0)"></p>
                    </div>
                </template>
            </div>

            {{-- Items Table --}}
            <template x-if="items.length === 0">
                <div class="text-center py-8 text-gray-400">
                    <i class="fa-solid fa-box-open text-2xl mb-1 block"></i>
                    <p class="text-sm">Belum ada item. Cari dan tambahkan produk.</p>
                </div>
            </template>

            <template x-if="items.length > 0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">
                            <tr>
                                <th class="text-left px-3 py-2">Produk</th>
                                <th class="text-center px-3 py-2 w-20">Qty</th>
                                <th class="text-right px-3 py-2 w-32">Harga Beli</th>
                                <th class="text-right px-3 py-2 w-32">Total</th>
                                <th class="text-center px-3 py-2 w-10"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in items" :key="item.variation_id">
                                <tr class="border-b border-gray-100 dark:border-gray-700/50">
                                    <td class="px-3 py-2">
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200" x-text="item.name"></p>
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" x-model.number="item.qty" x-on:input="recalc()" min="0.01" step="0.01" class="w-20 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-xs py-1.5 focus:ring-1 focus:ring-primary-500">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" x-model.number="item.purchase_price" x-on:input="recalc()" min="0" class="w-28 text-right border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-xs py-1.5 focus:ring-1 focus:ring-primary-500">
                                    </td>
                                    <td class="px-3 py-2 text-right text-sm font-semibold text-gray-800 dark:text-gray-200" x-text="'Rp ' + formatNumber(item.qty * item.purchase_price)"></td>
                                    <td class="px-3 py-2 text-center">
                                        <button type="button" x-on:click="removeItem(index)" class="text-red-500 hover:text-red-700 text-xs">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </template>

            {{-- Hidden item inputs --}}
            <template x-for="(item, idx) in items" :key="idx">
                <div>
                    <input type="hidden" :name="'items[' + idx + '][product_id]'" :value="item.product_id">
                    <input type="hidden" :name="'items[' + idx + '][variation_id]'" :value="item.variation_id">
                    <input type="hidden" :name="'items[' + idx + '][quantity]'" :value="item.qty">
                    <input type="hidden" :name="'items[' + idx + '][purchase_price]'" :value="item.purchase_price">
                </div>
            </template>
        </div>

        {{-- Totals --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">Total</h3>
            <div class="space-y-2 max-w-sm">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                    <span class="font-medium text-gray-800 dark:text-gray-200" x-text="'Rp ' + formatNumber(subtotal)"></span>
                </div>

                <div class="flex items-center gap-2">
                    <select name="discount_type" x-model="discountType" class="border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-xs py-1.5 px-2 focus:ring-1 focus:ring-primary-500">
                        <option value="fixed">Rp</option>
                        <option value="percentage">%</option>
                    </select>
                    <input type="number" name="discount_amount" x-model.number="discountAmountVal" x-on:input="recalc()" min="0" class="flex-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-xs py-1.5 px-2 focus:ring-1 focus:ring-primary-500" placeholder="Jumlah diskon">
                    <span class="text-gray-600 dark:text-gray-400 text-xs whitespace-nowrap" x-text="'-Rp ' + formatNumber(discountValue)"></span>
                </div>

                <div class="flex items-center gap-2">
                    <select name="tax_id" x-model="selectedTaxId" x-on:change="recalc()" class="flex-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-xs py-1.5 px-2 focus:ring-1 focus:ring-primary-500">
                        <option value="">Tanpa Pajak</option>
                        @foreach($taxRates as $tax)
                            <option value="{{ $tax->id }}">{{ $tax->name }} ({{ $tax->amount }}%)</option>
                        @endforeach
                    </select>
                    <span class="text-gray-600 dark:text-gray-400 text-xs whitespace-nowrap" x-text="'Rp ' + formatNumber(taxAmount)"></span>
                </div>

                <div class="flex justify-between text-base font-bold border-t border-gray-200 dark:border-gray-600 pt-2">
                    <span class="text-gray-800 dark:text-gray-100">Total Akhir</span>
                    <span class="text-primary-600 dark:text-primary-400" x-text="'Rp ' + formatNumber(grandTotal)"></span>
                </div>
            </div>
        </div>

        {{-- Payment (Optional) --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">Pembayaran (Opsional)</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-sm">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Metode</label>
                    <select name="payment_method" x-model="paymentMethod" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm py-2 px-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="cash">Tunai</option>
                        <option value="bank_transfer">Transfer Bank</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah Dibayar</label>
                    <input type="number" name="payment_amount" x-model.number="paymentAmount" min="0" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm py-2 px-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end gap-2">
            <a href="{{ route('purchases.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Batal
            </a>
            <button type="submit" x-bind:disabled="items.length === 0" class="px-6 py-2 text-white text-sm font-medium rounded-lg transition-colors" x-bind:class="items.length === 0 ? 'bg-gray-400 cursor-not-allowed' : 'bg-primary-600 hover:bg-primary-700'">
                <i class="fa-solid fa-save mr-2"></i> Simpan Pembelian
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const purchaseTaxRates = @json($taxRatesJson);

    function purchaseForm() {
        return {
            productSearch: '',
            productResults: [],
            items: [],
            discountType: 'fixed',
            discountAmountVal: 0,
            selectedTaxId: '',
            paymentMethod: 'cash',
            paymentAmount: 0,

            get subtotal() {
                return this.items.reduce((sum, item) => sum + (item.qty * item.purchase_price), 0);
            },

            get discountValue() {
                if (!this.discountAmountVal) return 0;
                if (this.discountType === 'percentage') {
                    return this.subtotal * (this.discountAmountVal / 100);
                }
                return this.discountAmountVal;
            },

            get taxAmount() {
                if (!this.selectedTaxId) return 0;
                const tax = purchaseTaxRates.find(t => t.id == this.selectedTaxId);
                if (!tax) return 0;
                return (this.subtotal - this.discountValue) * (tax.amount / 100);
            },

            get grandTotal() {
                return this.subtotal - this.discountValue + this.taxAmount;
            },

            async searchProducts() {
                if (!this.productSearch || this.productSearch.length < 2) {
                    this.productResults = [];
                    return;
                }
                try {
                    const response = await fetch('/api/products/search?q=' + encodeURIComponent(this.productSearch));
                    const data = await response.json();
                    this.productResults = data.data || data || [];
                } catch (e) {
                    console.error('Search error:', e);
                    this.productResults = [];
                }
            },

            addItem(product) {
                if (!product.variation_id) {
                    alert('Produk ini tidak memiliki data variasi yang valid.');
                    return;
                }
                const existing = this.items.find(item => item.variation_id == product.variation_id);
                if (existing) {
                    existing.qty += 1;
                } else {
                    this.items.push({
                        product_id: product.id,
                        variation_id: product.variation_id,
                        name: product.name,
                        purchase_price: parseFloat(product.purchase_price || 0),
                        qty: 1,
                    });
                }
                this.productSearch = '';
                this.productResults = [];
            },

            handleScanOrSearch() {
                if (this.productResults.length === 1) {
                    this.addItem(this.productResults[0]);
                } else if (this.productResults.length > 1) {
                    this.addItem(this.productResults[0]);
                }
            },

            validateForm(event) {
                if (this.items.length === 0) {
                    event.preventDefault();
                    alert('Tambahkan minimal satu item pembelian.');
                    return;
                }
                for (const item of this.items) {
                    if (!item.product_id || !item.variation_id || !item.qty || !item.purchase_price) {
                        event.preventDefault();
                        alert('Beberapa item tidak lengkap. Silakan periksa kembali.');
                        return;
                    }
                }
            },

            removeItem(index) {
                this.items.splice(index, 1);
            },

            recalc() {
                // Triggers reactive recalculation
            },

            formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(parseFloat(num) || 0);
            },
        };
    }
</script>
@endpush
