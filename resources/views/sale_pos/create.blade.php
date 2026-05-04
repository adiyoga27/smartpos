@extends('layouts.pos')

@section('title', 'Kasir POS')

@section('header-controls')
<div x-data="{ locId: {{ $selectedLocationId ?? 0 }} }" class="flex items-center gap-2">
    <i class="fa-solid fa-location-dot text-primary-500 text-xs"></i>
    <select x-model="locId" @change="fetch('{{ route('pos.set-location') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }, body: JSON.stringify({ location_id: locId }) }).then(r => r.json()).then(() => window.location.reload())" class="text-xs border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 py-1 px-2 focus:ring-1 focus:ring-primary-500 outline-none">
        @foreach($locations as $loc)
        <option value="{{ $loc->id }}">{{ $loc->name }}</option>
        @endforeach
    </select>
</div>
@endsection

@section('content')
<div x-data="posCart()" class="flex flex-col lg:flex-row lg:h-full" x-cloak>
    {{-- Left: Products --}}
    <div class="w-full lg:flex-1 flex flex-col lg:border-r border-gray-200 dark:border-gray-700 lg:h-full">
        <div class="bg-white dark:bg-gray-800 p-2 lg:p-3 flex flex-col flex-1 overflow-hidden max-h-[45vh] lg:max-h-none">
            <div class="mb-2 flex-shrink-0">
                <input type="text" x-model="searchQuery" x-on:input.debounce.200ms="searchProducts()" placeholder="Cari produk..." class="w-full pl-8 pr-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-xs focus:ring-1 focus:ring-primary-500 outline-none" autofocus>
                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            </div>
            <div class="flex-1 overflow-y-auto -mx-1 px-1">
                <div x-show="!searchQuery">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                        <template x-for="p in defaultProducts" :key="'d-'+p.id">
                            <div x-bind:class="(p.enable_stock && p.qty_available <= 0) ? 'bg-gray-100 dark:bg-gray-700/30 opacity-60 cursor-not-allowed' : 'bg-gray-50 dark:bg-gray-700/50 hover:border-primary-400 cursor-pointer'" class="rounded-lg border border-gray-200 dark:border-gray-600 p-2.5 transition-colors" x-on:click="addToCart(p)">
                                <div class="flex items-center gap-2 mb-1.5">
                                    <img :src="p.image || 'data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 40 40%22><rect fill=%22%23e5e7eb%22 width=%2240%22 height=%2240%22/><text x=%2250%25%22 y=%2255%25%22 text-anchor=%22middle%22 fill=%22%239ca3af%22 font-size=%2212%22>No Img</text></svg>'" class="w-10 h-10 rounded object-cover flex-shrink-0 bg-gray-100 dark:bg-gray-600">
                                    <div class="min-w-0">
                                        <div class="text-xs font-semibold text-gray-800 dark:text-gray-100 truncate" x-text="p.name"></div>
                                        <div class="text-[10px] text-gray-400 mt-0.5" x-text="p.barcode || p.sku"></div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-primary-600 dark:text-primary-400" x-text="'Rp'+formatNumber(p.sell_price_inc_tax||p.sell_price)"></span>
                                    <span class="px-1.5 py-0.5 text-[10px] font-medium text-white bg-primary-600 rounded">Tambah</span>
                                </div>
                                <div x-show="p.enable_stock" class="mt-1.5">
                                    <span class="text-[10px] px-1.5 py-0.5 rounded-full font-medium" x-bind:class="stockClass(p)">
                                        <span x-text="'Stok '+formatNumber(p.qty_available)"></span>
                                    </span>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div x-show="hasMorePages" class="text-center py-2">
                        <button x-on:click="loadMore()" x-bind:disabled="loadingMore" class="text-xs text-primary-600 hover:underline" x-text="loadingMore ? '...' : 'Lebih banyak'"></button>
                    </div>
                </div>
                <div x-show="searchQuery" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                    <template x-for="p in searchResults" :key="'s-'+p.id">
                        <div x-bind:class="(p.enable_stock && p.qty_available <= 0) ? 'bg-gray-100 dark:bg-gray-700/30 opacity-60 cursor-not-allowed' : 'bg-gray-50 dark:bg-gray-700/50 hover:border-primary-400 cursor-pointer'" class="rounded-lg border border-gray-200 dark:border-gray-600 p-2.5 transition-colors" x-on:click="addToCart(p)">
                            <div class="flex items-center gap-2 mb-1.5">
                                <img :src="p.image || 'data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 40 40%22><rect fill=%22%23e5e7eb%22 width=%2240%22 height=%2240%22/><text x=%2250%25%22 y=%2255%25%22 text-anchor=%22middle%22 fill=%22%239ca3af%22 font-size=%2212%22>No Img</text></svg>'" class="w-10 h-10 rounded object-cover flex-shrink-0 bg-gray-100 dark:bg-gray-600">
                                <div class="min-w-0">
                                    <div class="text-xs font-semibold text-gray-800 dark:text-gray-100 truncate" x-text="p.name"></div>
                                    <div class="text-[10px] text-gray-400 mt-0.5" x-text="p.barcode || p.sku"></div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-primary-600 dark:text-primary-400" x-text="'Rp'+formatNumber(p.sell_price_inc_tax||p.sell_price)"></span>
                                <span class="px-1.5 py-0.5 text-[10px] font-medium text-white bg-primary-600 rounded">Tambah</span>
                            </div>
                            <div x-show="p.enable_stock" class="mt-1.5">
                                <span class="text-[10px] px-1.5 py-0.5 rounded-full font-medium" x-bind:class="stockClass(p)">
                                    <span x-text="'Stok '+formatNumber(p.qty_available)"></span>
                                </span>
                            </div>
                        </div>
                    </template>
                </div>
                <div x-show="searchQuery && searchResults.length === 0 && !searching" class="text-center py-4 text-gray-400 text-xs">Produk tidak ditemukan</div>
            </div>
        </div>
    </div>

    {{-- Right: Cart --}}
    <div class="w-full lg:w-[44%] lg:min-w-[360px] lg:max-w-[480px] flex flex-col bg-white dark:bg-gray-800 border-t lg:border-t-0 border-gray-200 dark:border-gray-700 lg:h-full overflow-hidden">
        <div x-show="!showInvoice" class="flex flex-col flex-1 overflow-hidden p-2 lg:p-3 lg:overflow-y-auto">
            <div x-show="checkoutError" class="flex-shrink-0 mb-2 p-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-xs rounded flex items-start gap-2">
                <i class="fa-solid fa-circle-exclamation mt-0.5 flex-shrink-0"></i><span x-text="checkoutError" class="flex-1"></span>
                <button type="button" x-on:click="checkoutError = ''" class="text-red-400 hover:text-red-600"><i class="fa-solid fa-xmark"></i></button>
            </div>

            {{-- Customer --}}
            <div class="flex-shrink-0 mb-2">
                <div x-show="!contactId" class="flex gap-1">
                    <div class="relative flex-1">
                        <input type="text" x-model="customerSearch" @input.debounce.200="searchCustomers()" @keydown.escape="customerSearch = ''; customerResults = [];" placeholder="Pelanggan..." class="w-full pl-7 pr-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-xs focus:ring-1 focus:ring-primary-500">
                        <i class="fa-solid fa-search absolute left-2 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]"></i>
                        <div x-show="customerResults.length > 0" @click.outside="customerResults = []" class="absolute z-10 w-full mt-0.5 max-h-40 overflow-y-auto bg-white dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600 shadow-lg">
                            <template x-for="c in customerResults" :key="c.id">
                                <div @click="selectCustomer(c)" class="px-2 py-1 cursor-pointer hover:bg-primary-50 dark:hover:bg-primary-900/20 text-xs border-b border-gray-100 dark:border-gray-600 last:border-0"><span x-text="c.full_name"></span> <span class="text-gray-400" x-text="c.mobile||''"></span></div>
                            </template>
                        </div>
                    </div>
                    <button type="button" @click="showCustomerModal = true" class="px-2 py-1 bg-primary-600 text-white text-xs rounded hover:bg-primary-700 flex-shrink-0"><i class="fa-solid fa-user-plus"></i></button>
                </div>
                <div x-show="contactId" class="flex items-center gap-1 p-1.5 bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded text-xs">
                    <i class="fa-solid fa-user-check text-primary-600 text-[10px]"></i>
                    <span class="font-medium text-primary-700 dark:text-primary-300 flex-1 truncate" x-text="selectedCustomerName"></span>
                    <button type="button" @click="clearCustomer()" class="text-gray-400 hover:text-red-500"><i class="fa-solid fa-xmark text-[10px]"></i></button>
                </div>
            </div>

            {{-- Cart Items --}}
            <div class="flex-1 overflow-y-auto mb-2">
                <template x-if="cart.length === 0">
                    <div class="text-center py-6 text-gray-400 text-xs">Keranjang kosong</div>
                </template>
                <template x-if="cart.length > 0">
                    <table class="w-full text-xs">
                        <thead class="text-gray-500 uppercase border-b border-gray-200 dark:border-gray-700"><tr><th class="text-left pb-1">Item</th><th class="text-center pb-1 w-10">Qty</th><th class="text-right pb-1 w-16">Total</th><th class="w-4"></th></tr></thead>
                        <tbody>
                            <template x-for="(item, i) in cart" :key="item.variation_id">
                                <tr class="border-b border-gray-100 dark:border-gray-700/50">
                                    <td class="py-1"><div class="font-medium text-gray-800 dark:text-gray-200 truncate max-w-[140px]" x-text="item.name"></div><div class="text-[10px] text-gray-400" x-text="'Rp'+formatNumber(item.price)+'/pcs'"></div></td>
                                    <td class="py-1"><input type="number" x-model.number="item.qty" x-on:input="updateCart()" min="1" class="w-10 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-xs py-0.5 focus:ring-1 focus:ring-primary-500"></td>
                                    <td class="py-1 text-right font-semibold text-gray-800 dark:text-gray-200" x-text="'Rp'+formatNumber(item.price*item.qty)"></td>
                                    <td class="py-1 text-center"><button type="button" x-on:click="removeFromCart(i)" class="text-red-400 hover:text-red-600"><i class="fa-solid fa-xmark text-[10px]"></i></button></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </template>
            </div>

            {{-- Footer: Totals + Payment --}}
            <div class="flex-shrink-0 border-t border-gray-200 dark:border-gray-700 pt-2 space-y-1.5 text-xs">
                <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span class="font-semibold" x-text="'Rp'+formatNumber(subtotal)"></span></div>
                <div class="flex items-center gap-1">
                    <span class="text-gray-500">Diskon</span>
                    <select x-model="discountType" class="border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-xs py-0.5 px-1"><option value="fixed">Rp</option><option value="percentage">%</option></select>
                    <input type="number" x-model.number="discountAmount" x-on:input="updateCart()" min="0" class="w-16 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-xs py-0.5 px-1" placeholder="0">
                    <span class="text-red-500 ml-auto" x-show="discountAmount>0" x-text="'-Rp'+formatNumber(discountValue)"></span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="text-gray-500">Pajak</span>
                    <select x-model="selectedTaxId" class="flex-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-xs py-0.5 px-1">
                        <option value="">-</option>
                        @foreach($taxRates as $tax)<option value="{{ $tax->id }}">{{ $tax->name }}</option>@endforeach
                    </select>
                    <span x-show="selectedTaxId" x-text="'Rp'+formatNumber(taxAmount)"></span>
                </div>

                {{-- Shipping toggle --}}
                <div class="flex items-center gap-2">
                    <button type="button" @click="showShipping = !showShipping" class="text-xs font-medium text-primary-600 hover:text-primary-700 flex items-center gap-1">
                        <i class="fa-solid fa-truck-fast text-[10px]"></i> <span x-text="showShipping ? 'Sembunyikan' : 'Barang Dikirim'"></span>
                    </button>
                    <span x-show="shippingCharge>0" class="text-gray-500 ml-auto" x-text="'Ongkir Rp'+formatNumber(shippingCharge)"></span>
                </div>
                <div x-show="showShipping" x-transition class="space-y-1 pl-1">
                    <textarea x-model="shippingDetails" rows="1" placeholder="Alamat pengiriman..." class="w-full border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-xs py-1 px-2 focus:ring-1 focus:ring-primary-500 resize-none"></textarea>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-500 text-[10px]">Ongkir</span>
                        <input type="number" x-model.number="shippingCharge" min="0" class="w-24 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-xs py-0.5 px-2 focus:ring-1 focus:ring-primary-500" placeholder="0">
                    </div>
                </div>

                <div class="flex justify-between text-sm font-bold border-t border-gray-200 dark:border-gray-600 pt-1"><span>Total</span><span class="text-primary-600" x-text="'Rp'+formatNumber(grandTotal)"></span></div>

                {{-- Multi Payment --}}
                <template x-for="(p, i) in payments" :key="i">
                    <div class="flex items-center gap-1 bg-gray-50 dark:bg-gray-700/50 rounded p-1">
                        <span class="text-[10px] text-gray-500" x-text="i+1"></span>
                        <select x-model="p.method" class="border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-[10px] py-0.5 px-1"><option value="cash">Tunai</option><option value="card">Kartu</option><option value="bank_transfer">Transfer</option></select>
                        <input type="text" x-model="p.note" x-show="p.method!=='cash'" class="w-20 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-[10px] py-0.5 px-1" placeholder="Ref">
                        <input type="number" x-model.number="p.amount" x-on:input="updateCart()" min="0" class="flex-1 w-20 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-xs py-0.5 px-1" placeholder="0">
                        <button type="button" x-on:click="removePayment(i)" x-show="payments.length>1" class="text-red-400 hover:text-red-600"><i class="fa-solid fa-xmark text-[10px]"></i></button>
                    </div>
                </template>
                <button type="button" @click="addPayment()" class="text-[10px] text-primary-600 hover:text-primary-700">+ Tambah Bayar</button>

                <div class="flex justify-between" x-show="payments.length>0"><span class="text-gray-500">Dibayar</span><span x-bind:class="totalPaid>=grandTotal?'text-success-600':'text-danger-600'" x-text="'Rp'+formatNumber(totalPaid)"></span></div>
                <div class="flex justify-between" x-show="totalPaid>grandTotal"><span class="text-gray-500">Kembali</span><span class="font-bold text-success-600" x-text="'Rp'+formatNumber(totalPaid-grandTotal)"></span></div>
            </div>

            <button type="button" x-on:click="processCheckout()" x-bind:disabled="cart.length===0||submitting" class="mt-2 w-full py-2 px-4 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-1 text-sm" x-bind:class="cart.length===0?'bg-gray-400 cursor-not-allowed':'bg-success-600 hover:bg-success-700'">
                <i x-show="!submitting" class="fa-solid fa-check"></i><i x-show="submitting" class="fa-solid fa-spinner fa-spin"></i> <span x-text="submitting?'Proses...':'Proses Pembayaran'"></span>
            </button>
        </div>

        {{-- Invoice --}}
        <div x-show="showInvoice" x-transition class="flex flex-col h-full overflow-y-auto p-3">
            <div class="flex items-center justify-between mb-3 flex-shrink-0">
                <div><div class="text-lg font-bold text-success-600"><i class="fa-solid fa-circle-check mr-1"></i>Berhasil</div><div class="text-xs text-gray-500" x-text="invoiceData.invoice_no"></div></div>
                <button type="button" x-on:click="newTransaction()" class="px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded">Baru</button>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-2 mb-3 text-xs space-y-1">
                <div class="flex justify-between"><span class="text-gray-500">Tanggal</span><span x-text="invoiceData.transaction_date"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Pelanggan</span><span x-text="invoiceData.contact?.full_name||'Umum'"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Status</span><span class="text-success-600 font-medium" x-text="invoiceData.payment_status==='paid'?'Lunas':'Belum Lunas'"></span></div>
            </div>
            <div class="mb-3 overflow-y-auto flex-1 text-xs">
                <table class="w-full"><thead class="text-gray-500 border-b border-gray-200 dark:border-gray-700"><tr><th class="text-left pb-1">Item</th><th class="text-center w-8 pb-1">Qty</th><th class="text-right w-16 pb-1">Total</th></tr></thead>
                <tbody><template x-for="(item,idx) in invoiceData.items" :key="idx"><tr class="border-b border-gray-100 dark:border-gray-700/50"><td class="py-1" x-text="item.product_name"></td><td class="py-1 text-center" x-text="item.quantity"></td><td class="py-1 text-right font-semibold" x-text="'Rp'+formatNumber(item.line_total)"></td></tr></template></tbody></table>
            </div>
            <div class="flex-shrink-0 border-t border-gray-200 dark:border-gray-700 pt-2 space-y-1 text-xs">
                <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span x-text="'Rp'+formatNumber(invoiceData.total_before_tax+(invoiceData.discount_amount||0))"></span></div>
                <template x-if="invoiceData.discount_amount>0"><div class="flex justify-between"><span class="text-gray-500">Diskon</span><span class="text-red-500" x-text="'-Rp'+formatNumber(invoiceData.discount_amount)"></span></div></template>
                <template x-if="invoiceData.tax_amount>0"><div class="flex justify-between"><span class="text-gray-500">Pajak</span><span x-text="'Rp'+formatNumber(invoiceData.tax_amount)"></span></div></template>
                <div class="flex justify-between text-sm font-bold border-t border-gray-200 dark:border-gray-600 pt-1"><span>Total</span><span class="text-primary-600" x-text="'Rp'+formatNumber(invoiceData.final_total)"></span></div>
            </div>
            <div class="flex-shrink-0 space-y-1.5 mt-2">
                <button type="button" x-on:click="printThermal()" class="w-full py-2 px-4 text-white text-xs rounded flex items-center justify-center gap-1 bg-gray-700 hover:bg-gray-800">Cetak Thermal</button>
                <button type="button" x-on:click="printA4()" class="w-full py-2 px-4 text-white text-xs rounded flex items-center justify-center gap-1 bg-primary-600 hover:bg-primary-700">Cetak A4</button>
            </div>
        </div>
    </div>

    {{-- Customer Modal --}}
    <div x-show="showCustomerModal" class="fixed inset-0 z-50 overflow-y-auto" x-transition x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900/50" @click="showCustomerModal = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm p-4">
                <div class="flex items-center justify-between mb-3"><h3 class="text-sm font-bold text-gray-800 dark:text-gray-100">Pelanggan Baru</h3><button @click="showCustomerModal = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button></div>
                <div class="space-y-2">
                    <input type="text" x-model="newCustomer.name" placeholder="Nama *" class="w-full rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-xs px-2 py-1.5 focus:ring-1 focus:ring-primary-500">
                    <input type="text" x-model="newCustomer.mobile" placeholder="No. HP *" class="w-full rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-xs px-2 py-1.5 focus:ring-1 focus:ring-primary-500">
                    <input type="text" x-model="newCustomer.code" placeholder="Kode (opsional)" class="w-full rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-xs px-2 py-1.5 focus:ring-1 focus:ring-primary-500">
                    <div x-show="newCustomerError" class="text-xs text-red-600" x-text="newCustomerError"></div>
                </div>
                <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" @click="showCustomerModal = false" class="px-3 py-1.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs rounded hover:bg-gray-300">Batal</button>
                    <button type="button" @click="saveCustomer()" x-bind:disabled="customerSaving" class="px-4 py-1.5 bg-primary-600 text-white text-xs font-medium rounded hover:bg-primary-700 disabled:opacity-50"><span x-show="!customerSaving">Simpan</span><span x-show="customerSaving">...</span></button>
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
    const initialLocationId = {{ $selectedLocationId ?? 0 }};

    function posCart() {
        return {
            searchQuery: '', searchResults: [], searching: false,
            defaultProducts: [...initialProducts], currentPage: initialNextPage, hasMorePages: initialHasMore, loadingMore: false,
            cart: [], selectedLocationId: initialLocationId,
            contactId: '', customerSearch: '', customerResults: [], selectedCustomerName: '',
            showCustomerModal: false, newCustomer: { name: '', mobile: '', email: '', code: '' }, newCustomerError: '', customerSaving: false,
            discountType: 'fixed', discountAmount: 0, selectedTaxId: '',
            payments: [{ method: 'cash', amount: 0, note: '' }],
            showShipping: false, shippingDetails: '', shippingCharge: 0,
            submitting: false, showInvoice: false, invoiceData: {}, changeAmount: 0, checkoutError: '',

            get cartItems() { return this.cart.map(i => ({ product_id: i.product_id, variation_id: i.variation_id, qty: i.qty, price: i.price })); },
            get subtotal() { return this.cart.reduce((s, i) => s + (i.price * i.qty), 0); },
            get discountValue() { if (!this.discountAmount) return 0; return this.discountType === 'percentage' ? this.subtotal * (this.discountAmount / 100) : this.discountAmount; },
            get taxAmount() {
                if (!this.selectedTaxId) return 0; const t = taxRates.find(x => x.id == this.selectedTaxId); if (!t) return 0;
                const b = this.subtotal - this.discountValue; let tx = b * (t.amount / 100);
                if (t.is_tax_group && t.sub_taxes) tx += t.sub_taxes.reduce((s, st) => s + b * (st.amount / 100), 0);
                return tx;
            },
            get grandTotal() { return this.subtotal - this.discountValue + this.taxAmount + (this.shippingCharge || 0); },
            get totalPaid() { return this.payments.reduce((s, p) => s + (parseFloat(p.amount) || 0), 0); },

            addPayment() { this.payments.push({ method: 'cash', amount: 0, note: '' }); },
            removePayment(i) { if (this.payments.length > 1) this.payments.splice(i, 1); },
            async searchProducts() {
                if (!this.searchQuery || this.searchQuery.length < 2) { this.searchResults = []; return; }
                this.searching = true;
                try { const r = await fetch('/api/products/search?q=' + encodeURIComponent(this.searchQuery) + '&page=1&location_id=' + this.selectedLocationId); const d = await r.json(); this.searchResults = d.data ?? d ?? []; } catch (e) { this.searchResults = []; }
                this.searching = false;
            },
            handleBarcodeScan() { if (this.searchResults.length >= 1) { this.addToCart(this.searchResults[0]); this.searchQuery = ''; this.searchResults = []; } },
            addToCart(p) {
                const vid = p.variation_id || p.default_variation?.id || p.variations?.[0]?.id || p.id;
                const ex = this.cart.find(i => i.variation_id == vid);
                if (p.enable_stock && ((ex ? ex.qty : 0) + 1) > p.qty_available) { this.checkoutError = 'Stok "' + p.name + '" tidak cukup.'; return; }
                if (ex) { ex.qty += 1; } else { this.cart.push({ product_id: p.id, variation_id: vid, name: p.name, price: parseFloat(p.sell_price_inc_tax || p.sell_price || 0), tax_id: p.tax_id, qty: 1 }); }
                if (p.tax_id && !this.selectedTaxId) this.selectedTaxId = p.tax_id;
            },
            removeFromCart(i) { this.cart.splice(i, 1); }, updateCart() {},
            applyQuickDiscount(t, a) { this.discountType = t; this.discountAmount = (this.discountType === t && this.discountAmount === a) ? 0 : a; },

            async processCheckout() {
                if (this.cart.length === 0) return; this.checkoutError = '';
                const csrf = document.querySelector('meta[name=csrf-token]').content;
                const body = new URLSearchParams();
                body.append('contact_id', this.contactId || ''); body.append('location_id', this.selectedLocationId || '');
                body.append('discount_type', this.discountType); body.append('discount_amount', this.discountAmount || 0); body.append('tax_id', this.selectedTaxId || '');
                body.append('payment_method', this.payments[0]?.method || 'cash'); body.append('payment_amount', this.totalPaid || 0);
                if (this.shippingDetails) body.append('shipping_details', this.shippingDetails);
                if (this.shippingCharge > 0) body.append('shipping_charges', this.shippingCharge);
                this.payments.forEach((p, i) => { body.append(`payments[${i}][method]`, p.method); body.append(`payments[${i}][amount]`, p.amount || 0); if (p.note) body.append(`payments[${i}][note]`, p.note); });
                this.cartItems.forEach((item, i) => { body.append(`items[${i}][product_id]`, item.product_id); body.append(`items[${i}][variation_id]`, item.variation_id); body.append(`items[${i}][quantity]`, item.qty); body.append(`items[${i}][unit_price]`, item.price); });
                this.submitting = true;
                try { const r = await fetch('{{ route('pos.store') }}', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf }, body }); const d = await r.json(); if (d.success) { this.invoiceData = d.transaction; this.changeAmount = d.change || 0; this.showInvoice = true; } else { this.checkoutError = d.message || 'Gagal.'; } } catch (e) { this.checkoutError = 'Error jaringan.'; }
                this.submitting = false;
            },

            newTransaction() {
                this.cart = []; this.contactId = ''; this.selectedCustomerName = ''; this.customerSearch = '';
                this.discountType = 'fixed'; this.discountAmount = 0; this.selectedTaxId = '';
                this.payments = [{ method: 'cash', amount: 0, note: '' }];
                this.showShipping = false; this.shippingDetails = ''; this.shippingCharge = 0;
                this.showInvoice = false; this.invoiceData = {}; this.changeAmount = 0; this.checkoutError = '';
                this.searchQuery = ''; this.searchResults = [];
            },

            async searchCustomers() { if (!this.customerSearch || this.customerSearch.length < 2) { this.customerResults = []; return; } try { const r = await fetch('{{ route('customers.search') }}?q=' + encodeURIComponent(this.customerSearch)); this.customerResults = await r.json(); } catch (e) { this.customerResults = []; } },
            selectCustomer(c) { this.contactId = c.id; this.selectedCustomerName = c.full_name; this.customerSearch = c.full_name; this.customerResults = []; },
            clearCustomer() { this.contactId = ''; this.selectedCustomerName = ''; this.customerSearch = ''; },

            async saveCustomer() {
                if (!this.newCustomer.name.trim()) { this.newCustomerError = 'Nama harus diisi.'; return; }
                this.newCustomerError = ''; this.customerSaving = true;
                try {
                    const body = new URLSearchParams(); body.append('type', 'customer'); body.append('first_name', this.newCustomer.name); body.append('mobile', this.newCustomer.mobile); if (this.newCustomer.code) body.append('contact_id', this.newCustomer.code);
                    const r = await fetch('{{ route('contacts.store') }}', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }, body }); const d = await r.json();
                    if (d.success) { this.contactId = d.contact.id; this.selectedCustomerName = d.contact.full_name; this.showCustomerModal = false; this.newCustomer = { name: '', mobile: '', email: '', code: '' }; } else { this.newCustomerError = d.message || 'Gagal.'; }
                } catch (e) { this.newCustomerError = 'Error jaringan.'; }
                this.customerSaving = false;
            },

            stockClass(p) { if (!p.enable_stock) return ''; if (p.qty_available <= 0) return 'bg-red-100 text-red-700'; if (p.qty_available <= 5) return 'bg-yellow-100 text-yellow-700'; return 'bg-green-100 text-green-700'; },
            printThermal() { window.open(printThermalUrl.replace('__ID__', this.invoiceData.id), '_blank', 'width=320,height=600'); },
            printA4() { window.open(printA4Url.replace('__ID__', this.invoiceData.id), '_blank', 'width=800,height=600'); },
            formatNumber(n) { return new Intl.NumberFormat('id-ID').format(parseFloat(n) || 0); },

            async loadMore() { if (this.loadingMore || !this.hasMorePages) return; this.loadingMore = true; try { const r = await fetch('/api/products/search?q=&page=' + this.currentPage); const j = await r.json(); this.defaultProducts = [...this.defaultProducts, ...(j.data || [])]; this.currentPage = j.current_page + 1; this.hasMorePages = j.current_page < j.last_page; } catch (e) {} this.loadingMore = false; },
        };
    }
</script>
@endpush
