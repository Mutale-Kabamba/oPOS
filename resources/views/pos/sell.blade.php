<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Point of Sale</p>
                <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">New Sale</h2>
            </div>
            <a href="{{ route('pos.dashboard') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">Back to Dashboard</a>
        </div>
    </x-slot>

    <div x-data="posApp()" class="space-y-6">
        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-5">
            {{-- Product Grid --}}
            <div class="lg:col-span-3 space-y-4">
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
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
                        <p class="text-sm font-medium text-slate-500" x-text="search ? 'No products match \"' + search + '\"' : 'No products available'"></p>
                    </div>
                </div>
            </div>

            {{-- Cart --}}
            <div class="lg:col-span-2">
                <div class="rounded-xl border border-slate-200 bg-white shadow-sm sticky top-24">
                    <div class="border-b border-slate-200 px-4 py-3">
                        <h3 class="text-sm font-bold text-[#0B4D2C]">Cart</h3>
                    </div>

                    <div class="p-4 space-y-3 max-h-[40vh] overflow-y-auto">
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
                                    <button type="button" @click="decrementItem(index)" class="flex h-7 w-7 items-center justify-center rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-100">−</button>
                                    <span class="w-8 text-center text-sm font-bold" x-text="item.quantity"></span>
                                    <button type="button" @click="incrementItem(index)" class="flex h-7 w-7 items-center justify-center rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-100">+</button>
                                    <button type="button" @click="removeItem(index)" class="flex h-7 w-7 items-center justify-center rounded-lg border border-red-300 text-red-500 hover:bg-red-50">×</button>
                                </div>
                                <p class="text-sm font-bold text-[#0B4D2C] w-20 text-right">K <span x-text="(item.price * item.quantity).toFixed(2)"></span></p>
                            </div>
                        </template>
                    </div>

                    <div class="border-t border-slate-200 p-4 space-y-4">
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

                        <form method="POST" action="{{ route('pos.sales.store') }}" @submit="prepareSubmit($event)">
                            @csrf
                            <input type="hidden" name="payment_method" :value="paymentMethod">
                            <input type="hidden" name="notes" :value="notes">
                            <template x-for="(item, index) in cart" :key="item.product_id">
                                <div>
                                    <input type="hidden" :name="'items['+index+'][product_id]'" :value="item.product_id">
                                    <input type="hidden" :name="'items['+index+'][quantity]'" :value="item.quantity">
                                </div>
                            </template>
                            <button
                                type="submit"
                                :disabled="cart.length === 0"
                                class="w-full rounded-lg bg-[#32CD32] px-4 py-3 text-sm font-bold text-[#0B4D2C] disabled:opacity-50 disabled:cursor-not-allowed hover:bg-[#2db82d] transition"
                            >
                                Complete Sale
                            </button>
                        </form>

                        <button type="button" @click="clearCart()" x-show="cart.length > 0" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                            Clear Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $productsJson = $products->map(fn ($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'sku' => $p->sku ?? '',
            'price' => (float) $p->price,
            'stock' => $p->stock,
        ]);
    @endphp
    <script>
        function posApp() {
            return {
                allProducts: @json($productsJson),
                cart: [],
                search: '',
                showAllProducts: false,
                paymentMethod: 'cash',
                notes: '',

                get filteredProducts() {
                    if (this.search === '') return this.allProducts;
                    const q = this.search.toLowerCase();
                    return this.allProducts.filter(p => p.name.toLowerCase().includes(q) || p.sku.toLowerCase().includes(q));
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
                        if (existing.quantity < stock) {
                            existing.quantity++;
                        }
                    } else {
                        this.cart.push({ product_id: productId, name, price, quantity: 1, stock });
                    }
                },

                incrementItem(index) {
                    const item = this.cart[index];
                    if (item.quantity < item.stock) {
                        item.quantity++;
                    }
                },

                decrementItem(index) {
                    if (this.cart[index].quantity > 1) {
                        this.cart[index].quantity--;
                    } else {
                        this.cart.splice(index, 1);
                    }
                },

                removeItem(index) {
                    this.cart.splice(index, 1);
                },

                clearCart() {
                    this.cart = [];
                },

                prepareSubmit(event) {
                    if (this.cart.length === 0) {
                        event.preventDefault();
                    }
                }
            };
        }
    </script>
</x-app-layout>
