@extends('layouts.app')

@section('title', 'Kategori')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="flex items-center gap-1 text-sm">
            <li><a href="{{ route('dashboard') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700">Dashboard</a></li>
            <li><i class="fa-solid fa-chevron-right text-xs text-gray-400 mx-1"></i></li>
            <li><span class="text-gray-900 dark:text-white font-medium">Kategori</span></li>
        </ol>
    </nav>
@endsection

@section('content')
<div x-data="{ open: false, editMode: false, form: { id: null, name: '', short_code: '', parent_id: '' } }">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar Kategori</h2>
            <button x-on:click="open = true; editMode = false; form = { id: null, name: '', short_code: '', parent_id: '' }"
                class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i class="fa-solid fa-plus mr-2"></i> Tambah Kategori
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kode Singkat</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Induk Kategori</th>
                        <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($categories ?? [] as $category)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-medium">{{ $category->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 font-mono">{{ $category->short_code ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $category->parent->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button x-on:click="open = true; editMode = true; form = { id: {{ $category->id }}, name: '{{ addslashes($category->name) }}', short_code: '{{ addslashes($category->short_code ?? '') }}', parent_id: '{{ $category->parent_id ?? '' }}' }"
                                        class="p-2 text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-colors">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </button>
                                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
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
                                <i class="fa-solid fa-folder-open text-3xl mb-2 block"></i>
                                Belum ada kategori. Klik "Tambah Kategori" untuk memulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($categories ?? new \Illuminate\Database\Eloquent\Collection, 'links'))
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    {{-- Add/Edit Modal --}}
    <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto" x-transition.opacity x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div x-on:click="open = false" class="fixed inset-0 bg-gray-900/50 transition-opacity"></div>

            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="editMode ? 'Edit Kategori' : 'Tambah Kategori'"></h3>
                    <button x-on:click="open = false" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form x-bind:action="editMode ? '{{ route('categories.index') }}/' + form.id : '{{ route('categories.store') }}'" method="POST" class="space-y-4">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div>
                        <label for="cat_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Kategori</label>
                        <input id="cat_name" name="name" type="text" x-model="form.name" required
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm"
                            placeholder="Nama kategori">
                    </div>

                    <div>
                        <label for="cat_short_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kode Singkat</label>
                        <input id="cat_short_code" name="short_code" type="text" x-model="form.short_code"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm"
                            placeholder="Kode (opsional)">
                    </div>

                    <div>
                        <label for="cat_parent" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Induk Kategori</label>
                        <select id="cat_parent" name="parent_id" x-model="form.parent_id"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                            <option value="">-- Tidak ada --</option>
                            @foreach(($parentCategories ?? []) as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" x-on:click="open = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors">
                            <i class="fa-solid fa-save mr-1.5"></i> <span x-text="editMode ? 'Simpan' : 'Simpan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
