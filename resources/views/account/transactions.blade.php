@extends('layouts.app')

@section('title', 'Account Transactions')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('dashboard') }}" class="hover:text-primary-600">Dashboard</a></li>
            <li class="flex items-center"><i class="fa-solid fa-chevron-right text-xs mx-2"></i> Account Transactions</li>
        </ol>
    </nav>
@endsection

@section('content')
<div x-data="transactionList()">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Account Transactions</h2>
        <button @click="openAddModal()" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700">
            <i class="fa-solid fa-plus mr-2"></i> Add Transaction
        </button>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <div class="p-4 flex items-end gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Account</label>
                <select x-model="filters.account_id" @change="applyFilter()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All Accounts</option>
                    @foreach($accounts ?? [] as $account)
                        <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->account_number }})</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Date</th>
                        <th class="px-4 py-3 text-left font-medium">Account</th>
                        <th class="px-4 py-3 text-center font-medium">Type</th>
                        <th class="px-4 py-3 text-right font-medium">Amount</th>
                        <th class="px-4 py-3 text-left font-medium">Notes</th>
                        <th class="px-4 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($transactions ?? [] as $tx)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $tx->date }}</td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $tx->account->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ ($tx->type ?? '') === 'debit' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400' }}">
                                {{ ucfirst($tx->type ?? 'debit') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-medium text-gray-800 dark:text-gray-200">Rp {{ number_format($tx->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 max-w-[200px] truncate">{{ $tx->notes }}</td>
                        <td class="px-4 py-3 text-right">
                            <button @click="editTransaction({{ $tx }})" class="text-primary-600 hover:text-primary-800 mr-3"><i class="fa-solid fa-pen-to-square"></i></button>
                            <form action="{{ route('account.transactions.destroy', $tx->id ?? 0) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Delete this transaction?')"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No transactions found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            {{ ($transactions ?? collect())->links() }}
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900/50" @click="showModal = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100" x-text="editingId ? 'Edit Transaction' : 'Add Transaction'"></h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <form :action="editingId ? '{{ route('account.transactions.update', '') }}/' + editingId : '{{ route('account.transactions.store') }}'" method="POST">
                    @csrf
                    <input type="hidden" name="_method" x-bind:value="editingId ? 'PUT' : 'POST'">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                            <input type="date" name="date" x-model="form.date" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Account</label>
                            <select name="account_id" x-model="form.account_id" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Select Account</option>
                                @foreach($accounts ?? [] as $account)
                                    <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->account_number }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                            <select name="type" x-model="form.type" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="debit">Debit</option>
                                <option value="credit">Credit</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount (IDR)</label>
                            <input type="number" name="amount" x-model="form.amount" step="0.01" min="0" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                            <textarea name="notes" x-model="form.notes" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">Cancel</button>
                        <button type="submit" class="px-6 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function transactionList() {
    return {
        showModal: false,
        editingId: null,
        filters: { account_id: '' },
        form: { date: '{{ date('Y-m-d') }}', account_id: '', type: 'debit', amount: '', notes: '' },
        openAddModal() {
            this.editingId = null;
            this.form = { date: '{{ date('Y-m-d') }}', account_id: '', type: 'debit', amount: '', notes: '' };
            this.showModal = true;
        },
        editTransaction(tx) {
            this.editingId = tx.id;
            this.form.date = tx.date;
            this.form.account_id = tx.account_id;
            this.form.type = tx.type;
            this.form.amount = tx.amount;
            this.form.notes = tx.notes;
            this.showModal = true;
        },
        applyFilter() {
            const params = new URLSearchParams(this.filters);
            window.location.href = '{{ route('account.transactions.index') }}?' + params.toString();
        }
    }
}
</script>
@endpush
