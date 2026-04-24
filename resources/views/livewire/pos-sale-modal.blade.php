<div>
    @if ($isOpen)
    <div
        x-data="posSaleApp(@js($products))"
        x-cloak
        class="fixed inset-0 z-50 overflow-hidden"
        @keydown.escape.window="$wire.close()"
    >
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black/50 transition-opacity" wire:click="close"></div>

        {{-- Modal Panel --}}
        <div class="fixed inset-0 z-10 flex items-center justify-center p-4 sm:p-6">
        <div class="relative w-full max-w-4xl max-h-[90vh] bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden">
            @if ($receiptData)
                {{-- Receipt View --}}
                <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 sm:px-6">
                    <h2 class="text-lg font-bold text-[#0B4D2C]">Sale Complete</h2>
                    <button wire:click="closeAndRefresh" class="rounded-lg border border-slate-300 p-2 text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="flex-1 flex items-center justify-center p-6 overflow-y-auto">
                    <div class="w-full max-w-md">
                        <div class="text-center mb-6">
                            <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-green-100 mb-4">
                                <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                            </div>
                            <h2 class="text-xl font-bold text-[#0B4D2C]">Sale Completed!</h2>
                            <p class="text-sm text-slate-500 mt-1">{{ $receiptData['sale_number'] }}</p>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-[#F4F7F5] p-4 space-y-3">
                            <div class="text-xs text-slate-500">
                                <p>Date: {{ $receiptData['created_at'] }}</p>
                                <p>Cashier: {{ $receiptData['cashier'] }}</p>
                            </div>

                            <div class="border-t border-dashed border-slate-300 pt-3 space-y-2">
                                @foreach ($receiptData['items'] as $item)
                                <div class="flex justify-between text-sm">
                                    <span>{{ $item['name'] }} &times; {{ $item['quantity'] }}</span>
                                    <span class="font-medium">K {{ number_format($item['subtotal'], 2) }}</span>
                                </div>
                                @endforeach
                            </div>

                            <div class="border-t border-slate-300 pt-3 flex justify-between">
                                <span class="font-bold text-[#0B4D2C]">Total</span>
                                <span class="font-bold text-[#0B4D2C]">K {{ number_format($receiptData['total'], 2) }}</span>
                            </div>

                            <div class="text-xs text-slate-500">
                                <p>Payment: {{ ucwords(str_replace('_', ' ', $receiptData['payment_method'])) }}</p>
                                @if ($receiptData['notes'])
                                <p>Notes: {{ $receiptData['notes'] }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="mt-6 flex gap-3">
                            <button wire:click="closeAndRefresh" class="flex-1 rounded-lg bg-[#32CD32] px-4 py-3 text-sm font-bold text-[#0B4D2C] hover:bg-[#2db82d] transition">
                                Done
                            </button>
                            <a href="{{ route('pos.receipt', ['posSale' => $receiptData['id']]) }}" target="_blank" class="flex-1 rounded-lg border border-slate-300 px-4 py-3 text-sm font-medium text-slate-700 text-center hover:bg-slate-50 transition">
                                Print Receipt
                            </a>
                        </div>
                    </div>
                </div>
            @else
                {{-- POS Interface --}}
                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 sm:px-6">
                    <div>
                        <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Point of Sale</p>
                        <h2 class="text-lg font-bold text-[#0B4D2C]">New Sale</h2>
                    </div>
                    <button wire:click="close" class="rounded-lg border border-slate-300 p-2 text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Error messages --}}
                @if ($errors->any())
                <div class="mx-4 mt-3 sm:mx-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
                @endif

                {{-- Body --}}
                <div class="flex-1 overflow-hidden">
                    <div class="grid h-full lg:grid-cols-5">
                        {{-- Product Grid --}}
                        <div class="lg:col-span-3 overflow-y-auto p-4 sm:p-6 lg:border-r border-slate-200">
                            <div class="mb-4">
                                <input type="search" x-model="search" placeholder="Search products..." class="w-full rounded-lg border-slate-300 text-sm focus:border-[#32CD32] focus:ring-[#32CD32]">
                            </div>

                            <div class="grid grid-cols-3 gap-2 sm:grid-cols-4">
                                <template x-for="product in visibleProducts" :key="product.id">
                                    <button
                                        type="button"
                                        @click="addItem(product.id, product.name, product.price, product.stock)"
                                        class="flex flex-col items-start rounded-lg border border-slate-200 bg-[#F4F7F5] px-2.5 py-2 text-left hover:border-[#32CD32] hover:bg-green-50 transition"
                                    >
                                        <p class="text-xs font-semibold text-[#0B4D2C] leading-tight truncate w-full" x-text="product.name"></p>
                                        <p class="mt-0.5 text-xs font-bold text-slate-900" x-text="'K ' + product.price.toFixed(2)"></p>
                                        <p class="text-[10px] text-slate-400" x-text="'Stock: ' + product.stock"></p>
                                    </button>
                                </template>
                            </div>

                            <div class="mt-3 text-center" x-show="search === '' && filteredProducts.length > 12">
                                <button type="button" @click="showAllProducts = !showAllProducts" class="inline-flex items-center gap-1 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                                    <span x-text="showAllProducts ? 'Show Less' : 'View More (' + (filteredProducts.length - 12) + ' more)'"></span>
                                    <svg :class="showAllProducts && 'rotate-180'" class="h-4 w-4 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                                </button>
                            </div>

                            <div class="py-6 text-center" x-show="visibleProducts.length === 0" x-cloak>
                                <p class="text-sm font-medium text-slate-500" x-text="search ? 'No products match &quot;' + search + '&quot;' : 'No products available'"></p>
                            </div>
                        </div>

                        {{-- Cart --}}
                        <div class="lg:col-span-2 flex flex-col overflow-hidden bg-white">
                            <div class="border-b border-slate-200 px-4 py-3 sm:px-6">
                                <h3 class="text-sm font-bold text-[#0B4D2C]">Cart</h3>
                            </div>

                            <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-3">
                                <template x-if="cart.length === 0">
                                    <p class="text-sm text-slate-500 text-center py-4">No items in cart</p>
                                </template>

                                <template x-for="(item, index) in cart" :key="item.product_id">
                                    <div class="flex items-center justify-between gap-2 rounded-lg bg-[#F4F7F5] p-3">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-slate-700 truncate" x-text="item.name"></p>
                                            <p class="text-xs text-slate-500">K <span x-text="item.price.toFixed(2)"></span> each</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button type="button" @click="decrementItem(index)" class="flex h-7 w-7 items-center justify-center rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-100">&minus;</button>
                                            <span class="w-8 text-center text-sm font-bold" x-text="item.quantity"></span>
                                            <button type="button" @click="incrementItem(index)" class="flex h-7 w-7 items-center justify-center rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-100">+</button>
                                            <button type="button" @click="removeItem(index)" class="flex h-7 w-7 items-center justify-center rounded-lg border border-red-300 text-red-500 hover:bg-red-50">&times;</button>
                                        </div>
                                        <p class="text-sm font-bold text-[#0B4D2C] w-20 text-right">K <span x-text="(item.price * item.quantity).toFixed(2)"></span></p>
                                    </div>
                                </template>
                            </div>

                            <div class="border-t border-slate-200 p-4 sm:p-6 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-bold text-slate-700">Total</span>
                                    <span class="text-xl font-bold text-[#0B4D2C]">K <span x-text="cartTotal.toFixed(2)"></span></span>
                                </div>

                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-700">Payment Method</label>
                                    <select x-model="paymentMethod" class="w-full rounded-lg border-slate-300 text-sm">
                                        <option value="cash">Cash</option>
                                        <option value="card">Card</option>
                                        <option value="mobile_money">Mobile Money</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-700">Notes (optional)</label>
                                    <input type="text" x-model="notes" class="w-full rounded-lg border-slate-300 text-sm" placeholder="Customer name, reference...">
                                </div>

                                <button
                                    type="button"
                                    @click="submitSale()"
                                    :disabled="cart.length === 0 || submitting"
                                    class="w-full rounded-lg bg-[#32CD32] px-4 py-3 text-sm font-bold text-[#0B4D2C] disabled:opacity-50 disabled:cursor-not-allowed hover:bg-[#2db82d] transition"
                                >
                                    <span x-show="!submitting">Complete Sale</span>
                                    <span x-show="submitting">Processing...</span>
                                </button>

                                <button type="button" @click="clearCart()" x-show="cart.length > 0" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                    Clear Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        </div>
    </div>
    @endif

    <script>
        function posSaleApp(products) {
            return {
                allProducts: products,
                cart: [],
                search: '',
                showAllProducts: false,
                paymentMethod: 'cash',
                notes: '',
                submitting: false,

                get filteredProducts() {
                    if (this.search === '') return this.allProducts;
                    const q = this.search.toLowerCase();
                    return this.allProducts.filter(p => p.name.toLowerCase().includes(q) || (p.sku && p.sku.toLowerCase().includes(q)));
                },

                get visibleProducts() {
                    const filtered = this.filteredProducts;
                    if (this.search !== '' || this.showAllProducts) return filtered;
                    return filtered.slice(0, 12);
                },

                get cartTotal() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                },

                addItem(productId, name, price, stock) {
                    const existing = this.cart.find(i => i.product_id === productId);
                    if (existing) {
                        if (existing.quantity < stock) existing.quantity++;
                    } else {
                        this.cart.push({ product_id: productId, name, price, quantity: 1, stock });
                    }
                },

                incrementItem(index) {
                    const item = this.cart[index];
                    if (item.quantity < item.stock) item.quantity++;
                },

                decrementItem(index) {
                    if (this.cart[index].quantity > 1) this.cart[index].quantity--;
                    else this.cart.splice(index, 1);
                },

                removeItem(index) {
                    this.cart.splice(index, 1);
                },

                clearCart() {
                    this.cart = [];
                },

                async submitSale() {
                    if (this.cart.length === 0 || this.submitting) return;
                    this.submitting = true;

                    const items = this.cart.map(item => ({
                        product_id: item.product_id,
                        quantity: item.quantity,
                    }));

                    await this.$wire.completeSale(items, this.paymentMethod, this.notes || null);
                    this.submitting = false;
                },
            };
        }
    </script>
</div>
