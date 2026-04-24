<?php

namespace App\Livewire;

use App\Models\PosProduct;
use App\Models\PosSale;
use App\Models\PosSaleItem;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class PosSaleModal extends Component
{
    public bool $isOpen = false;
    public ?array $receiptData = null;

    #[On('openNewSale')]
    public function open(): void
    {
        $this->isOpen = true;
        $this->receiptData = null;
        $this->resetErrorBag();
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->receiptData = null;
    }

    public function closeAndRefresh(): void
    {
        $this->isOpen = false;
        $this->receiptData = null;
        $this->dispatch('sale-completed');
    }

    public function completeSale(array $items, string $paymentMethod, ?string $notes): void
    {
        if (empty($items)) {
            $this->addError('sale', 'No items in cart.');
            return;
        }

        if (! in_array($paymentMethod, ['cash', 'card', 'mobile_money'])) {
            $this->addError('sale', 'Invalid payment method.');
            return;
        }

        if ($notes && strlen($notes) > 500) {
            $this->addError('sale', 'Notes must not exceed 500 characters.');
            return;
        }

        try {
            $sale = DB::transaction(function () use ($items, $paymentMethod, $notes) {
                $sale = PosSale::create([
                    'sale_number' => PosSale::generateSaleNumber(),
                    'user_id' => auth()->id(),
                    'payment_method' => $paymentMethod,
                    'notes' => $notes,
                    'total' => 0,
                ]);

                $total = 0;

                foreach ($items as $item) {
                    $productId = (int) $item['product_id'];
                    $quantity = (int) $item['quantity'];

                    if ($quantity < 1) {
                        throw new \RuntimeException('Invalid quantity.');
                    }

                    $product = PosProduct::findOrFail($productId);

                    if ($product->stock < $quantity) {
                        throw new \RuntimeException("Insufficient stock for {$product->name}. Available: {$product->stock}");
                    }

                    $subtotal = $product->price * $quantity;
                    $total += $subtotal;

                    PosSaleItem::create([
                        'pos_sale_id' => $sale->id,
                        'pos_product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $product->price,
                        'subtotal' => $subtotal,
                    ]);

                    $product->decrement('stock', $quantity);
                }

                $sale->update(['total' => $total]);

                return $sale;
            });

            $sale->load('items.product', 'user');

            $this->receiptData = [
                'id' => $sale->id,
                'sale_number' => $sale->sale_number,
                'total' => $sale->total,
                'payment_method' => $sale->payment_method,
                'items' => $sale->items->map(fn ($item) => [
                    'name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->subtotal,
                ])->toArray(),
                'created_at' => $sale->created_at->format('d M Y H:i'),
                'notes' => $sale->notes,
                'cashier' => $sale->user->name,
            ];
        } catch (\RuntimeException $e) {
            $this->addError('sale', $e->getMessage());
        }
    }

    public function render()
    {
        $products = [];

        if ($this->isOpen && ! $this->receiptData) {
            $products = PosProduct::where('is_active', true)
                ->where('stock', '>', 0)
                ->orderBy('name')
                ->get()
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'sku' => $p->sku ?? '',
                    'price' => (float) $p->price,
                    'stock' => $p->stock,
                ])
                ->toArray();
        }

        return view('livewire.pos-sale-modal', [
            'products' => $products,
        ]);
    }
}
