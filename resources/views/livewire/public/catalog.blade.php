<div>
    {{-- Hero Section --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16 px-4">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Katalog Produk</h1>
            <p class="text-xl text-blue-100">Temukan berbagai produk kebutuhan mahasiswa dengan harga terjangkau</p>
        </div>
    </div>

    {{-- Search and Filter Section --}}
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Search Input --}}
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                        Cari Produk
                    </label>
                    <input 
                        type="text" 
                        id="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari berdasarkan nama, deskripsi, atau SKU..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>

                {{-- Category Filter --}}
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                        Kategori
                    </label>
                    <select 
                        id="category"
                        wire:model.live="category"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Active Filters Display --}}
            @if($search || $category)
                <div class="mt-4 flex flex-wrap gap-2">
                    @if($search)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                            Pencarian: "{{ $search }}"
                            <button wire:click="$set('search', '')" class="ml-2 hover:text-blue-600">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </span>
                    @endif
                    @if($category)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                            Kategori: {{ $category }}
                            <button wire:click="$set('category', '')" class="ml-2 hover:text-green-600">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </span>
                    @endif
                </div>
            @endif
        </div>

        {{-- Products Grid --}}
        @if($products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @foreach($products as $product)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        {{-- Product Image --}}
                        <div class="aspect-square bg-gray-100 relative overflow-hidden">
                            @if($product->image_url)
                                <img 
                                    src="{{ $product->image_url }}" 
                                    alt="{{ $product->name }}"
                                    class="w-full h-full object-cover"
                                    loading="lazy"
                                >
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif

                            {{-- Featured Badge --}}
                            @if($product->is_featured)
                                <div class="absolute top-2 right-2 bg-yellow-400 text-yellow-900 px-2 py-1 rounded-full text-xs font-semibold">
                                    ⭐ Unggulan
                                </div>
                            @endif

                            {{-- Stock Status Badge --}}
                            <div class="absolute bottom-2 left-2">
                                @if($product->isOutOfStock())
                                    <span class="bg-red-500 text-white px-2 py-1 rounded text-xs font-semibold">
                                        Stok Habis
                                    </span>
                                @elseif($product->isLowStock())
                                    <span class="bg-orange-500 text-white px-2 py-1 rounded text-xs font-semibold">
                                        Stok Terbatas
                                    </span>
                                @else
                                    <span class="bg-green-500 text-white px-2 py-1 rounded text-xs font-semibold">
                                        Tersedia
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Product Info --}}
                        <div class="p-4">
                            {{-- Category --}}
                            @if($product->category)
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">
                                    {{ $product->category }}
                                </p>
                            @endif

                            {{-- Product Name --}}
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                                {{ $product->name }}
                            </h3>

                            {{-- Description --}}
                            @if($product->description)
                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                    {{ $product->description }}
                                </p>
                            @endif

                            {{-- Price --}}
                            <div class="flex items-center justify-between">
                                <span class="text-2xl font-bold text-blue-600">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </span>
                                
                                {{-- View Details Link --}}
                                <a 
                                    href="{{ route('public.products.show', $product->slug) }}"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                                >
                                    Detail →
                                </a>
                            </div>

                            {{-- Stock Info --}}
                            <div class="mt-2 text-xs text-gray-500">
                                Stok: {{ $product->stock }} unit
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak Ada Produk</h3>
                <p class="text-gray-600 mb-4">
                    @if($search || $category)
                        Tidak ada produk yang sesuai dengan pencarian Anda.
                    @else
                        Belum ada produk yang tersedia saat ini.
                    @endif
                </p>
                @if($search || $category)
                    <button 
                        wire:click="$set('search', ''); $set('category', '')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        Hapus Filter
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>
