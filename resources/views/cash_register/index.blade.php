@extends('layouts.app')

@section('title', 'Cash Register')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('dashboard') }}" class="hover:text-primary-600">Dashboard</a></li>
            <li class="flex items-center"><i class="fa-solid fa-chevron-right text-xs mx-2"></i> Cash Register</li>
        </ol>
    </nav>
@endsection

@section('content')
<div x-data="cashRegister()">
    {{-- Open Register Status --}}
    @if(isset($register) && $register)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Register Open</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Opened at: {{ $register->opened_at }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Opening Amount: <span class="font-semibold text-gray-700 dark:text-gray-300">Rp {{ number_format($register->opening_amount, 0, ',', '.') }}</span></p>
            </div>
            <div class="flex items-center gap-4">
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg px-4 py-3 text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Cash Sales Today</p>
                    <p class="text-lg font-bold text-green-600 dark:text-green-400">Rp {{ number_format($cashSales ?? 0, 0, ',', '.') }}</p>
                </div>
                <button @click="showCloseModal = true" class="px-4 py-2 bg-danger-600 text-white text-sm font-medium rounded-lg hover:bg-danger-700">
                    <i class="fa-solid fa-door-closed mr-2"></i> Close Register
                </button>
            </div>
        </div>
    </div>
    @else
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Open Register</h3>
        <form action="{{ route('cash-register.open') }}" method="POST" class="space-y-4 max-w-md">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location</label>
                <select name="location_id" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Select Location</option>
                    @foreach($locations ?? [] as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Opening Amount (IDR)</label>
                <input type="number" name="opening_amount" step="0.01" min="0" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <button type="submit" class="px-6 py-2 bg-success-600 text-white text-sm font-medium rounded-lg hover:bg-success-700">
                <i class="fa-solid fa-door-open mr-2"></i> Open Register
            </button>
        </form>
    </div>
    @endif

    {{-- History --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <h3 class="px-6 py-4 text-lg font-bold text-gray-800 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700">Register History</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Date</th>
                        <th class="px-4 py-3 text-left font-medium">User</th>
                        <th class="px-4 py-3 text-right font-medium">Opening Amount</th>
                        <th class="px-4 py-3 text-right font-medium">Closing Amount</th>
                        <th class="px-4 py-3 text-center font-medium">Status</th>
                        <th class="px-4 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($registers ?? [] as $reg)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $reg->opened_at }}</td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $reg->user->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">Rp {{ number_format($reg->opening_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">{{ $reg->closing_amount ? 'Rp ' . number_format($reg->closing_amount, 0, ',', '.') : '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ ($reg->status ?? '') === 'closed' ? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' }}">
                                {{ ucfirst($reg->status ?? 'open') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('cash-register.show', $reg->id ?? 0) }}" class="text-primary-600 hover:text-primary-800"><i class="fa-solid fa-eye"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No register history found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            {{ ($registers ?? collect())->links() }}
        </div>
    </div>

    {{-- Close Register Modal --}}
    <div x-show="showCloseModal" class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900/50" @click="showCloseModal = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Close Register</h3>
                    <button @click="showCloseModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <form action="{{ route('cash-register.close', $register->id ?? 0) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Closing Amount (IDR)</label>
                            <input type="number" name="closing_amount" x-model="closeForm.closing_amount" step="0.01" min="0" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>

                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3">Denomination Breakdown</h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">100,000</label>
                                    <input type="number" name="denom_100000" x-model.number="closeForm.denom_100000" min="0" @input="calcTotal()" class="w-full rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-2 py-1.5 focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">50,000</label>
                                    <input type="number" name="denom_50000" x-model.number="closeForm.denom_50000" min="0" @input="calcTotal()" class="w-full rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-2 py-1.5 focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">20,000</label>
                                    <input type="number" name="denom_20000" x-model.number="closeForm.denom_20000" min="0" @input="calcTotal()" class="w-full rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-2 py-1.5 focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">10,000</label>
                                    <input type="number" name="denom_10000" x-model.number="closeForm.denom_10000" min="0" @input="calcTotal()" class="w-full rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-2 py-1.5 focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">5,000</label>
                                    <input type="number" name="denom_5000" x-model.number="closeForm.denom_5000" min="0" @input="calcTotal()" class="w-full rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-2 py-1.5 focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">2,000</label>
                                    <input type="number" name="denom_2000" x-model.number="closeForm.denom_2000" min="0" @input="calcTotal()" class="w-full rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-2 py-1.5 focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">1,000</label>
                                    <input type="number" name="denom_1000" x-model.number="closeForm.denom_1000" min="0" @input="calcTotal()" class="w-full rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-2 py-1.5 focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">500</label>
                                    <input type="number" name="denom_500" x-model.number="closeForm.denom_500" min="0" @input="calcTotal()" class="w-full rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-2 py-1.5 focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">100</label>
                                    <input type="number" name="denom_100" x-model.number="closeForm.denom_100" min="0" @input="calcTotal()" class="w-full rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-2 py-1.5 focus:ring-primary-500 focus:border-primary-500">
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600 text-right">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Total from denominations: </span>
                                <span class="text-lg font-bold text-gray-800 dark:text-gray-200" x-text="'Rp ' + formatNumber(denomTotal)"></span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Closing Note</label>
                            <textarea name="closing_note" rows="2" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" @click="showCloseModal = false" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">Cancel</button>
                        <button type="submit" class="px-6 py-2 bg-danger-600 text-white text-sm font-medium rounded-lg hover:bg-danger-700">Close Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function cashRegister() {
    return {
        showCloseModal: false,
        denomTotal: 0,
        closeForm: {
            closing_amount: '',
            denom_100000: 0, denom_50000: 0, denom_20000: 0, denom_10000: 0,
            denom_5000: 0, denom_2000: 0, denom_1000: 0, denom_500: 0, denom_100: 0
        },
        calcTotal() {
            this.denomTotal =
                this.closeForm.denom_100000 * 100000 +
                this.closeForm.denom_50000 * 50000 +
                this.closeForm.denom_20000 * 20000 +
                this.closeForm.denom_10000 * 10000 +
                this.closeForm.denom_5000 * 5000 +
                this.closeForm.denom_2000 * 2000 +
                this.closeForm.denom_1000 * 1000 +
                this.closeForm.denom_500 * 500 +
                this.closeForm.denom_100 * 100;
        },
        formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }
    }
}
</script>
@endpush
