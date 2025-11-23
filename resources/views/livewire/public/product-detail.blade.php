<div>
    {{-- Breadcrumb --}}
    <div class="bg-gray-100 py-4">
        <div class="max-w-7xl mx-auto px-4">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-blue-600 transition-colors">
                    <i class="fas fa-home"></i> Katalog
                </a>
                <span class="text-gray-400">/</span>
                @if($product->category)
                    <span class="text-gray-600">{{ $product->category }}</span>
                    <span class="text-gray-400">/</span>
                @endif
                <span class="text-gray-900 font-medium">{{ $product->name }}</span>
            </nav>
        </div>
    </div>

    {{-- Product Detail Section --}}
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-6 lg:p-8">
                {{-- Product Image --}}
                <div class="space-y-4">
                    <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden relative">
                        @if($product->image_url)
                            <img 
                                src="{{ $product->image_url }}" 
                                alt="{{ $product->name }}"
                                class="w-full h-full object-cover"
                            >
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif

                        {{-- Featured Badge --}}
                        @if($product->is_featured)
                            <div class="absolute top-4 right-4 bg-yellow-400 text-yellow-900 px-3 py-2 rounded-full text-sm font-semibold shadow-lg">
                                <i class="fas fa-star"></i> Produk Unggulan
                            </div>
                        @endif
                    </div>

                    {{-- Additional Info Cards --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <i class="fas fa-shield-alt text-blue-600 text-2xl mb-2"></i>
                            <p class="text-sm font-medium text-blue-900">Produk Resmi</p>
                            <p class="text-xs text-blue-700">Koperasi Mahasiswa</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <i class="fas fa-tags text-green-600 text-2xl mb-2"></i>
                            <p class="text-sm font-medium text-green-900">Harga Terjangkau</p>
                            <p class="text-xs text-green-700">Khusus Mahasiswa</p>
                        </div>
                    </div>
                </div>

                {{-- Product Information --}}
                <div class="space-y-6">
                    {{-- Category Badge --}}
                    @if($product->category)
                        <div>
                            <span class="inline-block px-3 py-1 bg-gray-100 text-gray-700 text-sm font-medium rounded-full">
                                <i class="fas fa-tag"></i> {{ $product->category }}
                            </span>
                        </div>
                    @endif

                    {{-- Product Name --}}
                    <div>
                        <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">
                            {{ $product->name }}
                        </h1>
                        @if($product->sku)
                            <p class="text-sm text-gray-500">
                                SKU: <span class="font-mono">{{ $product->sku }}</span>
                            </p>
                        @endif
                    </div>

                    {{-- Price --}}
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-6 border-2 border-blue-200">
                        <p class="text-sm text-gray-600 mb-1">Harga</p>
                        <p class="text-4xl font-bold text-blue-600">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </p>
                    </div>

                    {{-- Stock Availability --}}
                    <div class="border-t border-b border-gray-200 py-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700 font-medium">Ketersediaan Stok</span>
                            <div class="flex items-center space-x-2">
                                @if($product->isOutOfStock())
                                    <span class="flex items-center space-x-2 bg-red-100 text-red-800 px-4 py-2 rounded-lg font-semibold">
                                        <i class="fas fa-times-circle"></i>
                                        <span>Stok Habis</span>
                                    </span>
                                @elseif($product->isLowStock())
                                    <span class="flex items-center space-x-2 bg-orange-100 text-orange-800 px-4 py-2 rounded-lg font-semibold">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <span>Stok Terbatas ({{ $product->stock }} unit)</span>
                                    </span>
                                @else
                                    <span class="flex items-center space-x-2 bg-green-100 text-green-800 px-4 py-2 rounded-lg font-semibold">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Tersedia ({{ $product->stock }} unit)</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Product Description --}}
                    @if($product->description)
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 mb-3">
                                <i class="fas fa-info-circle text-blue-600"></i> Deskripsi Produk
                            </h2>
                            <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
                                <p>{{ $product->description }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Product Status --}}
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Status Produk</span>
                            @if($product->isActive())
                                <span class="flex items-center space-x-1 text-green-600 font-medium">
                                    <i class="fas fa-circle text-xs"></i>
                                    <span>Aktif</span>
                                </span>
                            @else
                                <span class="flex items-center space-x-1 text-gray-500 font-medium">
                                    <i class="fas fa-circle text-xs"></i>
                                    <span>Tidak Aktif</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Call to Action --}}
                    <div class="space-y-3 pt-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-info-circle text-blue-600 text-xl mt-1"></i>
                                <div>
                                    <p class="text-sm font-medium text-blue-900 mb-1">
                                        Cara Membeli
                                    </p>
                                    <p class="text-sm text-blue-700 leading-relaxed">
                                        Untuk membeli produk ini, silakan kunjungi koperasi kami pada jam operasional 
                                        (Senin-Kamis, 08:00-16:00) atau hubungi kami melalui WhatsApp.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <a 
                                href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('sikopma.contact_whatsapp', '6281234567890')) }}?text=Halo,%20saya%20tertarik%20dengan%20produk%20{{ urlencode($product->name) }}" 
                                target="_blank"
                                class="flex items-center justify-center space-x-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors shadow-md"
                            >
                                <i class="fab fa-whatsapp text-xl"></i>
                                <span>Hubungi via WhatsApp</span>
                            </a>
                            
                            <a 
                                href="{{ route('home') }}"
                                class="flex items-center justify-center space-x-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors"
                            >
                                <i class="fas fa-arrow-left"></i>
                                <span>Kembali ke Katalog</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Additional Information Section --}}
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clock text-blue-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Jam Operasional</h3>
                <p class="text-sm text-gray-600">
                    Senin - Kamis<br>
                    08:00 - 16:00 WIB
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-map-marker-alt text-green-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Lokasi Koperasi</h3>
                <p class="text-sm text-gray-600">
                    Kampus Universitas<br>
                    Yogyakarta
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-headset text-purple-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Butuh Bantuan?</h3>
                <p class="text-sm text-gray-600">
                    Hubungi kami untuk<br>
                    informasi lebih lanjut
                </p>
            </div>
        </div>
    </div>
</div>
