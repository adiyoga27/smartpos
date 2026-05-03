@extends('layouts.app')

@section('title', 'User Management')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('dashboard') }}" class="hover:text-primary-600">Dashboard</a></li>
            <li class="flex items-center"><i class="fa-solid fa-chevron-right text-xs mx-2"></i> Users</li>
        </ol>
    </nav>
@endsection

@section('content')
<div x-data="userList()">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">User Management</h2>
        <button @click="openAddModal()" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700">
            <i class="fa-solid fa-plus mr-2"></i> Add User
        </button>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Name</th>
                        <th class="px-4 py-3 text-left font-medium">Username</th>
                        <th class="px-4 py-3 text-left font-medium">Email</th>
                        <th class="px-4 py-3 text-left font-medium">Role</th>
                        <th class="px-4 py-3 text-center font-medium">Status</th>
                        <th class="px-4 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($users ?? [] as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $user->username }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $user->email }}</td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $user->role->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ ($user->status ?? '') === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                                {{ ucfirst($user->status ?? 'active') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button @click="editUser({{ $user }})" class="text-primary-600 hover:text-primary-800 mr-3"><i class="fa-solid fa-pen-to-square"></i></button>
                            <form action="{{ route('manage_user.destroy', $user->id ?? 0) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Delete this user?')"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            {{ ($users ?? collect())->links() }}
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900/50" @click="showModal = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-lg p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100" x-text="editingId ? 'Edit User' : 'Add User'"></h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <form :action="editingId ? '{{ route('manage_user.update', '') }}/' + editingId : '{{ route('manage_user.store') }}'" method="POST">
                    @csrf
                    <input type="hidden" name="_method" x-bind:value="editingId ? 'PUT' : 'POST'">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                            <input type="text" name="name" x-model="form.name" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Username</label>
                                <input type="text" name="username" x-model="form.username" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                                <input type="email" name="email" x-model="form.email" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Password <span x-show="!editingId" class="text-red-500">*</span>
                                    <span x-show="editingId" class="text-gray-400 text-xs">(leave blank to keep current)</span>
                                </label>
                                <input type="password" name="password" x-model="form.password" :required="!editingId" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                                <select name="role_id" x-model="form.role_id" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">Select Role</option>
                                    @foreach($roles ?? [] as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                                <input type="text" name="phone" x-model="form.phone" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                <select name="status" x-model="form.status" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
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
function userList() {
    return {
        showModal: false,
        editingId: null,
        form: { name: '', username: '', email: '', password: '', role_id: '', phone: '', status: 'active' },
        openAddModal() {
            this.editingId = null;
            this.form = { name: '', username: '', email: '', password: '', role_id: '', phone: '', status: 'active' };
            this.showModal = true;
        },
        editUser(user) {
            this.editingId = user.id;
            this.form.name = user.name;
            this.form.username = user.username;
            this.form.email = user.email;
            this.form.password = '';
            this.form.role_id = user.role_id;
            this.form.phone = user.phone ?? '';
            this.form.status = user.status ?? 'active';
            this.showModal = true;
        }
    }
}
</script>
@endpush
