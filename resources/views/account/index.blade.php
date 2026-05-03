@extends('layouts.app')

@section('title', 'Chart of Accounts')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('dashboard') }}" class="hover:text-primary-600">Dashboard</a></li>
            <li class="flex items-center"><i class="fa-solid fa-chevron-right text-xs mx-2"></i> Chart of Accounts</li>
        </ol>
    </nav>
@endsection

@section('content')
<div x-data="accountList()">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Chart of Accounts</h2>
        <button @click="openAddModal()" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700">
            <i class="fa-solid fa-plus mr-2"></i> Add Account
        </button>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Name</th>
                        <th class="px-4 py-3 text-left font-medium">Account Number</th>
                        <th class="px-4 py-3 text-left font-medium">Type</th>
                        <th class="px-4 py-3 text-center font-medium">Status</th>
                        <th class="px-4 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($accounts ?? [] as $account)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">{{ $account->name }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><code class="bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded text-xs">{{ $account->account_number }}</code></td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $account->accountType->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($account->is_closed)
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Closed</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Active</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button @click="editAccount({{ $account }})" class="text-primary-600 hover:text-primary-800 mr-3"><i class="fa-solid fa-pen-to-square"></i></button>
                            <form action="{{ route('account.destroy', $account->id ?? 0) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Delete this account?')"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No accounts found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            {{ ($accounts ?? collect())->links() }}
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900/50" @click="showModal = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100" x-text="editingId ? 'Edit Account' : 'Add Account'"></h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <form :action="editingId ? '{{ route('account.update', '') }}/' + editingId : '{{ route('account.store') }}'" method="POST">
                    @csrf
                    <input type="hidden" name="_method" x-bind:value="editingId ? 'PUT' : 'POST'">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                            <input type="text" name="name" x-model="form.name" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Account Number</label>
                            <input type="text" name="account_number" x-model="form.account_number" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Account Type</label>
                            <select name="account_type_id" x-model="form.account_type_id" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Select Type</option>
                                @foreach($accountTypes ?? [] as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="is_closed" x-model="form.is_closed" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Is Closed</span>
                            </label>
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
function accountList() {
    return {
        showModal: false,
        editingId: null,
        form: { name: '', account_number: '', account_type_id: '', is_closed: false },
        openAddModal() {
            this.editingId = null;
            this.form = { name: '', account_number: '', account_type_id: '', is_closed: false };
            this.showModal = true;
        },
        editAccount(account) {
            this.editingId = account.id;
            this.form.name = account.name;
            this.form.account_number = account.account_number;
            this.form.account_type_id = account.account_type_id;
            this.form.is_closed = account.is_closed;
            this.showModal = true;
        }
    }
}
</script>
@endpush
