<div class="min-h-screen">
    {{-- Banner Carousel Section --}}
    @if($banners->count() > 0)
        <div class="w-full relative group">
            <div class="absolute inset-0 bg-gradient-to-b from-slate-950/0 to-slate-950 pointer-events-none z-10"></div>
            <x-ui.banner-carousel :banners="$banners" />
        </div>
    @endif

    {{-- Main Content --}}
    <div class="max-w-7xl mx-auto px-4 py-12 relative z-20">
        
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight mb-2">Katalog Produk</h1>
                <p class="text-slate-400">Temukan kebutuhan harianmu dengan harga terbaik.</p>
            </div>
        </div>

        {{-- Search & Filter Bar (Glass Panel) --}}
        <div class="bg-slate-900/60 backdrop-blur-md border border-white/10 rounded-2xl p-4 mb-10 shadow-xl">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                {{-- Search Input --}}
                <div class="md:col-span-8 relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-slate-500"></i>
                    </div>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari produk (nama, SKU)..."
                        class="w-full pl-11 pr-4 py-3 bg-slate-950/50 border border-white/10 rounded-xl focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 text-white placeholder-slate-600 transition-all"
                    >
                </div>

                {{-- Category Filter --}}
                <div class="md:col-span-4 relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-filter text-slate-500"></i>
                    </div>
                    <select 
                        wire:model.live="category"
                        class="w-full pl-11 pr-10 py-3 bg-slate-950/50 border border-white/10 rounded-xl focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 text-white appearance-none cursor-pointer transition-all"
                    >
                        <option value="" class="bg-slate-900 text-slate-300">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" class="bg-slate-900 text-white">{{ $cat }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <i class="fas fa-chevron-down text-slate-500 text-xs"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Products Grid --}}
        @if($products->count() > 0)
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                @foreach($products as $product)
                    <div class="group relative bg-slate-900/40 border border-white/5 rounded-2xl overflow-hidden backdrop-blur-sm transition-all duration-300 hover:bg-slate-800/60 hover:border-indigo-500/30 hover:shadow-[0_0_20px_rgba(99,102,241,0.15)] hover:-translate-y-1">
                        
                        {{-- Image Area --}}
                        <div class="aspect-square relative overflow-hidden bg-slate-800">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 opacity-90 group-hover:opacity-100" loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-600 bg-slate-800/50">
                                    <i class="fas fa-cube text-4xl opacity-50"></i>
                                </div>
                            @endif

                            {{-- Badges --}}
                            <div class="absolute top-3 right-3 flex flex-col gap-2 items-end">
                                @if($product->is_featured)
                                    <span class="bg-yellow-500/20 text-yellow-300 backdrop-blur-md border border-yellow-500/30 px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider shadow-lg">
                                        Featured
                                    </span>
                                @endif
                            </div>

                            {{-- Stock Status Overlay --}}
                            <div class="absolute bottom-3 left-3">
                                @if($product->isOutOfStock())
                                    <span class="bg-red-500/90 text-white px-2 py-1 rounded-md text-xs font-medium shadow-sm">Habis</span>
                                @elseif($product->isLowStock())
                                    <span class="bg-orange-500/90 text-white px-2 py-1 rounded-md text-xs font-medium shadow-sm">Sisa {{ $product->stock }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="p-5">
                            @if($product->category)
                                <p class="text-[10px] text-indigo-400 uppercase tracking-widest font-semibold mb-2">{{ $product->category }}</p>
                            @endif
                            
                            <h3 class="text-sm md:text-base font-medium text-slate-100 mb-1 line-clamp-2 min-h-[2.5rem] group-hover:text-indigo-300 transition-colors">
                                {{ $product->name }}
                            </h3>

                            {{-- Stock Info --}}
                            <div class="flex items-center space-x-2 mb-2 text-xs">
                                <span class="text-slate-500">Stok: <span class="text-slate-300 font-medium">{{ $product->stock }}</span></span>
                            </div>
                            
                            <div class="flex items-end justify-between">
                                <div class="flex flex-col">
                                    <span class="text-xs text-slate-500 mb-1">Harga</span>
                                    <span class="text-lg font-bold text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-400 group-hover:from-indigo-400 group-hover:to-cyan-400 transition-all duration-300">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </span>
                                </div>
                                <a href="{{ route('public.products.show', $product->slug) }}" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/5 hover:bg-indigo-600 text-slate-400 hover:text-white transition-all border border-white/10">
                                    <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-8 px-4 py-3 bg-slate-900/50 rounded-xl border border-white/5">
                {{ $products->links() }}
            </div>
        @else
            <div class="bg-slate-900/40 rounded-3xl border border-dashed border-slate-700 p-20 text-center backdrop-blur-sm">
                <div class="w-20 h-20 bg-slate-800/50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-search text-3xl text-slate-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">Tidak Ada Produk</h3>
                <p class="text-slate-500">Coba ubah kata kunci pencarian atau kategori.</p>
            </div>
        @endif
    </div>
</div>