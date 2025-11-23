<div class="h-screen bg-gray-100 flex flex-col">
    {{-- Flash Messages --}}
    @if (session()->has('success'))
    <div class="fixed top-4 right-4 z-50 max-w-md">
        <x-ui.alert variant="success" dismissible="true">
            {{ session('success') }}
        </x-ui.alert>
    </div>
    @endif

    @if (session()->has('error'))
    <div class="fixed top-4 right-4 z-50 max-w-md">
        <x-ui.alert variant="danger" dismissible="true">
            {{ session('error') }}
        </x-ui.alert>
    </div>
    @endif

    <div class="flex-1 flex overflow-hidden">
        {{-- Left Panel - Product Search & Selection --}}
        <div class="flex-1 flex flex-col bg-white">
            {{-- Header --}}
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Point of Sale</h2>
                <p class="text-sm text-gray-600">Kasir: {{ auth()->user()->name }}</p>
            </div>

            {{-- Product Search --}}
            <div class="p-4 border-b border-gray-200">
                <x-ui.input 
                    wire:model.live.debounce.300ms="searchProduct" 
                    type="text" 
                    placeholder="Cari produk (nama atau SKU)..."
                    icon="magnifying-glass"
                />
            </div>

            {{-- Product List --}}
            <div class="flex-1 overflow-y-auto p-4">
                @if($searchResults->count() > 0)
                    <x-layout.grid cols="2" class="sm:grid-cols-3 md:grid-cols-4">
                        @foreach($searchResults as $product)
                        <button wire:click="addToCart({{ $product->id }})" 
                                @class([
                                    'p-4 rounded-lg border-2 text-left transition hover:shadow-md',
                                    'border-gray-200 hover:border-primary-500' => !$product->isLowStock(),
                                    'border-warning-300 bg-warning-50' => $product->isLowStock(),
                                ])>
                            <h3 class="font-semibold text-gray-900 text-sm mb-1 truncate">
                                {{ $product->name }}
                            </h3>
                            <p class="text-lg font-bold text-primary-600 mb-2">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                            <div class="flex items-center justify-between text-xs">
                                <x-ui.badge 
                                    :variant="$product->stock > $product->min_stock ? 'success' : 'warning'" 
                                    size="sm"
                                >
                                    Stok: {{ $product->stock }}
                                </x-ui.badge>
                            </div>
                        </button>
                        @endforeach
                    </x-layout.grid>
                @else
                    <x-layout.empty-state 
                        icon="magnifying-glass" 
                        :title="$searchProduct ? 'Produk tidak ditemukan' : 'Ketik untuk mencari produk'"
                    />
                @endif
            </div>
        </div>

        {{-- Right Panel - Shopping Cart --}}
        <div class="w-96 bg-gray-50 border-l border-gray-200 flex flex-col">
            {{-- Cart Header --}}
            <div class="p-4 bg-primary-600 text-white">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold">Keranjang</h3>
                    @if(count($cart) > 0)
                    <x-ui.button 
                        wire:click="clearCart" 
                        wire:confirm="Kosongkan keranjang?"
                        variant="ghost"
                        size="sm"
                        class="text-white hover:bg-white/20"
                    >
                        Kosongkan
                    </x-ui.button>
                    @endif
                </div>
                <p class="text-sm text-primary-100 mt-1">{{ count($cart) }} item</p>
            </div>

            {{-- Cart Items --}}
            <div class="flex-1 overflow-y-auto p-4 space-y-3">
                @forelse($cart as $key => $item)
                <x-ui.card shadow="sm">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 text-sm">{{ $item['name'] }}</h4>
                            <p class="text-sm text-gray-600">
                                Rp {{ number_format($item['price'], 0, ',', '.') }}
                            </p>
                        </div>
                        <button wire:click="removeFromCart('{{ $key }}')" 
                                class="text-danger-500 hover:text-danger-700 p-1">
                            <x-ui.icon name="trash" class="w-5 h-5" />
                        </button>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <button wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] - 1 }})" 
                                    class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded flex items-center justify-center transition">
                                <x-ui.icon name="minus" class="w-4 h-4" />
                            </button>
                            <input type="number" 
                                   wire:model.blur="cart.{{ $key }}.quantity"
                                   wire:change="updateQuantity('{{ $key }}', $event.target.value)"
                                   min="1"
                                   max="{{ $item['stock'] }}"
                                   class="w-16 text-center border border-gray-300 rounded py-1">
                            <button wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] + 1 }})" 
                                    @if($item['quantity'] >= $item['stock']) disabled @endif
                                    class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded flex items-center justify-center transition disabled:opacity-50 disabled:cursor-not-allowed">
                                <x-ui.icon name="plus" class="w-4 h-4" />
                            </button>
                        </div>
                        <div class="font-bold text-primary-600">
                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                        </div>
                    </div>
                </x-ui.card>
                @empty
                    <x-layout.empty-state 
                        icon="shopping-cart" 
                        title="Keranjang kosong"
                        description="Pilih produk untuk memulai transaksi"
                    />
                @endforelse
            </div>

            {{-- Cart Summary & Checkout --}}
            <div class="p-4 bg-white border-t border-gray-200 space-y-4">
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-lg">
                        <span class="font-semibold text-gray-700">Total:</span>
                        <span class="text-2xl font-bold text-primary-600">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <x-ui.button 
                    wire:click="openPaymentModal" 
                    variant="primary"
                    size="lg"
                    class="w-full"
                    :disabled="count($cart) === 0"
                    icon="credit-card"
                >
                    Proses Pembayaran
                </x-ui.button>
            </div>
        </div>
    </div>

    {{-- Payment Modal --}}
    @if($showPaymentModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showPaymentModal') }">
        <!-- Backdrop -->
        <div 
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500 bg-opacity-75"
            @click="$wire.set('showPaymentModal', false)"
        ></div>

        <!-- Modal -->
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 transform translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 transform translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle w-full max-w-md"
            >
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Proses Pembayaran</h3>
                    <button 
                        @click="$wire.set('showPaymentModal', false)"
                        type="button"
                        class="text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-primary-500 rounded-lg p-1"
                    >
                        <x-ui.icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <!-- Body -->
                <div class="px-6 py-4">
        {{-- Payment Method --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">Metode Pembayaran</label>
            <div class="grid grid-cols-3 gap-3">
                <button wire:click="$set('paymentMethod', 'cash')" 
                        @class([
                            'py-3 px-4 border-2 rounded-lg font-medium transition',
                            'border-primary-600 bg-primary-50 text-primary-600' => $paymentMethod === 'cash',
                            'border-gray-300 text-gray-700 hover:border-gray-400' => $paymentMethod !== 'cash',
                        ])>
                    Cash
                </button>
                <button wire:click="$set('paymentMethod', 'transfer')" 
                        @class([
                            'py-3 px-4 border-2 rounded-lg font-medium transition',
                            'border-primary-600 bg-primary-50 text-primary-600' => $paymentMethod === 'transfer',
                            'border-gray-300 text-gray-700 hover:border-gray-400' => $paymentMethod !== 'transfer',
                        ])>
                    Transfer
                </button>
                <button wire:click="$set('paymentMethod', 'qris')" 
                        @class([
                            'py-3 px-4 border-2 rounded-lg font-medium transition',
                            'border-primary-600 bg-primary-50 text-primary-600' => $paymentMethod === 'qris',
                            'border-gray-300 text-gray-700 hover:border-gray-400' => $paymentMethod !== 'qris',
                        ])>
                    QRIS
                </button>
            </div>
            @error('paymentMethod') <span class="text-danger-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Total --}}
        <div class="bg-gray-50 rounded-lg p-4 mb-6 border border-gray-200">
            <div class="flex items-center justify-between text-lg font-semibold">
                <span class="text-gray-700">Total Bayar:</span>
                <span class="text-2xl text-primary-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Payment Amount --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Jumlah Dibayar
            </label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                <input wire:model.live="paymentAmount" 
                       type="number" 
                       min="0"
                       step="1000"
                       class="w-full pl-12 pr-4 py-3 text-lg font-semibold border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            @error('paymentAmount') <span class="text-danger-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Change --}}
        @if($paymentMethod === 'cash' && $paymentAmount > 0)
        <div class="bg-success-50 border border-success-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <span class="text-success-800 font-medium">Kembalian:</span>
                <span class="text-2xl font-bold text-success-600">
                    Rp {{ number_format($change, 0, ',', '.') }}
                </span>
            </div>
        </div>
        @endif

        {{-- Notes --}}
        <x-ui.textarea 
            wire:model="notes" 
            label="Catatan (Opsional)"
            rows="2" 
            placeholder="Tambahkan catatan..."
        />
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-3">
                    <x-ui.button 
                        wire:click="$set('showPaymentModal', false)" 
                        variant="white"
                    >
                        Batal
                    </x-ui.button>
                    <x-ui.button 
                        wire:click="processPayment" 
                        variant="primary"
                    >
                        <span wire:loading.remove wire:target="processPayment">Bayar</span>
                        <span wire:loading wire:target="processPayment">Memproses...</span>
                    </x-ui.button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Loading Overlay --}}
    <div wire:loading.flex class="fixed inset-0 bg-gray-900/50 flex items-center justify-center z-40">
        <x-ui.card shadow="lg" class="text-center">
            <x-ui.spinner size="lg" color="primary" class="mx-auto" />
            <p class="mt-4 text-gray-700 font-medium">Memproses...</p>
        </x-ui.card>
    </div>
</div>
