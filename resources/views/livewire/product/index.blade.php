<div class="space-y-6">
    <x-layout.page-header 
        title="Daftar Produk"
        description="Kelola produk untuk penjualan"
    >
        <x-slot:actions>
            <x-ui.button 
                variant="primary" 
                icon="plus"
                :href="route('admin.products.create')"
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

    <x-data.table :headers="['Produk', 'SKU', 'Kategori', 'Harga', 'Stok', 'Status', 'Aksi']">
        @forelse($products as $product)
            <x-data.table-row>
                <x-data.table-cell>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex-shrink-0 overflow-hidden">
                            @if($product->image_thumbnail_url)
                                <img 
                                    src="{{ $product->image_thumbnail_url }}" 
                                    alt="{{ $product->name }}"
                                    class="w-full h-full object-cover"
                                    loading="lazy"
                                >
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="ml-3">
                            <div class="font-medium text-gray-900">{{ $product->name }}</div>
                            @if($product->is_featured)
                                <span class="inline-flex items-center text-xs text-amber-600">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    Unggulan
                                </span>
                            @endif
                        </div>
                    </div>
                </x-data.table-cell>
                <x-data.table-cell>
                    <span class="text-sm font-mono text-gray-600">{{ $product->sku ?? '-' }}</span>
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
                            :href="route('admin.products.edit', $product)"
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
                                :href="route('admin.products.create')"
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
