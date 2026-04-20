<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Admin Console</p>
                <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">Add Product</h2>
            </div>
            <a href="{{ route('admin.pos-products.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">Back to Products</a>
        </div>
    </x-slot>

    <div class="max-w-3xl">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.pos-products.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Product Name</label>
                    <input class="w-full rounded-lg border-slate-300 text-sm" name="name" value="{{ old('name') }}" required>
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">SKU (optional)</label>
                        <input class="w-full rounded-lg border-slate-300 text-sm" name="sku" value="{{ old('sku') }}">
                        @error('sku') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Category (optional)</label>
                        <input class="w-full rounded-lg border-slate-300 text-sm" name="category" value="{{ old('category') }}">
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Price (K)</label>
                        <input type="number" step="0.01" min="0.01" class="w-full rounded-lg border-slate-300 text-sm" name="price" value="{{ old('price') }}" required>
                        @error('price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Stock</label>
                        <input type="number" min="0" class="w-full rounded-lg border-slate-300 text-sm" name="stock" value="{{ old('stock', 0) }}" required>
                        @error('stock') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-[#32CD32] focus:ring-[#32CD32]">
                    Active
                </label>
                <div class="flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-[#32CD32] px-4 py-2 text-sm font-medium text-[#0B4D2C]">Save Product</button>
                    <a href="{{ route('admin.pos-products.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
