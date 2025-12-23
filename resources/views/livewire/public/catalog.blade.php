<div>
    {{-- Banner Carousel Section --}}
    @if($banners->count() > 0)
        <div class="w-full">
            <x-ui.banner-carousel :banners="$banners" />
        </div>
    @endif

    {{-- Main Content --}}
    <div class="max-w-7xl mx-auto px-4 py-8">
        {{-- Search and Filter Section --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Search Input --}}
                <div>
                    <label for="search" class="block text-sm font-semibold text-gray-700 mb-3">
                        🔍 Cari Produk
                    </label>
                    <input 
                        type="text" 
                        id="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari berdasarkan nama, deskripsi, atau SKU..."
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 focus:bg-white"
                    >
                </div>

                {{-- Category Filter --}}
                <div>
                    <label for="category" class="block text-sm font-semibold text-gray-700 mb-3">
                        📂 Kategori
                    </label>
                    <select 
                        id="category"
                        wire:model.live="category"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 focus:bg-white"
                    >
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Products Grid --}}
        @if($products->count() > 0)
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
                @foreach($products as $product)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
                        <div class="aspect-square bg-gray-100 relative overflow-hidden">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                            @if($product->is_featured)
                                <div class="absolute top-2 right-2 bg-yellow-400 text-white px-2 py-1 rounded-full text-xs font-semibold">⭐</div>
                            @endif
                            <div class="absolute bottom-2 left-2">
                                @if($product->isOutOfStock())
                                    <span class="bg-red-500 text-white px-2 py-1 rounded text-xs">Habis</span>
                                @elseif($product->isLowStock())
                                    <span class="bg-orange-500 text-white px-2 py-1 rounded text-xs">Terbatas</span>
                                @else
                                    <span class="bg-green-500 text-white px-2 py-1 rounded text-xs">Tersedia</span>
                                @endif
                            </div>
                        </div>
                        <div class="p-3 md:p-4">
                            @if($product->category)
                                <p class="text-xs text-gray-500 uppercase mb-1">{{ $product->category }}</p>
                            @endif
                            <h3 class="text-sm md:text-base font-semibold text-gray-900 mb-2 line-clamp-2">{{ $product->name }}</h3>
                            <span class="text-lg font-bold text-blue-600">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            <div class="mt-2 flex items-center justify-between">
                                <span class="text-xs text-gray-500">Stok: {{ $product->stock }}</span>
                                <a href="{{ route('public.products.show', $product->slug) }}" class="text-blue-600 text-xs font-medium">Detail →</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-8">{{ $products->links() }}</div>
        @else
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak Ada Produk</h3>
                <p class="text-gray-600">Belum ada produk yang tersedia.</p>
            </div>
        @endif
    </div>
</div>