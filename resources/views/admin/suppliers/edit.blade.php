<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Payables</p>
                <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">Edit Supplier</h2>
            </div>
            <a href="{{ route('admin.settings') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 sm:w-auto">Back to Settings</a>
        </div>
    </x-slot>

    <div class="max-w-3xl">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.suppliers.update', $supplier) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Name</label>
                    <input class="w-full rounded-lg border-slate-300 text-sm" name="name" value="{{ old('name', $supplier->name) }}" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Contact Person</label>
                    <input class="w-full rounded-lg border-slate-300 text-sm" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}">
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                        <input type="email" class="w-full rounded-lg border-slate-300 text-sm" name="email" value="{{ old('email', $supplier->email) }}">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Phone</label>
                        <input class="w-full rounded-lg border-slate-300 text-sm" name="phone" value="{{ old('phone', $supplier->phone) }}">
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Address</label>
                    <input class="w-full rounded-lg border-slate-300 text-sm" name="address" value="{{ old('address', $supplier->address) }}">
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300" @checked(old('is_active', $supplier->is_active))>
                    Active supplier
                </label>
                <div class="flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-[#32CD32] px-4 py-2 text-sm font-medium text-[#0B4D2C]">Update Supplier</button>
                    <a href="{{ route('admin.suppliers.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>