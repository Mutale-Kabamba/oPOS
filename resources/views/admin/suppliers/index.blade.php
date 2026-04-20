<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Payables</p>
                <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">Supplier Management</h2>
            </div>
            <div class="grid w-full grid-cols-2 gap-2 sm:flex sm:w-auto sm:items-center">
                <a href="{{ route('admin.settings') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 sm:w-auto">Back to Settings</a>
                <a href="{{ route('admin.suppliers.create') }}" class="inline-flex items-center justify-center rounded-lg bg-[#32CD32] px-4 py-2 text-sm font-medium text-[#0B4D2C] sm:w-auto">+ New Supplier</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">{{ session('status') }}</div>
        @endif

        <section x-data="{ showTemplateGuide: false }" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <form method="POST" action="{{ route('admin.suppliers.import') }}" enctype="multipart/form-data" class="grid gap-3 sm:grid-cols-[1fr_auto] sm:items-end">
                @csrf
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Bulk Import Suppliers (Excel/CSV)</label>
                    <input type="file" name="import_file" accept=".xlsx,.xls,.csv" class="w-full rounded-lg border-slate-300 text-sm" required>
                    <p class="mt-1 text-xs text-slate-500">Required headers: name. Optional: contact_person, email, phone, address, is_active.</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <a href="{{ route('admin.suppliers.template', ['format' => 'csv']) }}" class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Download CSV Template</a>
                        <a href="{{ route('admin.suppliers.template', ['format' => 'xlsx']) }}" class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Download XLSX Template</a>
                        <button type="button" @click="showTemplateGuide = true" class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Template Guide</button>
                    </div>
                    @error('import_file')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-[#0B4D2C] px-4 py-2 text-sm font-medium text-white">Import</button>
            </form>

            <div x-show="showTemplateGuide" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4" @click.self="showTemplateGuide = false">
                <div class="w-full max-w-lg rounded-xl bg-white p-5 shadow-xl">
                    <div class="flex items-start justify-between">
                        <h3 class="text-base font-semibold text-slate-900">Supplier Template Guide</h3>
                        <button type="button" @click="showTemplateGuide = false" class="rounded-md p-1 text-slate-500 hover:bg-slate-100 hover:text-slate-700">✕</button>
                    </div>
                    <div class="mt-3 space-y-3 text-sm text-slate-700">
                        <p><span class="font-semibold">Required column:</span> name</p>
                        <p><span class="font-semibold">Optional columns:</span> contact_person, email, phone, address, is_active</p>
                        <p><span class="font-semibold">is_active accepted values:</span> true, false, yes, no, 1, 0, active, inactive</p>
                        <p><span class="font-semibold">Tips:</span> keep one header row, avoid duplicate names if you want new rows (same name updates existing supplier).</p>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="button" @click="showTemplateGuide = false" class="inline-flex items-center rounded-lg bg-[#0B4D2C] px-4 py-2 text-sm font-medium text-white">Close</button>
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <form method="GET" class="grid gap-4 md:grid-cols-4">
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Search</label>
                    <input type="text" name="q" value="{{ $q }}" placeholder="Name, contact, email, or phone" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Status</label>
                    <select name="status" class="w-full rounded-lg border-slate-300 text-sm">
                        <option value="all" @selected($status === 'all')>All</option>
                        <option value="active" @selected($status === 'active')>Active</option>
                        <option value="inactive" @selected($status === 'inactive')>Inactive</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-[#0B4D2C] px-4 py-2 text-sm font-medium text-white">Apply</button>
                    <a href="{{ route('admin.suppliers.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">Reset</a>
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            @if ($suppliers->isEmpty())
                <div class="px-6 py-12 text-center">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10M7 12h10M7 17h6"/></svg>
                    </div>
                    <p class="font-medium text-slate-700">No suppliers yet</p>
                    <p class="mt-1 text-sm text-slate-500">Create suppliers so payable transactions can be assigned and aged correctly.</p>
                </div>
            @else
                <div class="table-shell">
                    <table class="table-compact min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Contact</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Phone</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($suppliers as $supplier)
                                <tr class="odd:bg-white even:bg-[#F4F7F5] hover:bg-slate-100/70">
                                    <td class="px-4 py-3 font-medium text-slate-800">{{ $supplier->name }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $supplier->contact_person ?: '—' }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $supplier->email ?: '—' }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $supplier->phone ?: '—' }}</td>
                                    <td class="px-4 py-3">
                                        @if ($supplier->is_active)
                                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">Active</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-200 px-2.5 py-1 text-xs font-medium text-slate-700">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="actions-compact">
                                            <a class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50" href="{{ route('admin.suppliers.edit', $supplier) }}">Edit</a>
                                            <form method="POST" action="{{ route('admin.suppliers.toggle', $supplier) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50" type="submit">{{ $supplier->is_active ? 'Deactivate' : 'Activate' }}</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.suppliers.destroy', $supplier) }}" data-confirm-title="Delete supplier?" data-confirm-message="This action cannot be undone." data-confirm-confirm-text="Delete" data-confirm-variant="danger">
                                                @csrf
                                                @method('DELETE')
                                                <button class="rounded-lg border border-[#FF6B35] px-3 py-1.5 text-xs font-medium text-[#FF6B35] hover:bg-orange-50" type="submit">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $suppliers->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>