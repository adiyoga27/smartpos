@extends('layouts.app')

@section('title', 'Satuan')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="flex items-center gap-1 text-sm">
            <li><a href="{{ route('dashboard') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700">Dashboard</a></li>
            <li><i class="fa-solid fa-chevron-right text-xs text-gray-400 mx-1"></i></li>
            <li><span class="text-gray-900 dark:text-white font-medium">Satuan</span></li>
        </ol>
    </nav>
@endsection

@section('content')
<div x-data="{ open: false, editMode: false, form: { id: null, actual_name: '', short_name: '', allow_decimal: true } }">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar Satuan</h2>
            <button x-on:click="open = true; editMode = false; form = { id: null, actual_name: '', short_name: '', allow_decimal: true }"
                class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i class="fa-solid fa-plus mr-2"></i> Tambah Satuan
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama Aktual</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama Singkat</th>
                        <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Desimal</th>
                        <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($units ?? [] as $unit)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-medium">{{ $unit->actual_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 font-mono">{{ $unit->short_name }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($unit->allow_decimal)
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-success-50 text-success-600 dark:bg-green-900/20 dark:text-success-400">
                                        <i class="fa-solid fa-check mr-1"></i> Ya
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                        <i class="fa-solid fa-xmark mr-1"></i> Tidak
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button x-on:click="open = true; editMode = true; form = { id: {{ $unit->id }}, actual_name: '{{ addslashes($unit->actual_name) }}', short_name: '{{ addslashes($unit->short_name) }}', allow_decimal: {{ $unit->allow_decimal ? 'true' : 'false' }} }"
                                        class="p-2 text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-colors">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </button>
                                    <form action="{{ route('units.destroy', $unit->id) }}" method="POST" onsubmit="return confirm('Hapus satuan ini?')">
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
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">
                                <i class="fa-solid fa-weight-scale text-3xl mb-2 block"></i>
                                Belum ada satuan. Klik "Tambah Satuan" untuk memulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($units ?? new \Illuminate\Database\Eloquent\Collection, 'links'))
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $units->links() }}
            </div>
        @endif
    </div>

    {{-- Add/Edit Modal --}}
    <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto" x-transition.opacity x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div x-on:click="open = false" class="fixed inset-0 bg-gray-900/50 transition-opacity"></div>

            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="editMode ? 'Edit Satuan' : 'Tambah Satuan'"></h3>
                    <button x-on:click="open = false" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form x-bind:action="editMode ? '{{ route('units.index') }}/' + form.id : '{{ route('units.store') }}'" method="POST" class="space-y-4">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div>
                        <label for="unit_actual" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Aktual</label>
                        <input id="unit_actual" name="actual_name" type="text" x-model="form.actual_name" required
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm"
                            placeholder="Contoh: Kilogram">
                    </div>

                    <div>
                        <label for="unit_short" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Singkat</label>
                        <input id="unit_short" name="short_name" type="text" x-model="form.short_name" required
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm"
                            placeholder="Contoh: kg">
                    </div>

                    <div class="flex items-center gap-3">
                        <input id="unit_decimal" name="allow_decimal" type="checkbox" x-model="form.allow_decimal" value="1"
                            class="rounded border border-gray-300 text-primary-600 focus:ring-primary-500">
                        <label for="unit_decimal" class="text-sm text-gray-700 dark:text-gray-300">Izinkan nilai desimal</label>
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
