<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Admin Console</p>
                <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">User Management</h2>
            </div>
            <div class="grid w-full grid-cols-2 gap-2 sm:flex sm:w-auto sm:items-center">
                <a href="{{ route('admin.settings') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 sm:w-auto">Back to Settings</a>
                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center justify-center rounded-lg bg-[#32CD32] px-4 py-2 text-sm font-medium text-[#0B4D2C] sm:w-auto">
                    <span class="sm:hidden">+ User</span>
                    <span class="hidden sm:inline">+ Create User</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">{{ session('status') }}</div>
        @endif

        <section class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            @if ($users->isEmpty())
                <div class="px-6 py-12 text-center">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm8 6a8 8 0 0 0-16 0"/></svg>
                    </div>
                    <p class="font-medium text-slate-700">No users found</p>
                    <p class="mt-1 text-sm text-slate-500">Create users and assign roles for operations.</p>
                </div>
            @else
                <div class="table-shell">
                    <table class="table-compact min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Role</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr class="odd:bg-white even:bg-[#F4F7F5] hover:bg-slate-100/70">
                                    <td class="cell-truncate px-4 py-3 font-medium text-slate-800">{{ $user->name }}</td>
                                    <td class="cell-truncate px-4 py-3 text-slate-700">{{ $user->email }}</td>
                                    <td class="px-4 py-3">
                                        @if ($user->role === 'admin')
                                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">Admin</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">Accountant</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($user->is_active)
                                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">Active</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-orange-100 px-2.5 py-1 text-xs font-medium text-orange-700">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="actions-compact">
                                            <a class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50" href="{{ route('admin.users.edit', $user) }}">Edit</a>
                                            @if ((int) $user->id !== (int) auth()->id())
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" data-confirm-title="Delete user?" data-confirm-message="This action cannot be undone." data-confirm-confirm-text="Delete" data-confirm-variant="danger">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="rounded-lg border border-red-300 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50" type="submit">Delete</button>
                                                </form>
                                            @endif
                                            @if ($user->is_active)
                                                <form method="POST" action="{{ route('admin.users.deactivate', $user) }}" data-confirm-title="Deactivate user?" data-confirm-message="The user will lose access until reactivated." data-confirm-confirm-text="Deactivate" data-confirm-variant="warning">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button class="rounded-lg border border-[#FF6B35] px-3 py-1.5 text-xs font-medium text-[#FF6B35] hover:bg-orange-50" type="submit">
                                                        <span class="sm:hidden">Disable</span>
                                                        <span class="hidden sm:inline">Deactivate</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
 </x-app-layout>
