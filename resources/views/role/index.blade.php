@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('dashboard') }}" class="hover:text-primary-600">Dashboard</a></li>
            <li class="flex items-center"><i class="fa-solid fa-chevron-right text-xs mx-2"></i> Roles</li>
        </ol>
    </nav>
@endsection

@section('content')
<div x-data="roleList()">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Roles & Permissions</h2>
        <button @click="openAddModal()" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700">
            <i class="fa-solid fa-plus mr-2"></i> Add Role
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($roles ?? [] as $role)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h3 class="text-base font-bold text-gray-800 dark:text-gray-100">{{ $role->name }}</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $role->users_count ?? 0 }} users</p>
                </div>
                <div class="flex gap-2">
                    <button @click="editRole({{ $role }})" class="text-primary-600 hover:text-primary-800"><i class="fa-solid fa-pen-to-square"></i></button>
                    <form action="{{ route('roles.destroy', $role->id ?? 0) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Delete this role?')"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </div>
            </div>
            <div class="flex flex-wrap gap-1">
                @forelse($role->permissions ?? [] as $perm)
                    <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded text-xs">{{ $perm->name }}</span>
                @empty
                    <span class="text-xs text-gray-400 dark:text-gray-500 italic">No permissions assigned</span>
                @endforelse
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-8 text-gray-500 dark:text-gray-400">No roles found.</div>
        @endforelse
    </div>

    {{-- Add/Edit Modal --}}
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900/50" @click="showModal = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-lg max-h-[85vh] overflow-y-auto p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100" x-text="editingId ? 'Edit Role' : 'Add Role'"></h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <form :action="editingId ? '{{ route('roles.update', '') }}/' + editingId : '{{ route('roles.store') }}'" method="POST">
                    @csrf
                    <input type="hidden" name="_method" x-bind:value="editingId ? 'PUT' : 'POST'">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role Name</label>
                            <input type="text" name="name" x-model="form.name" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>

                        <div>
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3">Permissions</h4>
                            <template x-for="mod in permissionModules" :key="mod.name">
                                <div class="mb-3 border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                                    <div class="flex items-center mb-2">
                                        <input type="checkbox" :id="'select-all-' + mod.name" @change="toggleModule(mod, $event.target.checked)" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700">
                                        <label :for="'select-all-' + mod.name" class="ml-2 text-sm font-semibold text-gray-700 dark:text-gray-300 capitalize" x-text="mod.name"></label>
                                    </div>
                                    <div class="grid grid-cols-2 gap-1.5 ml-6">
                                        <template x-for="perm in mod.permissions" :key="perm">
                                            <label class="flex items-center gap-1.5">
                                                <input type="checkbox" name="permissions[]" :value="perm" :checked="selectedPermissions.includes(perm)" @change="togglePerm(perm)" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700">
                                                <span class="text-xs text-gray-600 dark:text-gray-400" x-text="perm.split('.').pop().replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())"></span>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                            </template>
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
function roleList() {
    return {
        showModal: false,
        editingId: null,
        form: { name: '' },
        selectedPermissions: [],
        permissionModules: {!! json_encode($permissionModules ?? []) !!},
        openAddModal() {
            this.editingId = null;
            this.form.name = '';
            this.selectedPermissions = [];
            this.showModal = true;
        },
        editRole(role) {
            this.editingId = role.id;
            this.form.name = role.name;
            this.selectedPermissions = (role.permissions ?? []).map(p => p.name);
            this.showModal = true;
        },
        toggleModule(mod, checked) {
            if (checked) {
                mod.permissions.forEach(p => { if (!this.selectedPermissions.includes(p)) this.selectedPermissions.push(p); });
            } else {
                this.selectedPermissions = this.selectedPermissions.filter(p => !mod.permissions.includes(p));
            }
        },
        togglePerm(perm) {
            const idx = this.selectedPermissions.indexOf(perm);
            if (idx > -1) this.selectedPermissions.splice(idx, 1);
            else this.selectedPermissions.push(perm);
        }
    }
}
</script>
@endpush
