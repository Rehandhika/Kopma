<div class="p-6">
    @if (session()->has('success'))
        <x-ui.alert variant="success" dismissible class="mb-4">
            {{ session('success') }}
        </x-ui.alert>
    @endif
    
    @if (session()->has('error'))
        <x-ui.alert variant="danger" dismissible class="mb-4">
            {{ session('error') }}
        </x-ui.alert>
    @endif

    <x-layout.page-header 
        title="Manajemen Produk"
        description="Kelola produk untuk penjualan"
    >
        <x-slot:actions>
            <x-ui.button 
                variant="primary" 
                icon="plus"
                wire:click="create"
            >
                Tambah Produk
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    <x-layout.grid cols="4" class="mb-6">
        <x-layout.stat-card 
            label="Total Produk"
            :value="$stats['total']"
            icon="cube"
            icon-color="bg-primary-100"
            icon-text-color="text-primary-600"
        />

        <x-layout.stat-card 
            label="Produk Aktif"
            :value="$stats['active']"
            icon="check-circle"
            icon-color="bg-success-100"
            icon-text-color="text-success-600"
        />

        <x-layout.stat-card 
            label="Stok Menipis"
            :value="$stats['low_stock']"
            icon="exclamation-triangle"
            icon-color="bg-warning-100"
            icon-text-color="text-warning-600"
        />

        <x-layout.stat-card 
            label="Stok Habis"
            :value="$stats['out_of_stock']"
            icon="x-circle"
            icon-color="bg-danger-100"
            icon-text-color="text-danger-600"
        />
    </x-layout.grid>

    <x-ui.card class="mb-6">
        <x-layout.grid cols="4">
            <x-ui.input 
                label="Cari Produk"
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Nama, SKU, Kategori..."
            />

            <x-ui.select 
                label="Status"
                wire:model.live="statusFilter"
                placeholder="Semua Status"
                :options="[
                    'active' => 'Aktif',
                    'inactive' => 'Nonaktif'
                ]"
            />

            <x-ui.select 
                label="Kategori"
                wire:model.live="categoryFilter"
                placeholder="Semua Kategori"
                :options="collect($categories)->mapWithKeys(fn($cat) => [$cat => $cat])->toArray()"
            />

            <x-ui.select 
                label="Stok"
                wire:model.live="stockFilter"
                placeholder="Semua Stok"
                :options="[
                    'available' => 'Tersedia',
                    'low' => 'Stok Menipis',
                    'out' => 'Stok Habis'
                ]"
            />
        </x-layout.grid>
    </x-ui.card>

    <x-data.table :headers="['Produk', 'SKU', 'Kategori', 'Harga', 'Stok', 'Status', 'Aksi']">
        @forelse ($products as $product)
            <x-data.table-row>
                <x-data.table-cell>
                    <div class="font-medium text-gray-900">{{ $product->name }}</div>
                    @if($product->description)
                        <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($product->description, 50) }}</div>
                    @endif
                </x-data.table-cell>
                <x-data.table-cell>
                    {{ $product->sku ?? '-' }}
                </x-data.table-cell>
                <x-data.table-cell>
                    @if($product->category)
                        <x-ui.badge variant="info" size="sm">{{ $product->category }}</x-ui.badge>
                    @else
                        <span class="text-sm text-gray-400">-</span>
                    @endif
                </x-data.table-cell>
                <x-data.table-cell>
                    <span class="font-semibold text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                </x-data.table-cell>
                <x-data.table-cell>
                    @if($product->stock > $product->min_stock)
                        <x-ui.badge variant="success">{{ $product->stock }}</x-ui.badge>
                    @elseif($product->isLowStock() && !$product->isOutOfStock())
                        <x-ui.badge variant="warning">{{ $product->stock }}</x-ui.badge>
                    @else
                        <x-ui.badge variant="danger">{{ $product->stock }}</x-ui.badge>
                    @endif
                </x-data.table-cell>
                <x-data.table-cell>
                    <x-ui.button 
                        :variant="$product->status === 'active' ? 'success' : 'secondary'"
                        size="sm"
                        wire:click="toggleStatus({{ $product->id }})"
                    >
                        {{ $product->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                    </x-ui.button>
                </x-data.table-cell>
                <x-data.table-cell>
                    <div class="flex items-center justify-end space-x-2">
                        <x-ui.button 
                            variant="ghost" 
                            size="sm"
                            wire:click="edit({{ $product->id }})"
                        >
                            Edit
                        </x-ui.button>
                        <x-ui.button 
                            variant="ghost" 
                            size="sm"
                            wire:click="delete({{ $product->id }})"
                            wire:confirm="Yakin ingin menghapus produk ini?"
                        >
                            Hapus
                        </x-ui.button>
                    </div>
                </x-data.table-cell>
            </x-data.table-row>
        @empty
            <tr>
                <td colspan="7">
                    <x-layout.empty-state 
                        icon="cube"
                        title="Tidak ada produk ditemukan"
                        description="Coba ubah filter atau tambah produk baru"
                    >
                        <x-slot:action>
                            <x-ui.button 
                                variant="primary"
                                icon="plus"
                                wire:click="create"
                            >
                                Tambah Produk
                            </x-ui.button>
                        </x-slot:action>
                    </x-layout.empty-state>
                </td>
            </tr>
        @endforelse
    </x-data.table>

    <div class="mt-4">
        {{ $products->links() }}
    </div>

    @if($showModal)
        <x-ui.modal 
            name="product-form"
            :title="$editingId ? 'Edit Produk' : 'Tambah Produk Baru'"
            max-width="2xl"
            x-data="{ show: @entangle('showModal') }"
            x-show="show"
        >
            <form wire:submit.prevent="save">
                <x-layout.grid cols="2">
                    <div class="md:col-span-2">
                        <x-ui.input 
                            label="Nama Produk"
                            type="text"
                            wire:model="name"
                            required
                            :error="$errors->first('name')"
                        />
                    </div>

                    <x-ui.input 
                        label="SKU"
                        type="text"
                        wire:model="sku"
                        placeholder="Opsional"
                        :error="$errors->first('sku')"
                    />

                    <x-ui.input 
                        label="Kategori"
                        type="text"
                        wire:model="category"
                        placeholder="Opsional"
                        :error="$errors->first('category')"
                    />

                    <x-ui.input 
                        label="Harga"
                        type="number"
                        wire:model="price"
                        required
                        help="Masukkan harga dalam Rupiah"
                        :error="$errors->first('price')"
                    />

                    <x-ui.input 
                        label="Stok Awal"
                        type="number"
                        wire:model="stock"
                        required
                        :error="$errors->first('stock')"
                    />

                    <x-ui.input 
                        label="Stok Minimum"
                        type="number"
                        wire:model="min_stock"
                        required
                        :error="$errors->first('min_stock')"
                    />

                    <x-ui.select 
                        label="Status"
                        wire:model="status"
                        :options="[
                            'active' => 'Aktif',
                            'inactive' => 'Nonaktif'
                        ]"
                        required
                        :error="$errors->first('status')"
                    />

                    <div class="md:col-span-2">
                        <x-ui.textarea 
                            label="Deskripsi"
                            wire:model="description"
                            rows="3"
                            placeholder="Opsional"
                            :error="$errors->first('description')"
                        />
                    </div>
                </x-layout.grid>

                <x-slot:footer>
                    <x-ui.button 
                        variant="white"
                        type="button"
                        wire:click="$set('showModal', false)"
                    >
                        Batal
                    </x-ui.button>
                    <x-ui.button 
                        variant="primary"
                        type="submit"
                        :loading="$wire->loading('save')"
                    >
                        {{ $editingId ? 'Update' : 'Simpan' }}
                    </x-ui.button>
                </x-slot:footer>
            </form>
        </x-ui.modal>
    @endif

    <div wire:loading class="fixed inset-0 bg-gray-900/50 flex items-center justify-center z-40">
        <x-ui.card class="p-6">
            <div class="text-center">
                <x-ui.spinner size="lg" class="mx-auto mb-4" />
                <p class="text-gray-700 font-medium">Memuat...</p>
            </div>
        </x-ui.card>
    </div>
</div>
