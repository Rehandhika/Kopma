<div class="space-y-6">
    <!-- Header -->
    <x-layout.page-header 
        title="Manajemen Stok"
        description="Kelola dan monitor stok produk"
    >
        <x-slot:actions>
            <x-ui.button 
                variant="secondary" 
                href="{{ route('admin.products.create') }}"
                icon="plus"
            >
                Tambah Produk
            </x-ui.button>
            <x-ui.button 
                variant="primary" 
                href="{{ route('admin.stock.adjustment') }}"
                icon="adjustments"
            >
                Penyesuaian Stok
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    <!-- Stats -->
    <x-layout.grid cols="4" gap="4">
        <x-layout.stat-card
            label="Total Produk"
            :value="$stats['total_products']"
            icon="cube"
            iconColor="bg-primary-100"
            iconTextColor="text-primary-600"
        />
        <x-layout.stat-card
            label="Nilai Modal Stok"
            :value="'Rp ' . number_format($stats['total_stock_cost'], 0, ',', '.')"
            icon="banknotes"
            iconColor="bg-gray-100"
            iconTextColor="text-gray-600"
        />
        <x-layout.stat-card
            label="Nilai Jual Stok"
            :value="'Rp ' . number_format($stats['total_stock_value'], 0, ',', '.')"
            icon="currency-dollar"
            iconColor="bg-blue-100"
            iconTextColor="text-blue-600"
        />
        <x-layout.stat-card
            label="Potensi Keuntungan"
            :value="'Rp ' . number_format($stats['potential_profit'], 0, ',', '.')"
            icon="arrow-trending-up"
            iconColor="bg-success-100"
            iconTextColor="text-success-600"
        />
    </x-layout.grid>

    <!-- Alert Stats -->
    <x-layout.grid cols="2" gap="4">
        <x-ui.card padding="true" class="border-l-4 border-warning-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Stok Rendah</p>
                    <p class="text-2xl font-bold text-warning-600">{{ $stats['low_stock'] }} produk</p>
                </div>
                <div class="p-3 bg-warning-100 rounded-full">
                    <svg class="w-6 h-6 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </x-ui.card>
        <x-ui.card padding="true" class="border-l-4 border-danger-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Stok Habis</p>
                    <p class="text-2xl font-bold text-danger-600">{{ $stats['out_of_stock'] }} produk</p>
                </div>
                <div class="p-3 bg-danger-100 rounded-full">
                    <svg class="w-6 h-6 text-danger-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </x-ui.card>
    </x-layout.grid>

    <!-- Filters -->
    <x-ui.card padding="true">
        <x-layout.grid cols="2" gap="4">
            <x-ui.input 
                type="text" 
                wire:model.live="search" 
                placeholder="Cari produk..." 
                icon="search"
            />
            <x-ui.select wire:model.live="stockFilter">
                <option value="all">Semua Stok</option>
                <option value="low">Stok Rendah</option>
                <option value="out">Stok Habis</option>
            </x-ui.select>
        </x-layout.grid>
    </x-ui.card>

    <!-- Stock Table -->
    <x-data.table 
        :headers="['Produk', 'SKU', 'Stok', 'Harga Modal', 'Harga Jual', 'Margin', 'Nilai Stok', 'Status']"
        striped="true"
        hoverable="true"
    >
        @forelse($products as $product)
            <x-data.table-row>
                <x-data.table-cell>
                    <div class="flex items-center">
                        <x-ui.product-image 
                            :src="$product->image_thumbnail_url" 
                            :alt="$product->name"
                            size="w-10 h-10"
                            rounded="md"
                        />
                        <div class="ml-3">
                            <div class="font-medium text-gray-900">{{ $product->name }}</div>
                            <div class="text-xs text-gray-500">{{ $product->category ?? '-' }}</div>
                        </div>
                    </div>
                </x-data.table-cell>
                <x-data.table-cell>
                    <span class="text-sm font-mono text-gray-600">{{ $product->sku ?? '-' }}</span>
                </x-data.table-cell>
                <x-data.table-cell>
                    <div class="text-center">
                        <span @class([
                            'text-lg font-bold',
                            'text-danger-600' => $product->stock <= $product->min_stock,
                            'text-gray-900' => $product->stock > $product->min_stock,
                        ])>
                            {{ $product->stock }}
                        </span>
                        <div class="text-xs text-gray-500">Min: {{ $product->min_stock }}</div>
                    </div>
                </x-data.table-cell>
                <x-data.table-cell>
                    <span class="text-gray-600">Rp {{ number_format($product->cost_price, 0, ',', '.') }}</span>
                </x-data.table-cell>
                <x-data.table-cell>
                    <span class="font-medium">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                </x-data.table-cell>
                <x-data.table-cell>
                    @php
                        $margin = $product->profit_margin;
                    @endphp
                    <span @class([
                        'text-sm font-semibold px-2 py-1 rounded',
                        'bg-green-100 text-green-700' => $margin >= 20,
                        'bg-yellow-100 text-yellow-700' => $margin >= 10 && $margin < 20,
                        'bg-red-100 text-red-700' => $margin < 10,
                    ])>
                        {{ $margin }}%
                    </span>
                </x-data.table-cell>
                <x-data.table-cell>
                    <div>
                        <div class="font-medium">Rp {{ number_format($product->total_stock_value, 0, ',', '.') }}</div>
                        <div class="text-xs text-green-600">
                            +Rp {{ number_format($product->potential_profit, 0, ',', '.') }} potensi
                        </div>
                    </div>
                </x-data.table-cell>
                <x-data.table-cell>
                    @if($product->stock > $product->min_stock)
                        <x-ui.badge variant="success">Normal</x-ui.badge>
                    @elseif($product->stock > 0)
                        <x-ui.badge variant="warning">Rendah</x-ui.badge>
                    @else
                        <x-ui.badge variant="danger">Habis</x-ui.badge>
                    @endif
                </x-data.table-cell>
            </x-data.table-row>
        @empty
            <x-data.table-row>
                <x-data.table-cell colspan="8">
                    <x-layout.empty-state
                        icon="cube"
                        title="Tidak ada produk"
                        description="Belum ada produk yang terdaftar dalam sistem"
                    >
                        <x-slot:action>
                            <x-ui.button variant="primary" href="{{ route('admin.products.create') }}">
                                Tambah Produk
                            </x-ui.button>
                        </x-slot:action>
                    </x-layout.empty-state>
                </x-data.table-cell>
            </x-data.table-row>
        @endforelse
    </x-data.table>

    <!-- Pagination -->
    <div>
        {{ $products->links() }}
    </div>
</div>
