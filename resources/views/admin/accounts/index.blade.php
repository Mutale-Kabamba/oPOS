<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Bookkeeping Setup</p>
                <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">Chart of Accounts</h2>
            </div>
            <div class="grid w-full grid-cols-2 gap-2 sm:flex sm:w-auto sm:items-center">
                <a href="{{ route('admin.settings') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 sm:w-auto">Back to Settings</a>
                <a href="{{ route('admin.accounts.create') }}" class="inline-flex items-center justify-center rounded-lg bg-[#32CD32] px-4 py-2 text-sm font-medium text-[#0B4D2C] sm:w-auto">
                    <span class="sm:hidden">+ Acc</span>
                    <span class="hidden sm:inline">+ New Account</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">{{ session('status') }}</div>
        @endif

        <section class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            @if ($accounts->isEmpty())
                <div class="px-6 py-12 text-center text-slate-500">No accounts found.</div>
            @else
                <div class="table-shell">
                    <table class="table-compact min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Code</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Group</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($accounts as $account)
                                <tr class="odd:bg-white even:bg-[#F4F7F5] hover:bg-slate-100/70">
                                    <td class="px-4 py-3 font-medium text-slate-800">{{ $account->code }}</td>
                                    <td class="cell-truncate px-4 py-3 text-slate-700">{{ $account->name }}</td>
                                    <td class="px-4 py-3 text-slate-700 uppercase">{{ $account->type }}</td>
                                    <td class="cell-truncate px-4 py-3 text-slate-700">{{ str_replace('_', ' ', ucwords($account->group_name, '_')) }}</td>
                                    <td class="px-4 py-3">
                                        @if ($account->is_active)
                                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">Active</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-orange-100 px-2.5 py-1 text-xs font-medium text-orange-700">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="actions-compact">
                                            <a href="{{ route('admin.accounts.edit', $account) }}" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Edit</a>
                                            <form method="POST" action="{{ route('admin.accounts.destroy', $account) }}" data-confirm-title="Delete account?" data-confirm-message="This action cannot be undone." data-confirm-confirm-text="Delete" data-confirm-variant="danger">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-lg border border-red-300 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50">Delete</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.accounts.toggle', $account) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="rounded-lg border border-[#FF6B35] px-3 py-1.5 text-xs font-medium text-[#FF6B35] hover:bg-orange-50">
                                                    <span class="sm:hidden">{{ $account->is_active ? 'Disable' : 'Enable' }}</span>
                                                    <span class="hidden sm:inline">{{ $account->is_active ? 'Deactivate' : 'Activate' }}</span>
                                                </button>
                                            </form>
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
