<?php

namespace App\Http\Controllers;

use App\Models\PosProduct;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PosProductController extends Controller
{
    public function index()
    {
        $products = PosProduct::orderBy('name')->get();

        return view('admin.pos-products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.pos-products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:pos_products,sku'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'category' => ['nullable', 'string', 'max:255'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        PosProduct::create($validated);

        return redirect()->route('admin.pos-products.index')->with('status', 'Product created.');
    }

    public function edit(PosProduct $posProduct)
    {
        return view('admin.pos-products.edit', ['product' => $posProduct]);
    }

    public function update(Request $request, PosProduct $posProduct)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('pos_products', 'sku')->ignore($posProduct->id)],
            'price' => ['required', 'numeric', 'min:0.01'],
            'category' => ['nullable', 'string', 'max:255'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        $posProduct->update($validated);

        return redirect()->route('admin.pos-products.index')->with('status', 'Product updated.');
    }

    public function destroy(PosProduct $posProduct)
    {
        $posProduct->delete();

        return redirect()->route('admin.pos-products.index')->with('status', 'Product deleted.');
    }

    public function toggle(PosProduct $posProduct)
    {
        $posProduct->update(['is_active' => ! $posProduct->is_active]);

        return redirect()->route('admin.pos-products.index')->with('status', 'Product status updated.');
    }
}
