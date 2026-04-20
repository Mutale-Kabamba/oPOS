<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Admin Console</p>
                <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">POS Products</h2>
            </div>
            <a href="{{ route('admin.pos-products.create') }}" class="inline-flex items-center justify-center rounded-lg bg-[#32CD32] px-4 py-2 text-sm font-medium text-[#0B4D2C]">+ Add Product</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">{{ session('status') }}</div>
        @endif

        <section class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            @if ($products->isEmpty())
                <div class="px-6 py-12 text-center">
                    <p class="font-medium text-slate-700">No products yet</p>
                    <p class="mt-1 text-sm text-slate-500">Add your first POS product to get started.</p>
                </div>
            @else
                <div class="table-shell">
                    <table class="table-compact min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">SKU</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Price</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Stock</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Category</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr class="odd:bg-white even:bg-[#F4F7F5] hover:bg-slate-100/70">
                                    <td class="px-4 py-3 font-medium text-slate-700">{{ $product->name }}</td>
                                    <td class="px-4 py-3 text-slate-500">{{ $product->sku ?? '—' }}</td>
                                    <td class="px-4 py-3 font-medium text-slate-900">K {{ number_format($product->price, 2) }}</td>
                                    <td class="px-4 py-3 text-slate-700 {{ $product->stock <= 5 ? 'text-orange-600 font-bold' : '' }}">{{ $product->stock }}</td>
                                    <td class="px-4 py-3 text-slate-500">{{ $product->category ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="actions-compact">
                                            <a href="{{ route('admin.pos-products.edit', $product) }}" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Edit</a>
                                            <form method="POST" action="{{ route('admin.pos-products.toggle', $product) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">
                                                    {{ $product->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.pos-products.destroy', $product) }}" data-confirm-title="Delete product?" data-confirm-message="This action cannot be undone." data-confirm-confirm-text="Delete" data-confirm-variant="danger">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-lg border border-[#FF6B35] px-3 py-1.5 text-xs font-medium text-[#FF6B35] hover:bg-orange-50">Delete</button>
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
