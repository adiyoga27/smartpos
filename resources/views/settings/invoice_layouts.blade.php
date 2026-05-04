@extends('layouts.app')

@section('title', 'Invoice Layouts & Schemes')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('dashboard') }}" class="hover:text-primary-600">Dashboard</a></li>
            <li class="flex items-center"><i class="fa-solid fa-chevron-right text-xs mx-2"></i> Settings</li>
            <li class="flex items-center"><i class="fa-solid fa-chevron-right text-xs mx-2"></i> Invoice</li>
        </ol>
    </nav>
@endsection

@section('content')
<div x-data="invoiceSettings()">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Invoice Settings</h2>
    </div>

    {{-- Tabs --}}
    <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
        <nav class="flex gap-4 -mb-px">
            <button @click="activeTab = 'layouts'" :class="activeTab === 'layouts' ? 'border-primary-600 text-primary-600 dark:text-primary-400 dark:border-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'" class="pb-3 px-1 border-b-2 text-sm font-medium transition-colors">
                Layouts
            </button>
            <button @click="activeTab = 'schemes'" :class="activeTab === 'schemes' ? 'border-primary-600 text-primary-600 dark:text-primary-400 dark:border-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'" class="pb-3 px-1 border-b-2 text-sm font-medium transition-colors">
                Schemes
            </button>
        </nav>
    </div>

    {{-- Layouts Tab --}}
    <div x-show="activeTab === 'layouts'">
        <div class="flex justify-end mb-4">
            <button @click="openLayoutModal()" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700">
                <i class="fa-solid fa-plus mr-2"></i> Add Layout
            </button>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">Name</th>
                            <th class="px-4 py-3 text-left font-medium">Design</th>
                            <th class="px-4 py-3 text-center font-medium">Default</th>
                            <th class="px-4 py-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($layouts ?? [] as $layout)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">{{ $layout->name }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $layout->design ?? 'Default' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($layout->is_default)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-success-50 text-success-600 dark:bg-success-900/30 dark:text-success-400">Default</span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button @click="editLayout({{ $layout }})" class="text-primary-600 hover:text-primary-800 mr-3"><i class="fa-solid fa-pen-to-square"></i></button>
                                <form action="{{ route('settings.invoice-layouts.destroy', $layout->id ?? 0) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Delete this layout?')"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No invoice layouts found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Schemes Tab --}}
    <div x-show="activeTab === 'schemes'">
        <div class="flex justify-end mb-4">
            <button @click="openSchemeModal()" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700">
                <i class="fa-solid fa-plus mr-2"></i> Add Scheme
            </button>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">Name</th>
                            <th class="px-4 py-3 text-left font-medium">Prefix</th>
                            <th class="px-4 py-3 text-center font-medium">Start Number</th>
                            <th class="px-4 py-3 text-center font-medium">Current</th>
                            <th class="px-4 py-3 text-center font-medium">Default</th>
                            <th class="px-4 py-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($schemes ?? [] as $scheme)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">{{ $scheme->name }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><code class="bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded text-xs">{{ $scheme->prefix }}</code></td>
                            <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">{{ $scheme->start_number }}</td>
                            <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">{{ $scheme->current_number ?? $scheme->start_number }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($scheme->is_default)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-success-50 text-success-600 dark:bg-success-900/30 dark:text-success-400">Default</span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button @click="editScheme({{ $scheme }})" class="text-primary-600 hover:text-primary-800 mr-3"><i class="fa-solid fa-pen-to-square"></i></button>
                                <form action="{{ route('settings.invoice-schemes.destroy', $scheme->id ?? 0) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Delete this scheme?')"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No invoice schemes found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Layout Modal --}}
    <div x-show="showLayoutModal" class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900/50" @click="showLayoutModal = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100" x-text="editingLayoutId ? 'Edit Layout' : 'Add Layout'"></h3>
                    <button @click="showLayoutModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <form :action="editingLayoutId ? '{{ route('settings.invoice-layouts.update', '__ID__') }}'.replace('__ID__', editingLayoutId) : '{{ route('settings.invoice-layouts.store') }}'" method="POST">
                    @csrf
                    <input type="hidden" name="_method" x-bind:value="editingLayoutId ? 'PUT' : 'POST'">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                            <input type="text" name="name" x-model="layoutForm.name" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Design</label>
                            <select name="design" x-model="layoutForm.design" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="default">Default</option>
                                <option value="modern">Modern</option>
                                <option value="classic">Classic</option>
                                <option value="compact">Compact</option>
                            </select>
                        </div>
                        <div>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="is_default" x-model="layoutForm.is_default" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Set as Default</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" @click="showLayoutModal = false" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">Cancel</button>
                        <button type="submit" class="px-6 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Scheme Modal --}}
    <div x-show="showSchemeModal" class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900/50" @click="showSchemeModal = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100" x-text="editingSchemeId ? 'Edit Scheme' : 'Add Scheme'"></h3>
                    <button @click="showSchemeModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <form :action="editingSchemeId ? '{{ route('settings.invoice-schemes.update', '__ID__') }}'.replace('__ID__', editingSchemeId) : '{{ route('settings.invoice-schemes.store') }}'" method="POST">
                    @csrf
                    <input type="hidden" name="_method" x-bind:value="editingSchemeId ? 'PUT' : 'POST'">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                            <input type="text" name="name" x-model="schemeForm.name" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prefix</label>
                            <input type="text" name="prefix" x-model="schemeForm.prefix" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Number</label>
                            <input type="number" name="start_number" x-model="schemeForm.start_number" min="1" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="is_default" x-model="schemeForm.is_default" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Set as Default</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" @click="showSchemeModal = false" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">Cancel</button>
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
function invoiceSettings() {
    return {
        activeTab: 'layouts',
        showLayoutModal: false,
        editingLayoutId: null,
        layoutForm: { name: '', design: 'default', is_default: false },
        showSchemeModal: false,
        editingSchemeId: null,
        schemeForm: { name: '', prefix: '', start_number: 1, is_default: false },
        openLayoutModal() {
            this.editingLayoutId = null;
            this.layoutForm = { name: '', design: 'default', is_default: false };
            this.showLayoutModal = true;
        },
        editLayout(layout) {
            this.editingLayoutId = layout.id;
            this.layoutForm.name = layout.name;
            this.layoutForm.design = layout.design ?? 'default';
            this.layoutForm.is_default = layout.is_default;
            this.showLayoutModal = true;
        },
        openSchemeModal() {
            this.editingSchemeId = null;
            this.schemeForm = { name: '', prefix: '', start_number: 1, is_default: false };
            this.showSchemeModal = true;
        },
        editScheme(scheme) {
            this.editingSchemeId = scheme.id;
            this.schemeForm.name = scheme.name;
            this.schemeForm.prefix = scheme.prefix ?? '';
            this.schemeForm.start_number = scheme.start_number;
            this.schemeForm.is_default = scheme.is_default;
            this.showSchemeModal = true;
        }
    }
}
</script>
@endpush
