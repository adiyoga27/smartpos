@extends('layouts.app')

@section('title', 'Kasir POS')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('dashboard') }}" class="hover:text-primary-600"><i class="fa-solid fa-home mr-1"></i> Dashboard</a></li>
            <li><span class="mx-1">/</span></li>
            <li class="text-gray-700 dark:text-gray-200 font-medium">Kasir POS</li>
        </ol>
    </nav>
@endsection

@section('content')
<div x-data="posCart()" class="flex flex-col lg:flex-row gap-4 h-full" x-cloak>
    {{-- Left Panel: Product Search & Grid --}}
    <div class="w-full lg:w-3/5 flex flex-col">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 flex flex-col flex-1">
            <div class="mb-4">
                <div class="relative">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" x-model="searchQuery" x-on:input.debounce.300ms="searchProducts()" x-on:keydown.enter.prevent="handleBarcodeScan()" placeholder="Cari produk berdasarkan nama, SKU, atau barcode..." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm" autofocus>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto">
                <div x-show="searchQuery && searchResults.length === 0" class="text-center py-8 text-gray-500">
                    <i class="fa-solid fa-search text-3xl mb-2 block"></i>
                    <p>Produk tidak ditemukan</p>
                </div>
                <div x-show="!searchQuery" class="text-center py-8 text-gray-500">
                    <i class="fa-solid fa-search text-3xl mb-2 block"></i>
                    <p>Ketik untuk mencari produk</p>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    <template x-for="product in searchResults" :key="product.id">
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 p-3 hover:border-primary-400 dark:hover:border-primary-500 transition-colors cursor-pointer" x-on:click="addToCart(product)">
                            <div class="flex items-center gap-2 mb-2">
                                <img :src="product.image || 'data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 40 40%22><rect fill=%22%23e5e7eb%22 width=%2240%22 height=%2240%22/><text x=%2250%25%22 y=%2255%25%22 text-anchor=%22middle%22 fill=%22%239ca3af%22 font-size=%2212%22>No Img</text></svg>'" class="w-12 h-12 rounded object-cover flex-shrink-0 bg-gray-100 dark:bg-gray-600">
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate" x-text="product.name"></div>
                                    <div class="flex items-center gap-1 mt-0.5">
                                        <span class="text-xs text-gray-500 dark:text-gray-400" x-text="product.barcode || product.sku"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-primary-600 dark:text-primary-400" x-text="'Rp ' + formatNumber(product.sell_price_inc_tax || product.sell_price || 0)"></span>
                                <span class="px-2 py-1 text-xs font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-md transition-colors">
                                    <i class="fa-solid fa-plus mr-1"></i> Tambah
                                </span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Panel: Cart & Payment --}}
    <div class="w-full lg:w-2/5">
        <form method="POST" action="{{ route('pos.store') }}" x-on:submit="prepareForm($event)" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 flex flex-col" style="max-height: calc(100vh - 8rem);">
            @csrf

            <div class="flex-shrink-0 mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pelanggan</label>
                <select name="contact_id" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm py-2 px-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">-- Umum / Walk-in --</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->full_name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Cart Items --}}
            <div class="flex-1 overflow-y-auto mb-4">
                <template x-if="cart.length === 0">
                    <div class="text-center py-8 text-gray-400">
                        <i class="fa-solid fa-cart-shopping text-3xl mb-2 block"></i>
                        <p class="text-sm">Keranjang kosong</p>
                    </div>
                </template>
                <template x-if="cart.length > 0">
                    <table class="w-full text-sm">
                        <thead class="text-xs text-gray-500 dark:text-gray-400 uppercase border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="text-left pb-2">Produk</th>
                                <th class="text-center pb-2 w-14">Qty</th>
                                <th class="text-right pb-2 w-20">Harga</th>
                                <th class="text-right pb-2 w-24">Subtotal</th>
                                <th class="text-center pb-2 w-8"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in cart" :key="item.variation_id">
                                <tr class="border-b border-gray-100 dark:border-gray-700/50">
                                    <td class="py-2 pr-1">
                                        <div class="text-gray-800 dark:text-gray-200 font-medium text-xs truncate max-w-[120px]" x-text="item.name"></div>
                                    </td>
                                    <td class="py-2">
                                        <input type="number" x-model.number="item.qty" x-on:input="updateCart()" min="1" class="w-14 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-xs py-1 focus:ring-1 focus:ring-primary-500">
                                    </td>
                                    <td class="py-2 text-right text-xs text-gray-600 dark:text-gray-400" x-text="'Rp ' + formatNumber(item.price)"></td>
                                    <td class="py-2 text-right text-xs font-semibold text-gray-800 dark:text-gray-200" x-text="'Rp ' + formatNumber(item.price * item.qty)"></td>
                                    <td class="py-2 text-center">
                                        <button type="button" x-on:click="removeFromCart(index)" class="text-red-500 hover:text-red-700 text-xs">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </template>
            </div>

            {{-- Totals --}}
            <div class="flex-shrink-0 border-t border-gray-200 dark:border-gray-700 pt-3 space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200" x-text="'Rp ' + formatNumber(subtotal)"></span>
                </div>

                {{-- Discount --}}
                <div class="flex items-center gap-2">
                    <select x-model="discountType" class="border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-xs py-1.5 px-2 focus:ring-1 focus:ring-primary-500">
                        <option value="fixed">Rp</option>
                        <option value="percentage">%</option>
                    </select>
                    <input type="number" x-model.number="discountAmount" x-on:input="updateCart()" min="0" class="flex-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-xs py-1.5 px-2 focus:ring-1 focus:ring-primary-500" placeholder="Diskon">
                    <span class="text-gray-600 dark:text-gray-400 text-xs whitespace-nowrap" x-text="'-Rp ' + formatNumber(discountValue)"></span>
                </div>

                {{-- Tax --}}
                <div class="flex items-center gap-2">
                    <select name="tax_id" x-model="selectedTaxId" x-on:change="updateCart()" class="flex-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-xs py-1.5 px-2 focus:ring-1 focus:ring-primary-500">
                        <option value="">Tanpa Pajak</option>
                        @foreach($taxRates as $tax)
                            <option value="{{ $tax->id }}">{{ $tax->name }} ({{ $tax->amount }}%)</option>
                        @endforeach
                    </select>
                    <span class="text-gray-600 dark:text-gray-400 text-xs whitespace-nowrap" x-text="'Rp ' + formatNumber(taxAmount)"></span>
                </div>

                <div class="flex justify-between text-base font-bold border-t border-gray-200 dark:border-gray-600 pt-2">
                    <span class="text-gray-800 dark:text-gray-100">Total</span>
                    <span class="text-primary-600 dark:text-primary-400" x-text="'Rp ' + formatNumber(grandTotal)"></span>
                </div>

                {{-- Payment --}}
                <div class="border-t border-gray-200 dark:border-gray-600 pt-3 space-y-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Metode Pembayaran</label>
                    <select name="payment_method" x-model="paymentMethod" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm py-2 px-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                        <option value="cash">Tunai</option>
                        <option value="card">Kartu</option>
                        <option value="bank_transfer">Transfer Bank</option>
                        <option value="other">Lainnya</option>
                    </select>

                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Jumlah Dibayar</label>
                    <input type="number" x-model.number="paymentAmount" x-on:input="updateCart()" name="payment_amount" min="0" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm py-2 px-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>

                    <div class="flex justify-between text-sm" x-show="paymentAmount > 0">
                        <span class="text-gray-600 dark:text-gray-400">Kembalian</span>
                        <span class="font-bold text-success-600" x-text="'Rp ' + formatNumber(Math.max(0, paymentAmount - grandTotal))"></span>
                    </div>
                </div>
            </div>

            <template x-for="(item, idx) in cartItems" :key="idx">
                <div>
                    <input type="hidden" :name="'items[' + idx + '][product_id]'" :value="item.product_id">
                    <input type="hidden" :name="'items[' + idx + '][variation_id]'" :value="item.variation_id">
                    <input type="hidden" :name="'items[' + idx + '][quantity]'" :value="item.qty">
                    <input type="hidden" :name="'items[' + idx + '][unit_price]'" :value="item.price">
                </div>
            </template>
            <input type="hidden" name="discount_type" :value="discountType">
            <input type="hidden" name="discount_amount" :value="discountAmount || 0">

            <button type="submit" x-bind:disabled="cart.length === 0" class="mt-4 w-full py-3 px-4 text-white font-semibold rounded-lg transition-colors" x-bind:class="cart.length === 0 ? 'bg-gray-400 cursor-not-allowed' : 'bg-success-600 hover:bg-success-700'">
                <i class="fa-solid fa-check mr-2"></i> Proses Pembayaran
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const taxRates = @json($taxRates->map(fn($t) => ['id' => $t->id, 'name' => $t->name, 'amount' => $t->amount, 'is_tax_group' => $t->is_tax_group, 'sub_taxes' => $t->subTaxes->map(fn($s) => ['id' => $s->id, 'name' => $s->name, 'amount' => $s->amount])]));

    function posCart() {
        return {
            searchQuery: '',
            searchResults: [],
            cart: [],
            discountType: 'fixed',
            discountAmount: 0,
            selectedTaxId: '',
            paymentMethod: 'cash',
            paymentAmount: 0,

            get cartItems() {
                return this.cart.map(item => ({
                    product_id: item.product_id,
                    variation_id: item.variation_id,
                    qty: item.qty,
                    price: item.price,
                }));
            },

            get subtotal() {
                return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            },

            get discountValue() {
                if (!this.discountAmount) return 0;
                if (this.discountType === 'percentage') {
                    return this.subtotal * (this.discountAmount / 100);
                }
                return this.discountAmount;
            },

            get taxAmount() {
                if (!this.selectedTaxId) return 0;
                const tax = taxRates.find(t => t.id == this.selectedTaxId);
                if (!tax) return 0;
                const base = this.subtotal - this.discountValue;
                let totalTax = base * (tax.amount / 100);
                if (tax.is_tax_group && tax.sub_taxes) {
                    totalTax += tax.sub_taxes.reduce((sum, st) => sum + base * (st.amount / 100), 0);
                }
                return totalTax;
            },

            get grandTotal() {
                return this.subtotal - this.discountValue + this.taxAmount;
            },

            async searchProducts() {
                if (!this.searchQuery || this.searchQuery.length < 2) {
                    this.searchResults = [];
                    return;
                }
                try {
                    const response = await fetch('/api/products/search?q=' + encodeURIComponent(this.searchQuery));
                    const data = await response.json();
                    this.searchResults = data.data || data || [];
                } catch (e) {
                    console.error('Search error:', e);
                    this.searchResults = [];
                }
            },

            handleBarcodeScan() {
                if (this.searchResults.length === 1) {
                    this.addToCart(this.searchResults[0]);
                    this.searchQuery = '';
                    this.searchResults = [];
                } else if (this.searchResults.length > 1) {
                    this.addToCart(this.searchResults[0]);
                    this.searchQuery = '';
                }
            },

            addToCart(product) {
                const variationId = product.variation_id || product.default_variation?.id || product.variations?.[0]?.id || product.id;
                const existing = this.cart.find(item => item.variation_id == variationId);
                if (existing) {
                    existing.qty += 1;
                } else {
                    this.cart.push({
                        product_id: product.id,
                        variation_id: variationId,
                        name: product.name,
                        image: product.image,
                        price: parseFloat(product.sell_price_inc_tax || product.sell_price || product.default_sell_price || product.default_variation?.default_sell_price || 0),
                        tax_id: product.tax_id,
                        qty: 1,
                    });
                }
                if (product.tax_id && !this.selectedTaxId) {
                    this.selectedTaxId = product.tax_id;
                }
            },

            removeFromCart(index) {
                this.cart.splice(index, 1);
            },

            updateCart() {
                // Triggers reactive recalculation
            },

            prepareForm(event) {
                if (this.cart.length === 0) {
                    event.preventDefault();
                    alert('Keranjang masih kosong.');
                }
            },

            formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(parseFloat(num) || 0);
            },
        };
    }
</script>
@endpush
