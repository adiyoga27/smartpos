@extends('layouts.app')

@section('title', 'Kontak')

@section('breadcrumb')
    <h1 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Pelanggan & Supplier</h1>
@endsection

@section('content')
<div x-data="contactIndex()" x-init="init()">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Daftar Kontak</h2>
        <button type="button" x-on:click="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
            <i class="fa-solid fa-plus"></i> Tambah Kontak
        </button>
    </div>

    {{-- Search & Filter --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form method="GET" action="{{ route('contacts.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, perusahaan, email, telepon..." class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            </div>
            <select name="type" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none min-w-[160px]">
                <option value="">Semua Tipe</option>
                <option value="customer" {{ request('type') == 'customer' ? 'selected' : '' }}>Pelanggan</option>
                <option value="supplier" {{ request('type') == 'supplier' ? 'selected' : '' }}>Supplier</option>
                <option value="both" {{ request('type') == 'both' ? 'selected' : '' }}>Keduanya</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i class="fa-solid fa-filter mr-1"></i> Filter
            </button>
            @if(request('search') || request('type'))
                <a href="{{ route('contacts.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors inline-flex items-center">
                    <i class="fa-solid fa-xmark mr-1"></i> Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Nama / Perusahaan</th>
                        <th class="px-4 py-3 font-semibold">Tipe</th>
                        <th class="px-4 py-3 font-semibold">Telepon</th>
                        <th class="px-4 py-3 font-semibold">Email</th>
                        <th class="px-4 py-3 font-semibold text-right">Saldo (Rp)</th>
                        <th class="px-4 py-3 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($contacts ?? [] as $contact)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800 dark:text-gray-100">{{ $contact->full_name }}</div>
                                @if($contact->supplier_business_name)
                                    <div class="text-xs text-gray-400 dark:text-gray-500">{{ $contact->supplier_business_name }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($contact->type == 'customer')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                        <i class="fa-solid fa-user text-[10px]"></i> Pelanggan
                                    </span>
                                @elseif($contact->type == 'supplier')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-orange-50 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300">
                                        <i class="fa-solid fa-truck text-[10px]"></i> Supplier
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                        <i class="fa-solid fa-arrows-left-right text-[10px]"></i> Keduanya
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $contact->mobile ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $contact->email ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-semibold {{ ($contact->balance ?? 0) < 0 ? 'text-danger-600' : 'text-gray-700 dark:text-gray-200' }}">
                                {{ number_format($contact->balance ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button type="button" x-on:click="openEditModal({{ $contact->id }})" class="p-1.5 text-primary-600 hover:text-primary-700 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded transition-colors" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button type="button" x-on:click="confirmDelete({{ $contact->id }}, '{{ addslashes($contact->full_name) }}')" class="p-1.5 text-danger-500 hover:text-danger-600 hover:bg-danger-50 dark:hover:bg-red-900/20 rounded transition-colors" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                                <i class="fa-solid fa-users text-3xl mb-2 block"></i>
                                Belum ada kontak. Klik <span class="text-primary-500 cursor-pointer hover:underline" x-on:click="openCreateModal()">Tambah Kontak</span> untuk memulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(!empty($contacts) && $contacts->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $contacts->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Form --}}
    <div x-show="showModal" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center pt-[5vh] bg-black/50" x-on:click.self="showModal = false">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-gray-700" x-on:click.stop="">
            <div class="sticky top-0 bg-white dark:bg-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between z-10">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100" x-text="modalTitle"></h3>
                <button x-on:click="showModal = false" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <div class="px-6 py-4">
                <form method="POST" x-bind:action="formAction" id="contactForm" @submit="submitting = true">
                    @csrf
                    <input type="hidden" name="_method" x-bind:value="formMethod">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Type --}}
                        <div>
                            <label for="modal_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe <span class="text-danger-500">*</span></label>
                            <select name="type" id="modal_type" x-model="formData.type" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                                <option value="customer">Pelanggan</option>
                                <option value="supplier">Supplier</option>
                                <option value="both">Keduanya</option>
                            </select>
                        </div>

                        {{-- First Name --}}
                        <div>
                            <label for="modal_first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama <span class="text-danger-500">*</span></label>
                            <input type="text" name="first_name" id="modal_first_name" x-model="formData.first_name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                        </div>

                        {{-- Supplier Business Name --}}
                        <div x-show="formData.type === 'supplier' || formData.type === 'both'">
                            <label for="modal_supplier_business_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Perusahaan</label>
                            <input type="text" name="supplier_business_name" id="modal_supplier_business_name" x-model="formData.supplier_business_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                        </div>

                        {{-- Mobile --}}
                        <div>
                            <label for="modal_mobile" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telepon</label>
                            <input type="text" name="mobile" id="modal_mobile" x-model="formData.mobile" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="modal_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                            <input type="email" name="email" id="modal_email" x-model="formData.email" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                        </div>

                        {{-- Address Line 1 --}}
                        <div class="md:col-span-2">
                            <label for="modal_address_line_1" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat</label>
                            <textarea name="address_line_1" id="modal_address_line_1" x-model="formData.address_line_1" rows="2" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none"></textarea>
                        </div>

                        {{-- City --}}
                        <div>
                            <label for="modal_city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kota</label>
                            <input type="text" name="city" id="modal_city" x-model="formData.city" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                        </div>

                        {{-- Tax Number --}}
                        <div>
                            <label for="modal_tax_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NPWP</label>
                            <input type="text" name="tax_number" id="modal_tax_number" x-model="formData.tax_number" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                        </div>

                        {{-- Customer Group --}}
                        <div>
                            <label for="modal_customer_group_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Grup Pelanggan</label>
                            <select name="customer_group_id" id="modal_customer_group_id" x-model="formData.customer_group_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                                <option value="">-- Tidak Ada --</option>
                                @foreach($customerGroups ?? [] as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }} ({{ $group->amount }}%)</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Credit Limit --}}
                        <div>
                            <label for="modal_credit_limit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Batas Kredit (Rp)</label>
                            <input type="number" name="credit_limit" id="modal_credit_limit" x-model="formData.credit_limit" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
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
            <p class="text-gray-600 dark:text-gray-300 mb-4">Apakah Anda yakin ingin menghapus kontak <strong x-text="deleteName"></strong>?</p>
            <div class="flex justify-end gap-3">
                <button x-on:click="showDeleteModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Batal</button>
                <form method="POST" x-bind:action="'{{ route('contacts.index') }}/' + deleteId">
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
    function contactIndex() {
        return {
            showModal: false,
            showDeleteModal: false,
            modalTitle: 'Tambah Kontak',
            submitLabel: 'Simpan',
            formAction: '{{ route('contacts.store') }}',
            formMethod: 'POST',
            submitting: false,
            deleteId: null,
            deleteName: '',
            formData: {
                type: 'customer',
                first_name: '',
                supplier_business_name: '',
                mobile: '',
                email: '',
                address_line_1: '',
                city: '',
                tax_number: '',
                customer_group_id: '',
                credit_limit: ''
            },
            openCreateModal() {
                this.modalTitle = 'Tambah Kontak Baru';
                this.submitLabel = 'Simpan';
                this.formAction = '{{ route('contacts.store') }}';
                this.formMethod = 'POST';
                this.resetForm();
                this.showModal = true;
            },
            openEditModal(id) {
                this.modalTitle = 'Edit Kontak';
                this.submitLabel = 'Perbarui';
                this.formAction = '{{ route('contacts.index') }}/' + id;
                this.formMethod = 'PUT';
                this.submitting = false;
                this.fetchContact(id);
                this.showModal = true;
            },
            async fetchContact(id) {
                try {
                    const resp = await fetch('{{ route('contacts.index') }}/' + id + '?format=json');
                    const data = await resp.json();
                    this.formData = {
                        type: data.type || 'customer',
                        first_name: data.first_name || '',
                        supplier_business_name: data.supplier_business_name || '',
                        mobile: data.mobile || '',
                        email: data.email || '',
                        address_line_1: data.address_line_1 || '',
                        city: data.city || '',
                        tax_number: data.tax_number || '',
                        customer_group_id: data.customer_group_id || '',
                        credit_limit: data.credit_limit || ''
                    };
                } catch (e) {
                    alert('Gagal memuat data kontak.');
                    this.showModal = false;
                }
            },
            resetForm() {
                this.formData = {
                    type: 'customer',
                    first_name: '',
                    supplier_business_name: '',
                    mobile: '',
                    email: '',
                    address_line_1: '',
                    city: '',
                    tax_number: '',
                    customer_group_id: '',
                    credit_limit: ''
                };
                this.submitting = false;
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
