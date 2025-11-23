<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'SIKOPMA') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Styles -->
    @vite(['resources/css/app.css'])
    @livewireStyles
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation Header -->
        <header class="bg-white shadow-sm sticky top-0 z-50" x-data="{ mobileMenuOpen: false }">
            <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo and Brand -->
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-bold text-lg">S</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xl font-bold text-gray-900">SIKOPMA</span>
                            <span class="text-xs text-gray-500 hidden sm:block">Koperasi Mahasiswa</span>
                        </div>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="{{ route('home') }}" 
                           class="text-gray-700 hover:text-primary-600 font-medium transition-colors {{ request()->routeIs('home') ? 'text-primary-600' : '' }}">
                            Katalog
                        </a>
                        <a href="{{ route('public.about') }}" 
                           class="text-gray-700 hover:text-primary-600 font-medium transition-colors {{ request()->routeIs('public.about') ? 'text-primary-600' : '' }}">
                            Tentang
                        </a>
                        
                        <!-- Store Status Badge Placeholder -->
                        <div class="pl-4 border-l border-gray-200">
                            @livewire('public.store-status')
                        </div>
                        
                        <!-- Login Button -->
                        <a href="{{ route('login') }}" 
                           class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Login
                        </a>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" 
                            type="button"
                            class="md:hidden inline-flex items-center justify-center p-2 rounded-lg text-gray-700 hover:text-primary-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500 transition-colors"
                            aria-expanded="false"
                            aria-label="Toggle menu">
                        <i class="fas fa-bars text-xl" x-show="!mobileMenuOpen"></i>
                        <i class="fas fa-times text-xl" x-show="mobileMenuOpen" style="display: none;"></i>
                    </button>
                </div>

                <!-- Mobile Navigation Menu -->
                <div x-show="mobileMenuOpen" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform -translate-y-2"
                     class="md:hidden border-t border-gray-200 py-4"
                     style="display: none;">
                    <div class="space-y-1">
                        <a href="{{ route('home') }}" 
                           class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-primary-600 font-medium transition-colors {{ request()->routeIs('home') ? 'bg-primary-50 text-primary-600' : '' }}">
                            Katalog
                        </a>
                        <a href="{{ route('public.about') }}" 
                           class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-primary-600 font-medium transition-colors {{ request()->routeIs('public.about') ? 'bg-primary-50 text-primary-600' : '' }}">
                            Tentang
                        </a>
                        
                        <!-- Store Status Badge for Mobile -->
                        <div class="px-4 py-2">
                            @livewire('public.store-status')
                        </div>
                        
                        <div class="px-4 pt-2">
                            <a href="{{ route('login') }}" 
                               class="block w-full text-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Login
                            </a>
                        </div>
                    </div>
                </div>
            </nav>
        </header>

        <!-- Main Content -->
        <main class="flex-1">
            {{ $slot ?? '' }}
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 text-gray-300 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- About Section -->
                    <div>
                        <div class="flex items-center space-x-2 mb-4">
                            <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="text-white font-bold text-sm">S</span>
                            </div>
                            <span class="text-xl font-bold text-white">SIKOPMA</span>
                        </div>
                        <p class="text-sm text-gray-400 leading-relaxed">
                            Sistem Informasi Koperasi Mahasiswa - Menyediakan berbagai kebutuhan mahasiswa dengan harga terjangkau.
                        </p>
                    </div>

                    <!-- Contact Information -->
                    <div>
                        <h3 class="text-white font-semibold text-lg mb-4">Kontak Kami</h3>
                        <div class="space-y-3">
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-phone text-primary-500 mt-1"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-300">Telepon</p>
                                    <p class="text-sm text-gray-400">{{ $contactPhone ?? '(0274) 123-4567' }}</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-envelope text-primary-500 mt-1"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-300">Email</p>
                                    <p class="text-sm text-gray-400">{{ $contactEmail ?? 'info@sikopma.ac.id' }}</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <i class="fab fa-whatsapp text-primary-500 mt-1"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-300">WhatsApp</p>
                                    <p class="text-sm text-gray-400">{{ $contactWhatsapp ?? '+62 812-3456-7890' }}</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-map-marker-alt text-primary-500 mt-1"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-300">Alamat</p>
                                    <p class="text-sm text-gray-400">{{ $contactAddress ?? 'Kampus Universitas, Yogyakarta' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Operating Hours -->
                    <div>
                        <h3 class="text-white font-semibold text-lg mb-4">Jam Operasional</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-400">Senin - Kamis</span>
                                <span class="text-gray-300 font-medium">08:00 - 16:00</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-400">Jumat - Minggu</span>
                                <span class="text-red-400 font-medium">Tutup</span>
                            </div>
                        </div>
                        <div class="mt-6 p-4 bg-gray-800 rounded-lg border border-gray-700">
                            <p class="text-xs text-gray-400 leading-relaxed">
                                <i class="fas fa-info-circle text-primary-500 mr-1"></i>
                                Status buka/tutup dapat berubah sewaktu-waktu. Cek status real-time di bagian atas halaman.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Bottom Bar -->
                <div class="mt-8 pt-8 border-t border-gray-800">
                    <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                        <p class="text-sm text-gray-400">
                            &copy; {{ date('Y') }} SIKOPMA. All rights reserved.
                        </p>
                        <div class="flex items-center space-x-6">
                            <a href="#" class="text-gray-400 hover:text-primary-500 transition-colors">
                                <i class="fab fa-facebook text-xl"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-primary-500 transition-colors">
                                <i class="fab fa-instagram text-xl"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-primary-500 transition-colors">
                                <i class="fab fa-twitter text-xl"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    @vite(['resources/js/app.js'])
    @livewireScripts
    
    {{-- Toast Notifications --}}
    <div x-data="{ 
        show: false, 
        message: '', 
        type: 'success',
        display(msg, msgType = 'success') {
            this.message = msg;
            this.type = msgType;
            this.show = true;
            setTimeout(() => { this.show = false; }, 3000);
        }
    }"
    @alert.window="display($event.detail.message, $event.detail.type)"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-2"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-2"
    class="fixed top-4 right-4 z-50 max-w-sm w-full pointer-events-none"
    style="display: none;"
    role="alert"
    aria-live="polite">
        <div :class="{
            'bg-success-50 border-success-200 text-success-800': type === 'success',
            'bg-danger-50 border-danger-200 text-danger-800': type === 'error',
            'bg-warning-50 border-warning-200 text-warning-800': type === 'warning',
            'bg-info-50 border-info-200 text-info-800': type === 'info'
        }" class="border-l-4 p-4 rounded-lg shadow-lg pointer-events-auto">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i x-show="type === 'success'" class="fas fa-check-circle text-success-400"></i>
                    <i x-show="type === 'error'" class="fas fa-times-circle text-danger-400"></i>
                    <i x-show="type === 'warning'" class="fas fa-exclamation-triangle text-warning-400"></i>
                    <i x-show="type === 'info'" class="fas fa-info-circle text-info-400"></i>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium" x-text="message"></p>
                </div>
                <button @click="show = false" 
                        type="button"
                        class="ml-auto flex-shrink-0 inline-flex text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 rounded-lg p-1 transition-colors"
                        aria-label="Close notification">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>
