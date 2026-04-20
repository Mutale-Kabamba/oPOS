<?php

namespace App\Http\Controllers;

use App\Models\PosProduct;
use App\Models\PosSale;
use App\Models\PosSaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosSaleController extends Controller
{
    public function create()
    {
        $products = PosProduct::where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        return view('pos.sell', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:pos_products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'payment_method' => ['required', 'in:cash,card,mobile_money'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        return DB::transaction(function () use ($validated) {
            $sale = PosSale::create([
                'sale_number' => PosSale::generateSaleNumber(),
                'user_id' => auth()->id(),
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
                'total' => 0,
            ]);

            $total = 0;

            foreach ($validated['items'] as $item) {
                $product = PosProduct::findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw new \RuntimeException("Insufficient stock for {$product->name}. Available: {$product->stock}");
                }

                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                PosSaleItem::create([
                    'pos_sale_id' => $sale->id,
                    'pos_product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal,
                ]);

                $product->decrement('stock', $item['quantity']);
            }

            $sale->update(['total' => $total]);

            return redirect()->route('pos.receipt', $sale)->with('status', 'Sale completed successfully.');
        });
    }

    public function index(Request $request)
    {
        $query = PosSale::where('user_id', auth()->id())
            ->with('items.product')
            ->latest();

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $sales = $query->paginate(20);

        return view('pos.sales.index', compact('sales'));
    }

    public function receipt(PosSale $posSale)
    {
        if ($posSale->user_id !== auth()->id()) {
            abort(403);
        }

        $posSale->load('items.product', 'user');

        return view('pos.sales.receipt', ['sale' => $posSale]);
    }
}
