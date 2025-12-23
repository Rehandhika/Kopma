<div class="max-w-4xl mx-auto">
    <x-layout.page-header 
        title="Tambah Produk Baru"
        description="Isi informasi produk untuk ditambahkan ke inventori"
    >
        <x-slot:actions>
            <x-ui.button 
                variant="white" 
                :href="route('admin.products.index')"
                icon="arrow-left"
            >
                Kembali
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    <form wire:submit="save" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column - Image --}}
            <div class="lg:col-span-1">
                <x-ui.card padding="true">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Gambar Produk</h3>
                    
                    <x-ui.image-upload 
                        name="image"
                        label=""
                        :preview="$imagePreview"
                        :error="$errors->first('image')"
                        hint="Gambar akan dioptimasi otomatis untuk performa terbaik"
                    />
                </x-ui.card>
            </div>

            {{-- Right Column - Form Fields --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Basic Info --}}
                <x-ui.card padding="true">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                    
                    <div class="space-y-4">
                        <x-ui.input 
                            label="Nama Produk"
                            wire:model="name"
                            placeholder="Masukkan nama produk"
                            required
                            :error="$errors->first('name')"
                        />

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-ui.input 
                                label="SKU / Kode Barang"
                                wire:model="sku"
                                placeholder="Opsional, akan digenerate otomatis"
                                :error="$errors->first('sku')"
                            />

                            <x-ui.input 
                                label="Kategori"
                                wire:model="category"
                                placeholder="Contoh: Makanan, Minuman"
                                :error="$errors->first('category')"
                            />
                        </div>

                        <x-ui.textarea 
                            label="Deskripsi"
                            wire:model="description"
                            rows="3"
                            placeholder="Deskripsi singkat produk (opsional)"
                            :error="$errors->first('description')"
                        />
                    </div>
                </x-ui.card>

                {{-- Pricing & Stock --}}
                <x-ui.card padding="true">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Harga & Stok</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <x-ui.input 
                            type="number"
                            label="Harga Beli / Modal (Rp)"
                            wire:model.live="cost_price"
                            min="0"
                            step="100"
                            required
                            :error="$errors->first('cost_price')"
                            hint="Harga pembelian dari supplier"
                        />

                        <x-ui.input 
                            type="number"
                            label="Harga Jual (Rp)"
                            wire:model.live="price"
                            min="0"
                            step="100"
                            required
                            :error="$errors->first('price')"
                        />
                    </div>

                    {{-- Profit Preview --}}
                    @if($cost_price && $price && $price > 0)
                        @php
                            $profit = $price - $cost_price;
                            $margin = round(($profit / $price) * 100, 1);
                        @endphp
                        <div class="p-3 rounded-lg mb-4 {{ $profit > 0 ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
                            <div class="flex items-center justify-between text-sm">
                                <span class="{{ $profit > 0 ? 'text-green-700' : 'text-red-700' }}">
                                    <strong>Keuntungan per unit:</strong> 
                                    Rp {{ number_format($profit, 0, ',', '.') }}
                                </span>
                                <span class="{{ $profit > 0 ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                    {{ $margin }}% margin
                                </span>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-ui.input 
                            type="number"
                            label="Stok Awal"
                            wire:model="stock"
                            min="0"
                            required
                            :error="$errors->first('stock')"
                        />

                        <x-ui.input 
                            type="number"
                            label="Minimal Stok"
                            wire:model="min_stock"
                            min="0"
                            required
                            :error="$errors->first('min_stock')"
                            hint="Alert jika stok di bawah nilai ini"
                        />
                    </div>
                </x-ui.card>

                {{-- Status --}}
                <x-ui.card padding="true">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Status</h3>
                    
                    <x-ui.select 
                        label="Status Produk"
                        wire:model="status"
                        :error="$errors->first('status')"
                    >
                        <option value="active">Aktif - Dapat dijual</option>
                        <option value="inactive">Tidak Aktif - Tidak ditampilkan</option>
                    </x-ui.select>
                </x-ui.card>

                {{-- Actions --}}
                <div class="flex justify-end space-x-3">
                    <x-ui.button 
                        type="button"
                        variant="white" 
                        :href="route('admin.products.index')"
                    >
                        Batal
                    </x-ui.button>
                    <x-ui.button 
                        type="submit" 
                        variant="primary"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="save">Simpan Produk</span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </x-ui.button>
                </div>
            </div>
        </div>
    </form>
</div>
