<div class="h-screen flex flex-col bg-gray-100">
    <div class="flex-1 flex overflow-hidden">
        <!-- Products Section -->
        <div class="flex-1 overflow-y-auto p-4">
            <!-- Search -->
            <div class="mb-4">
                <x-ui.input 
                    type="text" 
                    wire:model.live="search" 
                    placeholder="Cari produk atau scan barcode..." 
                    icon="magnifying-glass"
                />
            </div>

            <!-- Products Grid -->
            <x-layout.grid cols="2" class="md:grid-cols-3 lg:grid-cols-4">
                @foreach($products as $product)
                    <button wire:click="addToCart({{ $product->id }})" 
                            class="bg-white rounded-lg p-4 hover:shadow-lg transition-shadow text-left border border-gray-200">
                        <div class="aspect-square bg-gray-200 rounded-lg mb-2 flex items-center justify-center">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-lg">
                            @else
                                <x-ui.icon name="cube" class="w-12 h-12 text-gray-400" />
                            @endif
                        </div>
                        <h3 class="font-medium text-gray-900 text-sm mb-1 truncate">{{ $product->name }}</h3>
                        <p class="text-primary-600 font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">Stok: {{ $product->stock }}</p>
                    </button>
                @endforeach
            </x-layout.grid>
        </div>

        <!-- Cart Section -->
        <div class="w-96 bg-white border-l border-gray-200 flex flex-col">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Keranjang</h2>
            </div>

            <!-- Cart Items -->
            <div class="flex-1 overflow-y-auto p-4 space-y-3">
                @forelse($cart as $key => $item)
                    <x-ui.card padding="true" shadow="sm" class="bg-gray-50">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-medium text-gray-900 text-sm flex-1">{{ $item['name'] }}</h3>
                            <button wire:click="removeFromCart('{{ $key }}')" class="text-danger-600 hover:text-danger-800">
                                <x-ui.icon name="x-mark" class="w-5 h-5" />
                            </button>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <button wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] - 1 }})" 
                                        class="w-8 h-8 rounded bg-gray-200 hover:bg-gray-300 flex items-center justify-center transition-colors">
                                    <x-ui.icon name="minus" class="w-4 h-4" />
                                </button>
                                <span class="w-12 text-center font-medium">{{ $item['quantity'] }}</span>
                                <button wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] + 1 }})" 
                                        class="w-8 h-8 rounded bg-gray-200 hover:bg-gray-300 flex items-center justify-center transition-colors">
                                    <x-ui.icon name="plus" class="w-4 h-4" />
                                </button>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-600">@ Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                                <div class="font-bold text-gray-900">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</div>
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

            <!-- Summary & Payment -->
            @if(!empty($cart))
                <div class="border-t border-gray-200 p-4 space-y-4">
                    <!-- Totals -->
                    <div class="space-y-2">
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total</span>
                            <span class="text-primary-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <x-ui.select 
                        wire:model="paymentMethod" 
                        label="Metode Pembayaran"
                        :options="[
                            'cash' => 'Tunai',
                            'transfer' => 'Transfer',
                            'qris' => 'QRIS'
                        ]"
                    />

                    <!-- Payment Amount -->
                    <x-ui.input 
                        type="number" 
                        wire:model.live="paymentAmount" 
                        label="Jumlah Bayar"
                        class="text-lg font-bold"
                    />

                    <!-- Change -->
                    @if($paymentAmount > 0)
                        <div class="bg-success-50 rounded-lg p-3 border border-success-200">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-success-800">Kembalian</span>
                                <span class="text-xl font-bold text-success-600">Rp {{ number_format($change, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Process Button -->
                    <x-ui.button 
                        wire:click="processPayment" 
                        variant="primary" 
                        size="lg"
                        class="w-full"
                        :disabled="$paymentAmount < $total"
                        icon="check-circle"
                    >
                        Proses Pembayaran
                    </x-ui.button>
                </div>
            @endif
        </div>
    </div>
</div>
