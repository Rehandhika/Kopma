<div class="space-y-6">
    <!-- Header -->
    <x-layout.page-header 
        title="Manajemen Stok"
        description="Kelola dan monitor stok produk"
    >
        <x-slot:actions>
            <x-ui.button 
                variant="secondary" 
                href="{{ route('products.create') }}"
                icon="plus"
            >
                Tambah Produk
            </x-ui.button>
            <x-ui.button 
                variant="primary" 
                href="{{ route('stock.adjustment') }}"
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
            label="Stok Rendah"
            :value="$stats['low_stock']"
            icon="exclamation-triangle"
            iconColor="bg-warning-100"
            iconTextColor="text-warning-600"
        />
        <x-layout.stat-card
            label="Stok Habis"
            :value="$stats['out_of_stock']"
            icon="x-circle"
            iconColor="bg-danger-100"
            iconTextColor="text-danger-600"
        />
        <x-layout.stat-card
            label="Nilai Stok"
            :value="'Rp ' . number_format($stats['total_stock_value'], 0, ',', '.')"
            icon="currency-dollar"
            iconColor="bg-success-100"
            iconTextColor="text-success-600"
        />
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
        :headers="['Produk', 'SKU', 'Kategori', 'Stok Saat Ini', 'Min. Stok', 'Harga Modal', 'Nilai Total', 'Status']"
        striped="true"
        hoverable="true"
    >
        @forelse($products as $product)
            <x-data.table-row>
                <x-data.table-cell>
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gray-200 rounded flex-shrink-0">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover rounded" alt="{{ $product->name }}">
                            @endif
                        </div>
                        <div class="ml-3">
                            <div class="font-medium text-gray-900">{{ $product->name }}</div>
                        </div>
                    </div>
                </x-data.table-cell>
                <x-data.table-cell>{{ $product->sku }}</x-data.table-cell>
                <x-data.table-cell>{{ $product->category?->name ?? '-' }}</x-data.table-cell>
                <x-data.table-cell>
                    <span @class([
                        'text-lg font-bold',
                        'text-danger-600' => $product->stock <= $product->min_stock,
                        'text-gray-900' => $product->stock > $product->min_stock,
                    ])>
                        {{ $product->stock }}
                    </span>
                </x-data.table-cell>
                <x-data.table-cell>{{ $product->min_stock }}</x-data.table-cell>
                <x-data.table-cell>Rp {{ number_format($product->cost, 0, ',', '.') }}</x-data.table-cell>
                <x-data.table-cell class="font-medium">Rp {{ number_format($product->stock * $product->cost, 0, ',', '.') }}</x-data.table-cell>
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
                            <x-ui.button variant="primary" href="{{ route('products.create') }}">
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
