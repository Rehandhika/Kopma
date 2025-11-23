<div class="space-y-6">
    <x-layout.page-header 
        title="Daftar Produk"
        description="Kelola produk untuk penjualan"
    >
        <x-slot:actions>
            <x-ui.button 
                variant="primary" 
                icon="plus"
                :href="route('products.create')"
            >
                Tambah Produk
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    <x-ui.card>
        <x-layout.grid cols="3">
            <x-ui.input 
                type="text"
                wire:model.live="search"
                placeholder="Cari produk..."
            />
            
            <x-ui.select 
                wire:model.live="categoryFilter"
                placeholder="Semua Kategori"
                :options="collect($categories)->mapWithKeys(fn($cat) => [$cat => $cat])->toArray()"
            />
            
            <x-ui.select 
                wire:model.live="stockFilter"
                placeholder="Semua Stok"
                :options="[
                    'low' => 'Stok Rendah',
                    'out' => 'Habis'
                ]"
            />
        </x-layout.grid>
    </x-ui.card>

    <x-data.table :headers="['Produk', 'SKU/Barcode', 'Kategori', 'Harga', 'Stok', 'Status', 'Aksi']">
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
                <x-data.table-cell>
                    <div class="text-sm">
                        <div>{{ $product->sku }}</div>
                        <div class="text-gray-500">{{ $product->barcode }}</div>
                    </div>
                </x-data.table-cell>
                <x-data.table-cell>{{ $product->category ?? '-' }}</x-data.table-cell>
                <x-data.table-cell>
                    <span class="font-medium">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                </x-data.table-cell>
                <x-data.table-cell>
                    <div class="text-sm">
                        <div class="font-medium {{ $product->stock <= $product->min_stock ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $product->stock }}
                        </div>
                        <div class="text-gray-500">Min: {{ $product->min_stock }}</div>
                    </div>
                </x-data.table-cell>
                <x-data.table-cell>
                    @if($product->stock > $product->min_stock)
                        <x-ui.badge variant="success">Tersedia</x-ui.badge>
                    @elseif($product->stock > 0)
                        <x-ui.badge variant="warning">Stok Rendah</x-ui.badge>
                    @else
                        <x-ui.badge variant="danger">Habis</x-ui.badge>
                    @endif
                </x-data.table-cell>
                <x-data.table-cell>
                    <div class="flex items-center space-x-2">
                        <x-ui.button 
                            variant="ghost" 
                            size="sm"
                            icon="pencil"
                            :href="route('products.edit', $product)"
                        />
                        <x-ui.button 
                            variant="ghost" 
                            size="sm"
                            icon="trash"
                            wire:click="deleteProduct({{ $product->id }})"
                            wire:confirm="Hapus produk ini?"
                        />
                    </div>
                </x-data.table-cell>
            </x-data.table-row>
        @empty
            <tr>
                <td colspan="7">
                    <x-layout.empty-state 
                        icon="cube"
                        title="Tidak ada produk"
                        description="Belum ada produk yang ditambahkan"
                    >
                        <x-slot:action>
                            <x-ui.button 
                                variant="primary"
                                icon="plus"
                                :href="route('products.create')"
                            >
                                Tambah Produk Pertama
                            </x-ui.button>
                        </x-slot:action>
                    </x-layout.empty-state>
                </td>
            </tr>
        @endforelse
    </x-data.table>

    <div>
        {{ $products->links() }}
    </div>
</div>
