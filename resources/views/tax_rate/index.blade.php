@extends('layouts.app')

@section('title', 'Pajak')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="flex items-center gap-1 text-sm">
            <li><a href="{{ route('dashboard') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700">Dashboard</a></li>
            <li><i class="fa-solid fa-chevron-right text-xs text-gray-400 mx-1"></i></li>
            <li><span class="text-gray-900 dark:text-white font-medium">Pajak</span></li>
        </ol>
    </nav>
@endsection

@section('content')
<div x-data="{ open: false, editMode: false, form: { id: null, name: '', amount: '' } }">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar Tarif Pajak</h2>
            <button x-on:click="open = true; editMode = false; form = { id: null, name: '', amount: '' }"
                class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i class="fa-solid fa-plus mr-2"></i> Tambah Pajak
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tarif (%)</th>
                        <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($taxRates ?? [] as $tax)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-medium">{{ $tax->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300">
                                    {{ number_format($tax->amount, 2) }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button x-on:click="open = true; editMode = true; form = { id: {{ $tax->id }}, name: '{{ addslashes($tax->name) }}', amount: '{{ $tax->amount }}' }"
                                        class="p-2 text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-colors">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </button>
                                    <form action="{{ route('tax-rates.destroy', $tax->id) }}" method="POST" onsubmit="return confirm('Hapus pajak ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-danger-600 hover:text-danger-700 dark:text-danger-400 dark:hover:text-danger-300 hover:bg-danger-50 dark:hover:bg-danger-900/20 rounded-lg transition-colors">
                                            <i class="fa-solid fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">
                                <i class="fa-solid fa-percent text-3xl mb-2 block"></i>
                                Belum ada tarif pajak. Klik "Tambah Pajak" untuk memulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($taxRates ?? new \Illuminate\Database\Eloquent\Collection, 'links'))
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $taxRates->links() }}
            </div>
        @endif
    </div>

    {{-- Add/Edit Modal --}}
    <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto" x-transition.opacity x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div x-on:click="open = false" class="fixed inset-0 bg-gray-900/50 transition-opacity"></div>

            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="editMode ? 'Edit Pajak' : 'Tambah Pajak'"></h3>
                    <button x-on:click="open = false" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form x-bind:action="editMode ? '{{ route('tax-rates.index') }}/' + form.id : '{{ route('tax-rates.store') }}'" method="POST" class="space-y-4">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div>
                        <label for="tax_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Pajak</label>
                        <input id="tax_name" name="name" type="text" x-model="form.name" required
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm"
                            placeholder="Contoh: PPN 11%">
                    </div>

                    <div>
                        <label for="tax_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tarif (%)</label>
                        <div class="relative">
                            <input id="tax_amount" name="amount" type="number" step="0.01" min="0" max="100" x-model="form.amount" required
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm"
                                placeholder="0.00">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-400">%</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" x-on:click="open = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors">
                            <i class="fa-solid fa-save mr-1.5"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
