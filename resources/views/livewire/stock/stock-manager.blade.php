<div class="space-y-4">
    {{-- Header Compact --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Manajemen Stok</h1>
            <p class="text-sm text-gray-500">{{ $this->stats['total'] }} produk terdaftar</p>
        </div>
        @if(count($selectedProducts) > 0)
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">{{ count($selectedProducts) }} dipilih</span>
                <x-ui.button variant="primary" size="sm" wire:click="openBulkModal">Bulk Adjust</x-ui.button>
                <x-ui.button variant="ghost" size="sm" wire:click="clearSelection">×</x-ui.button>
            </div>
        @endif
    </div>

    {{-- Stats Row - Compact & Clickable --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
        @php
            $filters = [
                'all' => ['label' => 'Semua', 'value' => $this->stats['total'], 'color' => 'gray', 'icon' => 'cube'],
                'normal' => ['label' => 'Normal', 'value' => $this->stats['normal'], 'color' => 'green', 'icon' => 'check-circle'],
                'low' => ['label' => 'Rendah', 'value' => $this->stats['low'], 'color' => 'yellow', 'icon' => 'exclamation-triangle'],
                'out' => ['label' => 'Habis', 'value' => $this->stats['out'], 'color' => 'red', 'icon' => 'x-circle'],
            ];
        @endphp
        @foreach($filters as $key => $filter)
            <button 
                wire:click="$set('stockFilter', '{{ $key }}')" 
                @class([
                    'p-3 rounded-xl text-left transition-all duration-200',
                    'ring-2 ring-primary-500 bg-primary-50' => $stockFilter === $key,
                    'bg-white border border-gray-200 hover:border-gray-300 hover:shadow-sm' => $stockFilter !== $key,
                ])
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-gray-500 uppercase">{{ $filter['label'] }}</span>
                    <span @class([
                        'w-6 h-6 rounded-full flex items-center justify-center',
                        "bg-{$filter['color']}-100 text-{$filter['color']}-600",
                    ])>
                        <x-ui.icon :name="$filter['icon']" class="w-3.5 h-3.5" />
                    </span>
                </div>
                <p @class([
                    'text-2xl font-bold mt-1',
                    "text-{$filter['color']}-600" => $filter['value'] > 0 || $key === 'all',
                    'text-gray-400' => $filter['value'] === 0 && $key !== 'all',
                ])>{{ $filter['value'] }}</p>
            </button>
        @endforeach
    </div>

    {{-- Financial Summary - Single Line --}}
    <div class="flex flex-wrap items-center gap-4 sm:gap-8 px-4 py-3 bg-slate-800 rounded-xl text-white text-sm">
        <div>
            <span class="text-slate-400">Modal:</span>
            <span class="font-semibold ml-1">Rp {{ number_format($this->stats['cost'], 0, ',', '.') }}</span>
        </div>
        <div>
            <span class="text-slate-400">Nilai:</span>
            <span class="font-semibold ml-1">Rp {{ number_format($this->stats['value'], 0, ',', '.') }}</span>
        </div>
        <div>
            <span class="text-slate-400">Profit:</span>
            <span class="font-semibold ml-1 text-green-400">+Rp {{ number_format($this->stats['profit'], 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Tabs + Search Combined --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="flex gap-1 p-1 bg-gray-100 rounded-lg w-fit">
            <button wire:click="setTab('products')" @class([
                'px-4 py-2 text-sm font-medium rounded-md transition-all',
                'bg-white shadow text-gray-900' => $activeTab === 'products',
                'text-gray-600 hover:text-gray-900' => $activeTab !== 'products',
            ])>Produk</button>
            <button wire:click="setTab('history')" @class([
                'px-4 py-2 text-sm font-medium rounded-md transition-all',
                'bg-white shadow text-gray-900' => $activeTab === 'history',
                'text-gray-600 hover:text-gray-900' => $activeTab !== 'history',
            ])>Riwayat</button>
        </div>
        
        <div class="flex-1 flex gap-2">
            <div class="relative flex-1">
                <x-ui.icon name="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="{{ $activeTab === 'products' ? 'search' : 'historySearch' }}"
                    placeholder="Cari produk..."
                    class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                >
            </div>
            @if($activeTab === 'products')
                <select wire:model.live="categoryFilter" class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500">
                    <option value="">Kategori</option>
                    @foreach($this->categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            @else
                <select wire:model.live="historyType" class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500">
                    <option value="all">Semua</option>
                    <option value="in">Masuk</option>
                    <option value="out">Keluar</option>
                </select>
            @endif
        </div>
    </div>

    {{-- Products Tab --}}
    @if($activeTab === 'products')
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            {{-- Mobile Cards / Desktop Table --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase">
                        <tr>
                            <th class="w-8 px-3 py-3">
                                <input type="checkbox" wire:click="selectAllVisible" 
                                    @checked(count($selectedProducts) > 0 && count($selectedProducts) === $this->products->count())
                                    class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            </th>
                            <th class="px-3 py-3 text-left">Produk</th>
                            <th class="px-3 py-3 text-center w-24">Stok</th>
                            <th class="px-3 py-3 text-center w-28">Adjust</th>
                            <th class="px-3 py-3 text-right w-32">Harga</th>
                            <th class="px-3 py-3 text-right w-36">Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($this->products as $product)
                            <tr wire:key="p-{{ $product->id }}" class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-3 py-2">
                                    <input type="checkbox" wire:click="toggleProductSelection({{ $product->id }})"
                                        @checked(in_array($product->id, $selectedProducts))
                                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        @if($product->image)
                                            <img src="{{ $product->image_thumbnail_url }}" alt="" class="w-8 h-8 rounded-lg object-cover bg-gray-100" loading="lazy">
                                        @else
                                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                                                <x-ui.icon name="cube" class="w-4 h-4 text-gray-400" />
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-900 text-sm truncate">{{ $product->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $product->sku ?? $product->category ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <button wire:click="quickAdjust({{ $product->id }}, 'in')" 
                                        class="group cursor-pointer hover:bg-gray-100 rounded-lg px-2 py-1 transition-colors"
                                        title="Klik untuk adjust">
                                        <span @class([
                                            'text-lg font-bold',
                                            'text-red-600' => $product->stock <= 0,
                                            'text-yellow-600' => $product->stock > 0 && $product->stock <= $product->min_stock,
                                            'text-gray-900' => $product->stock > $product->min_stock,
                                        ])>{{ $product->stock }}</span>
                                        <span class="block text-[10px] text-gray-400">min {{ $product->min_stock }}</span>
                                    </button>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center justify-center gap-1">
                                        <button wire:click="quickDecrement({{ $product->id }})" 
                                            @disabled($product->stock <= 0)
                                            @class([
                                                'w-7 h-7 rounded-md text-sm font-bold transition-all',
                                                'bg-red-50 text-red-600 hover:bg-red-100' => $product->stock > 0,
                                                'bg-gray-100 text-gray-300 cursor-not-allowed' => $product->stock <= 0,
                                            ])>−</button>
                                        <button wire:click="quickIncrement({{ $product->id }})" 
                                            class="w-7 h-7 rounded-md bg-green-50 text-green-600 hover:bg-green-100 text-sm font-bold transition-all">+</button>
                                    </div>
                                </td>
                                <td class="px-3 py-2 text-right text-sm">
                                    <p class="font-medium">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                    <p class="text-xs text-gray-400">{{ number_format($product->cost_price, 0, ',', '.') }}</p>
                                </td>
                                <td class="px-3 py-2 text-right text-sm">
                                    <p class="font-medium">Rp {{ number_format($product->stock * $product->price, 0, ',', '.') }}</p>
                                    @php $profit = $product->stock * ($product->price - $product->cost_price); @endphp
                                    <p class="text-xs {{ $profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $profit >= 0 ? '+' : '' }}{{ number_format($profit, 0, ',', '.') }}
                                    </p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <x-ui.icon name="cube" class="w-8 h-8 mx-auto mb-2 text-gray-300" />
                                    <p>Tidak ada produk</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile View --}}
            <div class="sm:hidden divide-y divide-gray-100">
                @forelse($this->products as $product)
                    <div wire:key="pm-{{ $product->id }}" class="p-3 flex items-center gap-3">
                        <input type="checkbox" wire:click="toggleProductSelection({{ $product->id }})"
                            @checked(in_array($product->id, $selectedProducts))
                            class="rounded border-gray-300 text-primary-600">
                        
                        @if($product->image)
                            <img src="{{ $product->image_thumbnail_url }}" class="w-10 h-10 rounded-lg object-cover" loading="lazy">
                        @else
                            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                <x-ui.icon name="cube" class="w-5 h-5 text-gray-400" />
                            </div>
                        @endif
                        
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-sm truncate">{{ $product->name }}</p>
                            <p class="text-xs text-gray-500">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <button wire:click="quickDecrement({{ $product->id }})" @disabled($product->stock <= 0)
                                class="w-8 h-8 rounded-lg bg-red-50 text-red-600 font-bold disabled:opacity-50">−</button>
                            <button wire:click="quickAdjust({{ $product->id }}, 'in')" @class([
                                'w-10 text-center font-bold',
                                'text-red-600' => $product->stock <= 0,
                                'text-yellow-600' => $product->stock > 0 && $product->stock <= $product->min_stock,
                                'text-gray-900' => $product->stock > $product->min_stock,
                            ])>{{ $product->stock }}</button>
                            <button wire:click="quickIncrement({{ $product->id }})" 
                                class="w-8 h-8 rounded-lg bg-green-50 text-green-600 font-bold">+</button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">Tidak ada produk</div>
                @endforelse
            </div>

            @if($this->products->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
                    {{ $this->products->links() }}
                </div>
            @endif
        </div>
    @endif

    {{-- History Tab --}}
    @if($activeTab === 'history')
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="divide-y divide-gray-100">
                @forelse($this->adjustments as $adj)
                    <div wire:key="h-{{ $adj->id }}" class="p-3 flex items-center gap-3 hover:bg-gray-50/50">
                        <div @class([
                            'w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0',
                            'bg-green-100 text-green-600' => $adj->type === 'in',
                            'bg-red-100 text-red-600' => $adj->type === 'out',
                        ])>
                            <x-ui.icon :name="$adj->type === 'in' ? 'arrow-up' : 'arrow-down'" class="w-4 h-4" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-sm truncate">{{ $adj->product->name ?? '-' }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $adj->reason }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p @class([
                                'font-bold',
                                'text-green-600' => $adj->type === 'in',
                                'text-red-600' => $adj->type === 'out',
                            ])>{{ $adj->type === 'in' ? '+' : '-' }}{{ $adj->quantity }}</p>
                            <p class="text-xs text-gray-400">{{ $adj->previous_stock }} → {{ $adj->new_stock }}</p>
                        </div>
                        <div class="text-right flex-shrink-0 hidden sm:block">
                            <p class="text-xs text-gray-500">{{ $adj->created_at->format('d/m H:i') }}</p>
                            <p class="text-xs text-gray-400">{{ $adj->user->name ?? '-' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <x-ui.icon name="clock" class="w-8 h-8 mx-auto mb-2 text-gray-300" />
                        <p>Belum ada riwayat</p>
                    </div>
                @endforelse
            </div>
            
            @if($this->adjustments->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
                    {{ $this->adjustments->links() }}
                </div>
            @endif
        </div>
    @endif

    {{-- Quick Adjust Modal - Simplified --}}
    @if($showAdjustModal && $this->selectedProduct)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data x-init="$refs.qty.focus()">
            <div class="fixed inset-0 bg-black/50" wire:click="closeAdjustModal"></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden">
                <form wire:submit="saveAdjustment">
                    {{-- Product Header --}}
                    <div class="p-4 bg-gray-50 border-b flex items-center gap-3">
                        @if($this->selectedProduct->image)
                            <img src="{{ $this->selectedProduct->image_thumbnail_url }}" class="w-10 h-10 rounded-lg object-cover">
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-medium truncate">{{ $this->selectedProduct->name }}</p>
                            <p class="text-sm text-gray-500">Stok: {{ $this->selectedProduct->stock }}</p>
                        </div>
                        <button type="button" wire:click="closeAdjustModal" class="text-gray-400 hover:text-gray-600">
                            <x-ui.icon name="x-mark" class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="p-4 space-y-4">
                        {{-- Type Toggle --}}
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" wire:click="$set('adjustType', 'in')" @class([
                                'py-3 rounded-xl font-medium transition-all flex items-center justify-center gap-2',
                                'bg-green-500 text-white' => $adjustType === 'in',
                                'bg-gray-100 text-gray-600' => $adjustType !== 'in',
                            ])>
                                <x-ui.icon name="arrow-up" class="w-4 h-4" /> Tambah
                            </button>
                            <button type="button" wire:click="$set('adjustType', 'out')" @class([
                                'py-3 rounded-xl font-medium transition-all flex items-center justify-center gap-2',
                                'bg-red-500 text-white' => $adjustType === 'out',
                                'bg-gray-100 text-gray-600' => $adjustType !== 'out',
                            ])>
                                <x-ui.icon name="arrow-down" class="w-4 h-4" /> Kurangi
                            </button>
                        </div>

                        {{-- Quantity --}}
                        <div>
                            <input type="number" wire:model="adjustQuantity" x-ref="qty" min="1"
                                class="w-full text-center text-3xl font-bold py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                            @error('adjustQuantity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            
                            {{-- Preview --}}
                            <div class="flex items-center justify-center gap-2 mt-2 text-sm text-gray-500">
                                {{ $this->selectedProduct->stock }}
                                <x-ui.icon name="arrow-right" class="w-4 h-4" />
                                <span @class([
                                    'font-bold',
                                    'text-green-600' => $adjustType === 'in',
                                    'text-red-600' => $adjustType === 'out',
                                ])>
                                    @php
                                        $newStock = $adjustType === 'in' 
                                            ? $this->selectedProduct->stock + (int)$adjustQuantity 
                                            : max(0, $this->selectedProduct->stock - (int)$adjustQuantity);
                                    @endphp
                                    {{ $newStock }}
                                </span>
                            </div>
                        </div>

                        {{-- Reason --}}
                        <div>
                            <input type="text" wire:model="adjustReason" placeholder="Alasan (wajib)"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                            @error('adjustReason')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="p-4 border-t bg-gray-50 flex gap-2">
                        <button type="button" wire:click="closeAdjustModal" class="flex-1 py-3 rounded-xl bg-gray-200 text-gray-700 font-medium">Batal</button>
                        <button type="submit" @class([
                            'flex-1 py-3 rounded-xl text-white font-medium transition-all',
                            'bg-green-500 hover:bg-green-600' => $adjustType === 'in',
                            'bg-red-500 hover:bg-red-600' => $adjustType === 'out',
                        ])>
                            <span wire:loading.remove wire:target="saveAdjustment">Simpan</span>
                            <span wire:loading wire:target="saveAdjustment">...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Bulk Modal --}}
    @if($showBulkModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50" wire:click="closeBulkModal"></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden">
                <form wire:submit="saveBulkAdjustment">
                    <div class="p-4 border-b">
                        <h3 class="font-semibold">Bulk Adjustment</h3>
                        <p class="text-sm text-gray-500">{{ count($selectedProducts) }} produk dipilih</p>
                    </div>
                    <div class="p-4 space-y-4">
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" wire:click="$set('bulkType', 'in')" @class([
                                'py-3 rounded-xl font-medium',
                                'bg-green-500 text-white' => $bulkType === 'in',
                                'bg-gray-100 text-gray-600' => $bulkType !== 'in',
                            ])>+ Tambah</button>
                            <button type="button" wire:click="$set('bulkType', 'out')" @class([
                                'py-3 rounded-xl font-medium',
                                'bg-red-500 text-white' => $bulkType === 'out',
                                'bg-gray-100 text-gray-600' => $bulkType !== 'out',
                            ])>− Kurangi</button>
                        </div>
                        <input type="number" wire:model="adjustQuantity" min="1" placeholder="Jumlah"
                            class="w-full text-center text-2xl font-bold py-3 border rounded-xl">
                        <input type="text" wire:model="bulkReason" placeholder="Alasan"
                            class="w-full px-4 py-3 border rounded-xl">
                    </div>
                    <div class="p-4 border-t flex gap-2">
                        <button type="button" wire:click="closeBulkModal" class="flex-1 py-3 rounded-xl bg-gray-200 font-medium">Batal</button>
                        <button type="submit" class="flex-1 py-3 rounded-xl bg-primary-500 text-white font-medium">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
