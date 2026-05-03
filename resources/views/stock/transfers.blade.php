@extends('layouts.app')

@section('title', 'Stock Transfers')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('dashboard') }}" class="hover:text-primary-600">Dashboard</a></li>
            <li class="flex items-center"><i class="fa-solid fa-chevron-right text-xs mx-2"></i> Stock Transfers</li>
        </ol>
    </nav>
@endsection

@section('content')
<div x-data="transferList()">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Stock Transfers</h2>
        <button @click="openAddModal()" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700">
            <i class="fa-solid fa-plus mr-2"></i> New Transfer
        </button>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Date</th>
                        <th class="px-4 py-3 text-left font-medium">From Location</th>
                        <th class="px-4 py-3 text-left font-medium">To Location</th>
                        <th class="px-4 py-3 text-left font-medium">Notes</th>
                        <th class="px-4 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($transfers ?? [] as $transfer)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $transfer->date }}</td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $transfer->fromLocation->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $transfer->toLocation->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 max-w-[200px] truncate">{{ $transfer->notes }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('stock.transfers.show', $transfer->id ?? 0) }}" class="text-primary-600 hover:text-primary-800"><i class="fa-solid fa-eye"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No stock transfers found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            {{ ($transfers ?? collect())->links() }}
        </div>
    </div>

    {{-- Add New Transfer Modal --}}
    <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900/50" @click="showAddModal = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">New Stock Transfer</h3>
                    <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <form action="{{ route('stock.transfers.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                                <input type="date" name="date" x-model="form.date" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From Location</label>
                                <select name="from_location_id" x-model="form.from_location_id" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">Select Location</option>
                                    @foreach($locations ?? [] as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To Location</label>
                                <select name="to_location_id" x-model="form.to_location_id" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">Select Location</option>
                                    @foreach($locations ?? [] as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                            <textarea name="notes" x-model="form.notes" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                        </div>

                        {{-- Items Section --}}
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3">Items</h4>
                            <div class="flex gap-2 mb-3">
                                <div class="flex-1">
                                    <input type="text" x-model="itemSearch" @input.debounce.300="searchProducts()" placeholder="Search product..." class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                <div class="w-32">
                                    <input type="number" x-model="itemQty" placeholder="Qty" min="1" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                <button type="button" @click="addItem()" class="px-4 py-2 bg-success-500 text-white text-sm rounded-lg hover:bg-success-600">Add</button>
                            </div>
                            <div x-show="searchResults.length > 0" class="mb-3 max-h-40 overflow-y-auto bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                <template x-for="product in searchResults" :key="product.id">
                                    <div @click="selectProduct(product)" class="px-3 py-2 cursor-pointer hover:bg-primary-50 dark:hover:bg-primary-900/20 text-sm text-gray-700 dark:text-gray-300 border-b border-gray-100 dark:border-gray-600 last:border-0">
                                        <span x-text="product.name"></span>
                                        <span class="text-gray-400 text-xs ml-2" x-text="'(' + product.sku + ')'"></span>
                                    </div>
                                </template>
                            </div>
                            <table class="w-full text-sm" x-show="items.length > 0">
                                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium">Product</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium w-24">Quantity</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium w-12"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr>
                                            <td class="px-3 py-2 text-gray-700 dark:text-gray-300 text-sm">
                                                <span x-text="item.product_name"></span>
                                                <input type="hidden" :name="'items[' + index + '][product_id]'" :value="item.product_id">
                                            </td>
                                            <td class="px-3 py-2 text-right">
                                                <input type="number" :name="'items[' + index + '][quantity]'" x-model="item.quantity" min="1" class="w-20 text-right rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-2 py-1 focus:ring-primary-500 focus:border-primary-500">
                                            </td>
                                            <td class="px-3 py-2 text-right">
                                                <button type="button" @click="items.splice(index, 1)" class="text-red-500 hover:text-red-700"><i class="fa-solid fa-xmark"></i></button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                            <p x-show="items.length === 0" class="text-sm text-gray-400 dark:text-gray-500 text-center py-3">No items added yet.</p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">Cancel</button>
                        <button type="submit" class="px-6 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700">Save Transfer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function transferList() {
    return {
        showAddModal: false,
        form: { date: '{{ date('Y-m-d') }}', from_location_id: '', to_location_id: '', notes: '' },
        itemSearch: '',
        itemQty: '',
        items: [],
        searchResults: [],
        openAddModal() {
            this.showAddModal = true;
            this.items = [];
            this.itemSearch = '';
            this.itemQty = '';
        },
        async searchProducts() {
            if (this.itemSearch.length < 2) { this.searchResults = []; return; }
            const resp = await fetch('{{ route('product.search') }}?q=' + encodeURIComponent(this.itemSearch));
            this.searchResults = await resp.json();
        },
        selectProduct(product) {
            this.itemSearch = product.name;
            this.searchResults = [];
        },
        addItem() {
            if (!this.itemSearch || !this.itemQty) return;
            this.items.push({
                product_id: this.searchResults.length > 0 ? this.searchResults[0].id : null,
                product_name: this.itemSearch,
                quantity: this.itemQty
            });
            this.itemSearch = '';
            this.itemQty = '';
            this.searchResults = [];
        }
    }
}
</script>
@endpush
