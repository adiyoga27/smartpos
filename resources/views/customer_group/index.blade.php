@extends('layouts.app')

@section('title', 'Grup Pelanggan')

@section('breadcrumb')
    <h1 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Grup Pelanggan</h1>
@endsection

@section('content')
<div x-data="customerGroupIndex()" x-init="init()">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Daftar Grup Pelanggan</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola grup pelanggan untuk diskon atau harga khusus.</p>
        </div>
        <button type="button" x-on:click="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
            <i class="fa-solid fa-plus"></i> Tambah Grup
        </button>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Nama Grup</th>
                        <th class="px-4 py-3 font-semibold text-right">Diskon (%)</th>
                        <th class="px-4 py-3 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($customerGroups ?? [] as $group)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $group->name }}</td>
                            <td class="px-4 py-3 text-right">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300">
                                    {{ number_format($group->amount, 2) }}%
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button type="button" x-on:click="openEditModal({{ $group->id }}, '{{ addslashes($group->name) }}', {{ $group->amount }})" class="p-1.5 text-primary-600 hover:text-primary-700 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded transition-colors" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button type="button" x-on:click="confirmDelete({{ $group->id }}, '{{ addslashes($group->name) }}')" class="p-1.5 text-danger-500 hover:text-danger-600 hover:bg-danger-50 dark:hover:bg-red-900/20 rounded transition-colors" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                                <i class="fa-solid fa-layer-group text-3xl mb-2 block"></i>
                                Belum ada grup pelanggan. Klik <span class="text-primary-500 cursor-pointer hover:underline" x-on:click="openCreateModal()">Tambah Grup</span> untuk memulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(!empty($customerGroups) && $customerGroups->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $customerGroups->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Form --}}
    <div x-show="showModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-on:click.self="showModal = false">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4 border border-gray-200 dark:border-gray-700" x-on:click.stop="">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100" x-text="modalTitle"></h3>
                <button x-on:click="showModal = false" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <div class="px-6 py-4">
                <form method="POST" x-bind:action="formAction" @submit="submitting = true">
                    @csrf
                    <input type="hidden" name="_method" x-bind:value="formMethod">

                    <div class="space-y-4">
                        <div>
                            <label for="group_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Grup <span class="text-danger-500">*</span></label>
                            <input type="text" name="name" id="group_name" x-model="formData.name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                        </div>
                        <div>
                            <label for="group_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Diskon (%) <span class="text-danger-500">*</span></label>
                            <input type="number" name="amount" id="group_amount" x-model="formData.amount" required step="0.01" min="0" max="100" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                            <p class="mt-1 text-xs text-gray-400">Persentase diskon yang diberikan untuk anggota grup ini.</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" x-on:click="showModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors" x-bind:disabled="submitting">
                            <i class="fa-solid fa-floppy-disk mr-1"></i> <span x-text="submitLabel"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-show="showDeleteModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-on:click.self="showDeleteModal = false">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2">Konfirmasi Hapus</h3>
            <p class="text-gray-600 dark:text-gray-300 mb-4">Apakah Anda yakin ingin menghapus grup <strong x-text="deleteName"></strong>?</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mb-4">Pelanggan dalam grup ini tidak akan dihapus.</p>
            <div class="flex justify-end gap-3">
                <button x-on:click="showDeleteModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Batal</button>
                <form method="POST" x-bind:action="'{{ route('customer-groups.destroy', '__ID__') }}'.replace('__ID__', deleteId)">
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
    function customerGroupIndex() {
        return {
            showModal: false,
            showDeleteModal: false,
            modalTitle: 'Tambah Grup',
            submitLabel: 'Simpan',
            formAction: '{{ route('customer-groups.store') }}',
            formMethod: 'POST',
            submitting: false,
            deleteId: null,
            deleteName: '',
            formData: {
                name: '',
                amount: 0
            },
            openCreateModal() {
                this.modalTitle = 'Tambah Grup Pelanggan';
                this.submitLabel = 'Simpan';
                this.formAction = '{{ route('customer-groups.store') }}';
                this.formMethod = 'POST';
                this.formData = { name: '', amount: 0 };
                this.submitting = false;
                this.showModal = true;
            },
            openEditModal(id, name, amount) {
                this.modalTitle = 'Edit Grup Pelanggan';
                this.submitLabel = 'Perbarui';
                this.formAction = '{{ route('customer-groups.update', '__ID__') }}'.replace('__ID__', id);
                this.formMethod = 'PUT';
                this.formData = { name: name, amount: amount };
                this.submitting = false;
                this.showModal = true;
            },
            confirmDelete(id, name) {
                this.deleteId = id;
                this.deleteName = name;
                this.showDeleteModal = true;
            }
        }
    }
</script>
@endpush
