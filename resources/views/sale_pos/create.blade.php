@extends('layouts.pos')

@section('title', 'Kasir POS')

@section('content')
<div x-data="posCart()" class="flex flex-col lg:flex-row gap-0 h-full" x-cloak>
    {{-- Left Panel: Product Search & Grid --}}
    <div class="w-full lg:w-3/5 flex flex-col border-r border-gray-200 dark:border-gray-700">
        <div class="bg-white dark:bg-gray-800 p-4 flex flex-col flex-1 overflow-hidden">
            <div class="mb-4 flex-shrink-0">
                <div class="relative">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" x-model="searchQuery" x-on:input.debounce.300ms="searchProducts()" x-on:keydown.enter.prevent="handleBarcodeScan()" placeholder="Cari produk berdasarkan nama, SKU, atau barcode..." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm" autofocus>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto">
                <div x-show="searchQuery && searchResults.length === 0 && !searching" class="text-center py-8 text-gray-500">
                    <i class="fa-solid fa-search text-3xl mb-2 block"></i>
                    <p>Produk tidak ditemukan</p>
                </div>

                {{-- Default Grid (no search) --}}
                <div x-show="!searchQuery">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        <template x-for="product in defaultProducts" :key="'def-' + product.id">
                            <div x-bind:class="(product.enable_stock && product.qty_available <= 0) ? 'bg-gray-100 dark:bg-gray-700/30 opacity-60 cursor-not-allowed' : 'bg-gray-50 dark:bg-gray-700/50 hover:border-primary-400 dark:hover:border-primary-500 cursor-pointer'" class="rounded-lg border border-gray-200 dark:border-gray-600 p-3 transition-colors" x-on:click="addToCart(product)">
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
                                <div x-show="product.enable_stock" class="mt-2">
                                    <span class="text-xs px-2 py-0.5 rounded-full font-medium" x-bind:class="stockClass(product)">
                                        <i x-show="product.qty_available <= 0" class="fa-solid fa-circle-xmark mr-1"></i>
                                        <i x-show="product.qty_available > 0 && product.qty_available <= 5" class="fa-solid fa-triangle-exclamation mr-1"></i>
                                        <i x-show="product.qty_available > 5" class="fa-solid fa-boxes-stacked mr-1"></i>
                                        Stok: <span x-text="formatNumber(product.qty_available)"></span>
                                    </span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div x-show="hasMorePages" class="text-center py-4">
                        <button x-on:click="loadMore()" x-bind:disabled="loadingMore" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 disabled:bg-gray-400 text-white text-sm font-medium rounded-lg transition-colors">
                            <i class="fa-solid fa-chevron-down mr-1"></i>
                            <span x-text="loadingMore ? 'Memuat...' : 'Tampilkan Lebih Banyak'"></span>
                        </button>
                    </div>
                </div>

                {{-- Search Results Grid --}}
                <div x-show="searchQuery" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    <template x-for="product in searchResults" :key="'sr-' + product.id">
                        <div x-bind:class="(product.enable_stock && product.qty_available <= 0) ? 'bg-gray-100 dark:bg-gray-700/30 opacity-60 cursor-not-allowed' : 'bg-gray-50 dark:bg-gray-700/50 hover:border-primary-400 dark:hover:border-primary-500 cursor-pointer'" class="rounded-lg border border-gray-200 dark:border-gray-600 p-3 transition-colors" x-on:click="addToCart(product)">
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
                            <div x-show="product.enable_stock" class="mt-2">
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium" x-bind:class="stockClass(product)">
                                    <i x-show="product.qty_available <= 0" class="fa-solid fa-circle-xmark mr-1"></i>
                                    <i x-show="product.qty_available > 0 && product.qty_available <= 5" class="fa-solid fa-triangle-exclamation mr-1"></i>
                                    <i x-show="product.qty_available > 5" class="fa-solid fa-boxes-stacked mr-1"></i>
                                    Stok: <span x-text="formatNumber(product.qty_available)"></span>
                                </span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Panel: Cart / Invoice Preview --}}
    <div class="w-full lg:w-2/5 flex flex-col bg-white dark:bg-gray-800">
        {{-- Cart View --}}
        <div x-show="!showInvoice" class="flex flex-col h-full p-4">
            <div x-show="checkoutError" class="flex-shrink-0 mb-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-lg flex items-start gap-2">
                <i class="fa-solid fa-circle-exclamation mt-0.5 flex-shrink-0"></i>
                <span x-text="checkoutError" class="flex-1"></span>
                <button type="button" x-on:click="checkoutError = ''" class="text-red-400 hover:text-red-600 flex-shrink-0">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="flex-shrink-0 mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pelanggan</label>
                <input type="hidden" name="contact_id" x-model="contactId">

                {{-- No customer selected: search input --}}
                <div x-show="!contactId" class="flex gap-2">
                    <div class="relative flex-1">
                        <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" x-model="customerSearch" @input.debounce.200="searchCustomers()" @keydown.escape="customerSearch = ''; customerResults = [];" placeholder="Cari nama, kode member, atau HP..." class="w-full pl-9 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <div x-show="customerResults.length > 0" @click.outside="customerResults = []" class="absolute z-10 w-full mt-1 max-h-48 overflow-y-auto bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 shadow-lg">
                            <template x-for="c in customerResults" :key="c.id">
                                <div @click="selectCustomer(c)" class="px-3 py-2 cursor-pointer hover:bg-primary-50 dark:hover:bg-primary-900/20 text-sm border-b border-gray-100 dark:border-gray-600 last:border-0">
                                    <div class="font-medium text-gray-800 dark:text-gray-200" x-text="c.full_name"></div>
                                    <div class="text-xs text-gray-400">
                                        <span x-show="c.contact_id"><span x-text="c.contact_id"></span> &middot; </span>
                                        <span x-text="c.mobile || ''"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <button type="button" @click="showCustomerModal = true" class="px-3 py-2 bg-primary-600 text-white text-sm rounded-lg hover:bg-primary-700 flex-shrink-0" title="Tambah Pelanggan Baru">
                        <i class="fa-solid fa-user-plus"></i>
                    </button>
                </div>

                {{-- Customer selected: compact badge --}}
                <div x-show="contactId" class="flex items-center gap-2">
                    <div class="flex-1 flex items-center gap-2 px-3 py-2 bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-lg">
                        <i class="fa-solid fa-user-check text-primary-600 dark:text-primary-400"></i>
                        <span class="text-sm font-medium text-primary-700 dark:text-primary-300 flex-1" x-text="selectedCustomerName"></span>
                        <button type="button" @click="clearCustomer()" class="text-gray-400 hover:text-red-500 transition-colors" title="Hapus pelanggan">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <button type="button" @click="contactId = ''; customerSearch = ''; selectedCustomerName = '';" class="px-3 py-2 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 flex-shrink-0">
                        Ganti
                    </button>
                </div>
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
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-2 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Diskon</span>
                        <div class="flex gap-1">
                            <button type="button" @click="applyQuickDiscount('percentage', 5)" class="px-1.5 py-0.5 text-xs rounded border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-white dark:hover:bg-gray-600">5%</button>
                            <button type="button" @click="applyQuickDiscount('percentage', 10)" class="px-1.5 py-0.5 text-xs rounded border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-white dark:hover:bg-gray-600">10%</button>
                            <button type="button" @click="applyQuickDiscount('fixed', 5000)" class="px-1.5 py-0.5 text-xs rounded border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-white dark:hover:bg-gray-600">5rb</button>
                            <button type="button" @click="applyQuickDiscount('fixed', 10000)" class="px-1.5 py-0.5 text-xs rounded border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-white dark:hover:bg-gray-600">10rb</button>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <select x-model="discountType" class="border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-xs py-1.5 px-2 focus:ring-1 focus:ring-primary-500">
                            <option value="fixed">Rp</option>
                            <option value="percentage">%</option>
                        </select>
                        <input type="number" x-model.number="discountAmount" x-on:input="updateCart()" min="0" class="flex-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-xs py-1.5 px-2 focus:ring-1 focus:ring-primary-500" placeholder="Jumlah diskon">
                    </div>
                    <div x-show="discountAmount > 0" class="flex justify-between text-xs">
                        <span class="text-gray-500 dark:text-gray-400" x-text="discountType === 'percentage' ? 'Diskon ' + discountAmount + '%' : 'Diskon Rp'"></span>
                        <span class="font-semibold text-red-600 dark:text-red-400" x-text="'-Rp ' + formatNumber(discountValue)"></span>
                    </div>
                </div>

                {{-- Tax --}}
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-2">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Pajak</span>
                        <select x-model="selectedTaxId" x-on:change="updateCart()" class="flex-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-xs py-1.5 px-2 focus:ring-1 focus:ring-primary-500">
                            <option value="">Tanpa Pajak</option>
                            @foreach($taxRates as $tax)
                                <option value="{{ $tax->id }}">{{ $tax->name }} ({{ $tax->amount }}%)</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="selectedTaxId" class="flex justify-between text-xs mt-1">
                        <span class="text-gray-500 dark:text-gray-400">Pajak</span>
                        <span class="font-semibold text-gray-700 dark:text-gray-300" x-text="'Rp ' + formatNumber(taxAmount)"></span>
                    </div>
                </div>

                <div class="flex justify-between text-base font-bold border-t border-gray-200 dark:border-gray-600 pt-2">
                    <span class="text-gray-800 dark:text-gray-100">Total</span>
                    <span class="text-primary-600 dark:text-primary-400 text-lg" x-text="'Rp ' + formatNumber(grandTotal)"></span>
                </div>

                {{-- Payment --}}
                <div class="pt-2 space-y-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Metode Pembayaran</label>
                    <div class="grid grid-cols-3 gap-1">
                        <button type="button" @click="paymentMethod = 'cash'" class="py-1.5 text-xs font-medium rounded-lg transition-colors" x-bind:class="paymentMethod === 'cash' ? 'bg-success-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600'">Tunai</button>
                        <button type="button" @click="paymentMethod = 'card'" class="py-1.5 text-xs font-medium rounded-lg transition-colors" x-bind:class="paymentMethod === 'card' ? 'bg-success-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600'">Kartu</button>
                        <button type="button" @click="paymentMethod = 'bank_transfer'" class="py-1.5 text-xs font-medium rounded-lg transition-colors" x-bind:class="paymentMethod === 'bank_transfer' ? 'bg-success-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600'">Transfer</button>
                    </div>
                    <input type="hidden" name="payment_method" x-model="paymentMethod">

                    <div x-show="paymentMethod === 'card' || paymentMethod === 'bank_transfer'" class="space-y-1">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">
                            <span x-show="paymentMethod === 'card'">No. Kartu / Referensi</span>
                            <span x-show="paymentMethod === 'bank_transfer'">No. Referensi Transfer</span>
                        </label>
                        <input type="text" x-model="paymentNote" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-xs py-1.5 px-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500" :placeholder="paymentMethod === 'card' ? '4 digit terakhir kartu...' : 'No. ref / bukti transfer...'">
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Dibayar</span>
                        <input type="number" x-model.number="paymentAmount" x-on:input="updateCart()" name="payment_amount" min="0" class="flex-1 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm py-2 px-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="0">
                        <button type="button" @click="paymentAmount = grandTotal" class="px-2 py-2 text-xs font-medium rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-green-100 dark:hover:bg-green-900/30 hover:text-green-700 transition-colors whitespace-nowrap">Uang Pas</button>
                    </div>

                    <div class="flex justify-between text-sm" x-show="paymentAmount > 0">
                        <span class="text-gray-600 dark:text-gray-400">Kembalian</span>
                        <span class="font-bold text-success-600" x-text="'Rp ' + formatNumber(Math.max(0, paymentAmount - grandTotal))"></span>
                    </div>
                </div>
            </div>

            <button type="button" x-on:click="processCheckout()" x-bind:disabled="cart.length === 0 || submitting" class="mt-4 w-full py-3 px-4 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2" x-bind:class="cart.length === 0 ? 'bg-gray-400 cursor-not-allowed' : 'bg-success-600 hover:bg-success-700'">
                <i x-show="!submitting" class="fa-solid fa-check"></i>
                <i x-show="submitting" class="fa-solid fa-spinner fa-spin"></i>
                <span x-text="submitting ? 'Memproses...' : 'Proses Pembayaran'"></span>
            </button>
        </div>

        {{-- Invoice Preview --}}
        <div x-show="showInvoice" x-transition class="flex flex-col h-full overflow-y-auto p-4">
            <div class="flex items-center justify-between mb-4 flex-shrink-0">
                <div>
                    <div class="text-lg font-bold text-success-600">
                        <i class="fa-solid fa-circle-check mr-2"></i> Transaksi Berhasil
                    </div>
                    <div class="text-sm text-gray-500" x-text="invoiceData.invoice_no"></div>
                </div>
                <button type="button" x-on:click="newTransaction()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fa-solid fa-plus mr-1"></i> Transaksi Baru
                </button>
            </div>

            {{-- Invoice Info --}}
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 mb-4 text-sm space-y-1">
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Tanggal</span>
                    <span class="font-medium" x-text="invoiceData.transaction_date"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Pelanggan</span>
                    <span class="font-medium" x-text="invoiceData.contact?.full_name || 'Umum'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Status</span>
                    <span class="font-medium text-success-600" x-text="invoiceData.payment_status === 'paid' ? 'Lunas' : 'Belum Lunas'"></span>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="mb-4 overflow-y-auto flex-1">
                <table class="w-full text-sm">
                    <thead class="text-xs text-gray-500 dark:text-gray-400 uppercase border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="text-left pb-2">Item</th>
                            <th class="text-center pb-2 w-12">Qty</th>
                            <th class="text-right pb-2 w-20">Harga</th>
                            <th class="text-right pb-2 w-20">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, idx) in invoiceData.items" :key="idx">
                            <tr class="border-b border-gray-100 dark:border-gray-700/50">
                                <td class="py-1.5">
                                    <div class="text-gray-800 dark:text-gray-200 font-medium text-xs" x-text="item.product_name"></div>
                                </td>
                                <td class="py-1.5 text-center text-xs" x-text="item.quantity"></td>
                                <td class="py-1.5 text-right text-xs" x-text="'Rp ' + formatNumber(item.unit_price)"></td>
                                <td class="py-1.5 text-right text-xs font-semibold" x-text="'Rp ' + formatNumber(item.line_total)"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            {{-- Invoice Totals --}}
            <div class="flex-shrink-0 border-t border-gray-200 dark:border-gray-700 pt-3 space-y-1 text-sm mb-4">
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Subtotal</span>
                    <span x-text="'Rp ' + formatNumber(invoiceData.total_before_tax + (invoiceData.discount_amount || 0))"></span>
                </div>
                <template x-if="invoiceData.discount_amount > 0">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Diskon</span>
                        <span class="text-red-500" x-text="'-Rp ' + formatNumber(invoiceData.discount_amount)"></span>
                    </div>
                </template>
                <template x-if="invoiceData.tax_amount > 0">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Pajak</span>
                        <span x-text="'Rp ' + formatNumber(invoiceData.tax_amount)"></span>
                    </div>
                </template>
                <div class="flex justify-between text-base font-bold border-t border-gray-200 dark:border-gray-600 pt-2">
                    <span>Total</span>
                    <span class="text-primary-600 dark:text-primary-400" x-text="'Rp ' + formatNumber(invoiceData.final_total)"></span>
                </div>
            </div>

            {{-- Payment Info --}}
            <div class="flex-shrink-0 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3 mb-4 text-sm">
                <div class="font-medium text-green-700 dark:text-green-400 mb-1">Pembayaran</div>
                <template x-for="(p, idx) in invoiceData.payments" :key="idx">
                    <div class="flex justify-between text-xs py-0.5">
                        <span x-text="p.method_label"></span>
                        <span x-text="'Rp ' + formatNumber(p.amount)"></span>
                    </div>
                </template>
                <template x-if="changeAmount > 0">
                    <div class="flex justify-between text-xs pt-1 border-t border-green-200 dark:border-green-800 mt-1 font-semibold text-green-700 dark:text-green-400">
                        <span>Kembalian</span>
                        <span x-text="'Rp ' + formatNumber(changeAmount)"></span>
                    </div>
                </template>
            </div>

            {{-- Print Buttons --}}
            <div class="flex-shrink-0 space-y-2">
                <button type="button" x-on:click="printThermal()" class="w-full py-2.5 px-4 text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2 bg-gray-700 hover:bg-gray-800 text-sm">
                    <i class="fa-solid fa-print"></i> Cetak Thermal (Struk)
                </button>
                <button type="button" x-on:click="printA4()" class="w-full py-2.5 px-4 text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2 bg-primary-600 hover:bg-primary-700 text-sm">
                    <i class="fa-solid fa-file-invoice"></i> Cetak A4 (Invoice)
                </button>
            </div>
        </div>
    </div>

    {{-- Quick Add Customer Modal --}}
    <div x-show="showCustomerModal" class="fixed inset-0 z-50 overflow-y-auto" x-transition x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900/50" @click="showCustomerModal = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Tambah Pelanggan Baru</h3>
                    <button @click="showCustomerModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama <span class="text-red-500">*</span></label>
                        <input type="text" x-model="newCustomer.name" @keydown.enter="saveCustomer()" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Nama pelanggan">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">No. HP <span class="text-red-500">*</span></label>
                            <input type="text" x-model="newCustomer.mobile" @keydown.enter="saveCustomer()" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="08xxx">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kode Member</label>
                            <input type="text" x-model="newCustomer.code" @keydown.enter="saveCustomer()" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="M-001">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email <span class="text-gray-400">(opsional)</span></label>
                        <input type="email" x-model="newCustomer.email" @keydown.enter="saveCustomer()" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="email@example.com">
                    </div>
                    <div x-show="newCustomerError" class="text-sm text-red-600 dark:text-red-400" x-text="newCustomerError"></div>
                </div>
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" @click="showCustomerModal = false" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">Batal</button>
                    <button type="button" @click="saveCustomer()" x-bind:disabled="customerSaving" class="px-6 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 disabled:opacity-50">
                        <span x-show="!customerSaving">Simpan</span>
                        <span x-show="customerSaving"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const taxRates = @json($taxRatesJson);
    const initialProducts = @json($productsJson);
    const initialHasMore = {{ $products->hasMorePages() ? 'true' : 'false' }};
    const initialNextPage = {{ $products->currentPage() + 1 }};
    const printThermalUrl = '{{ route('pos.print.thermal', ['transaction' => '__ID__']) }}';
    const printA4Url = '{{ route('pos.print.a4', ['transaction' => '__ID__']) }}';

    function posCart() {
        return {
            searchQuery: '',
            searchResults: [],
            searching: false,
            defaultProducts: [...initialProducts],
            currentPage: initialNextPage,
            hasMorePages: initialHasMore,
            loadingMore: false,
            cart: [],
            contactId: '',
            customerSearch: '',
            customerResults: [],
            selectedCustomerName: '',
            showCustomerModal: false,
            newCustomer: { name: '', mobile: '', email: '', code: '' },
            newCustomerError: '',
            customerSaving: false,
            discountType: 'fixed',
            discountAmount: 0,
            selectedTaxId: '',
            paymentMethod: 'cash',
            paymentAmount: 0,
            paymentNote: '',
            submitting: false,
            showInvoice: false,
            invoiceData: {},
            changeAmount: 0,
            checkoutError: '',

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
                this.searching = true;
                try {
                    const response = await fetch('/api/products/search?q=' + encodeURIComponent(this.searchQuery) + '&page=1');
                    const data = await response.json();
                    this.searchResults = data.data ?? data ?? [];
                } catch (e) {
                    console.error('Search error:', e);
                    this.searchResults = [];
                }
                this.searching = false;
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
                const currentQty = existing ? existing.qty : 0;

                if (product.enable_stock && (currentQty + 1) > product.qty_available) {
                    this.checkoutError = 'Stok tidak mencukupi untuk "' + product.name + '". Tersedia: ' + this.formatNumber(product.qty_available);
                    return;
                }

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
            },

            applyQuickDiscount(type, amount) {
                this.discountType = type;
                if (type === 'percentage' && this.discountAmount === amount) {
                    this.discountAmount = 0;
                } else if (type === 'fixed' && this.discountAmount === amount) {
                    this.discountAmount = 0;
                } else {
                    this.discountAmount = amount;
                }
            },

            async processCheckout() {
                if (this.cart.length === 0) return;

                this.checkoutError = '';

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const body = new URLSearchParams();
                body.append('contact_id', this.contactId || '');
                body.append('discount_type', this.discountType);
                body.append('discount_amount', this.discountAmount || 0);
                body.append('tax_id', this.selectedTaxId || '');
                body.append('payment_method', this.paymentMethod);
                body.append('payment_amount', this.paymentAmount || 0);
                if (this.paymentNote) body.append('payment_note', this.paymentNote);
                this.cartItems.forEach((item, idx) => {
                    body.append(`items[${idx}][product_id]`, item.product_id);
                    body.append(`items[${idx}][variation_id]`, item.variation_id);
                    body.append(`items[${idx}][quantity]`, item.qty);
                    body.append(`items[${idx}][unit_price]`, item.price);
                });

                this.submitting = true;
                try {
                    const response = await fetch('{{ route('pos.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: body,
                    });

                    const data = await response.json();
                    if (data.success) {
                        this.invoiceData = data.transaction;
                        this.changeAmount = data.change || 0;
                        this.showInvoice = true;
                    } else {
                        this.checkoutError = data.message || 'Gagal memproses transaksi.';
                    }
                } catch (e) {
                    console.error('Checkout error:', e);
                    this.checkoutError = 'Terjadi kesalahan saat memproses transaksi.';
                }
                this.submitting = false;
            },

            newTransaction() {
                this.cart = [];
                this.contactId = '';
                this.customerSearch = '';
                this.customerResults = [];
                this.selectedCustomerName = '';
                this.discountType = 'fixed';
                this.discountAmount = 0;
                this.selectedTaxId = '';
                this.paymentMethod = 'cash';
                this.paymentAmount = 0;
                this.paymentNote = '';
                this.showInvoice = false;
                this.invoiceData = {};
                this.changeAmount = 0;
                this.checkoutError = '';
                this.searchQuery = '';
                this.searchResults = [];
            },

            async searchCustomers() {
                if (!this.customerSearch || this.customerSearch.length < 2) {
                    this.customerResults = [];
                    return;
                }
                try {
                    const resp = await fetch('{{ route('customers.search') }}?q=' + encodeURIComponent(this.customerSearch));
                    this.customerResults = await resp.json();
                } catch (e) {
                    this.customerResults = [];
                }
            },

            selectCustomer(c) {
                this.contactId = c.id;
                this.selectedCustomerName = c.full_name + (c.contact_id ? ' (' + c.contact_id + ')' : '');
                this.customerSearch = c.full_name;
                this.customerResults = [];
            },

            clearCustomer() {
                this.contactId = '';
                this.selectedCustomerName = '';
                this.customerSearch = '';
                this.customerResults = [];
            },

            async saveCustomer() {
                const name = this.newCustomer.name.trim();
                const mobile = this.newCustomer.mobile.trim();
                if (!name) {
                    this.newCustomerError = 'Nama pelanggan harus diisi.';
                    return;
                }
                if (!mobile) {
                    this.newCustomerError = 'No. HP harus diisi.';
                    return;
                }

                this.newCustomerError = '';
                this.customerSaving = true;
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const body = new URLSearchParams();
                    body.append('type', 'customer');
                    body.append('first_name', name);
                    body.append('mobile', mobile);
                    if (this.newCustomer.email) body.append('email', this.newCustomer.email);
                    if (this.newCustomer.code) body.append('contact_id', this.newCustomer.code);

                    const resp = await fetch('{{ route('contacts.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: body,
                    });

                    const data = await resp.json();
                    if (data.success) {
                        this.contactId = data.contact.id;
                        this.selectedCustomerName = data.contact.full_name + (data.contact.contact_id ? ' (' + data.contact.contact_id + ')' : '');
                        this.customerSearch = data.contact.full_name;
                        this.showCustomerModal = false;
                        this.newCustomer = { name: '', mobile: '', email: '', code: '' };
                    } else {
                        this.newCustomerError = data.message || 'Gagal menyimpan pelanggan.';
                    }
                } catch (e) {
                    console.error('Save customer error:', e);
                    this.newCustomerError = 'Terjadi kesalahan.';
                }
                this.customerSaving = false;
            },

            stockClass(product) {
                if (!product.enable_stock) return '';
                if (product.qty_available <= 0) return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
                if (product.qty_available <= 5) return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400';
                return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
            },

            printThermal() {
                const url = printThermalUrl.replace('__ID__', this.invoiceData.id);
                window.open(url, '_blank', 'width=320,height=600');
            },

            printA4() {
                const url = printA4Url.replace('__ID__', this.invoiceData.id);
                window.open(url, '_blank', 'width=800,height=600');
            },

            formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(parseFloat(num) || 0);
            },

            async loadMore() {
                if (this.loadingMore || !this.hasMorePages) return;
                this.loadingMore = true;
                try {
                    const response = await fetch('/api/products/search?q=&page=' + this.currentPage);
                    const json = await response.json();
                    const results = json.data || [];
                    this.defaultProducts = [...this.defaultProducts, ...results];
                    this.currentPage = json.current_page + 1;
                    this.hasMorePages = json.current_page < json.last_page;
                } catch (e) {
                    console.error('Load more error:', e);
                }
                this.loadingMore = false;
            },
        };
    }
</script>
@endpush
