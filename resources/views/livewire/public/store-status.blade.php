<div wire:poll.10s="refresh" 
     x-data="{ 
         isOpen: @entangle('isOpen'),
         showDetails: false 
     }"
     class="inline-flex items-center">
    
    <!-- Status Badge -->
    <div class="relative inline-flex items-center">
        <!-- Animated Badge -->
        <div :class="{
                'bg-success-100 text-success-800 border-success-300': isOpen,
                'bg-danger-100 text-danger-800 border-danger-300': !isOpen
             }"
             class="inline-flex items-center px-3 py-1.5 rounded-full border-2 font-semibold text-sm transition-all duration-300 cursor-pointer hover:shadow-md"
             @click="showDetails = !showDetails"
             role="button"
             tabindex="0"
             @keydown.enter="showDetails = !showDetails"
             @keydown.space.prevent="showDetails = !showDetails"
             :aria-expanded="showDetails"
             aria-label="Status koperasi">
            
            <!-- Pulse Animation (only when open) -->
            <span x-show="isOpen" 
                  class="absolute -left-1 -top-1 flex h-3 w-3"
                  x-transition:enter="transition ease-out duration-300"
                  x-transition:enter-start="opacity-0 scale-0"
                  x-transition:enter-end="opacity-100 scale-100">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-success-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-success-500"></span>
            </span>
            
            <!-- Status Icon -->
            <i :class="{
                'fa-store text-success-600': isOpen,
                'fa-store-slash text-danger-600': !isOpen
               }"
               class="fas mr-2 transition-all duration-300"
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="opacity-0 scale-50"
               x-transition:enter-end="opacity-100 scale-100"></i>
            
            <!-- Status Text -->
            <span x-text="isOpen ? 'BUKA' : 'TUTUP'" 
                  class="font-bold tracking-wide"
                  x-transition:enter="transition ease-out duration-300"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"></span>
            
            <!-- Dropdown Arrow -->
            <i class="fas fa-chevron-down ml-2 text-xs transition-transform duration-200"
               :class="{ 'rotate-180': showDetails }"></i>
        </div>
    </div>

    <!-- Details Dropdown -->
    <div x-show="showDetails"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         @click.away="showDetails = false"
         class="absolute top-full mt-2 right-0 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50 overflow-hidden"
         style="display: none;"
         role="region"
         aria-label="Detail status koperasi">
        
        <!-- Header -->
        <div :class="{
                'bg-gradient-to-r from-success-500 to-success-600': isOpen,
                'bg-gradient-to-r from-danger-500 to-danger-600': !isOpen
             }"
             class="px-4 py-3 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i :class="{
                        'fa-store': isOpen,
                        'fa-store-slash': !isOpen
                       }"
                       class="fas text-lg"></i>
                    <span class="font-bold text-lg" x-text="isOpen ? 'Koperasi Buka' : 'Koperasi Tutup'"></span>
                </div>
                <button @click="showDetails = false" 
                        type="button"
                        class="text-white hover:text-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-success-600 rounded-lg p-1"
                        aria-label="Tutup detail">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="p-4 space-y-3">
            <!-- Status Reason -->
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0 mt-0.5">
                    <i class="fas fa-info-circle text-primary-500"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-700">Status</p>
                    <p class="text-sm text-gray-600 mt-0.5">{{ $reason }}</p>
                </div>
            </div>

            <!-- Attendees (only when open) -->
            @if($isOpen && count($attendees) > 0)
                <div class="flex items-start space-x-3 pt-2 border-t border-gray-100"
                     x-show="isOpen"
                     x-transition:enter="transition ease-out duration-300 delay-100"
                     x-transition:enter-start="opacity-0 transform translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0">
                    <div class="flex-shrink-0 mt-0.5">
                        <i class="fas fa-users text-primary-500"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-700">Pengurus Bertugas</p>
                        <div class="mt-1.5 space-y-1">
                            @foreach($attendees as $attendee)
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-success-500 rounded-full animate-pulse"></div>
                                    <span class="text-sm text-gray-600">{{ $attendee }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Next Open Time (only when closed) -->
            @if(!$isOpen && $nextOpenTime)
                <div class="flex items-start space-x-3 pt-2 border-t border-gray-100"
                     x-show="!isOpen"
                     x-transition:enter="transition ease-out duration-300 delay-100"
                     x-transition:enter-start="opacity-0 transform translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0">
                    <div class="flex-shrink-0 mt-0.5">
                        <i class="fas fa-clock text-primary-500"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-700">Buka Berikutnya</p>
                        <p class="text-sm text-gray-600 mt-0.5">{{ $nextOpenTime }}</p>
                    </div>
                </div>
            @endif

            <!-- Last Updated -->
            <div class="pt-2 border-t border-gray-100">
                <p class="text-xs text-gray-500 text-center">
                    <i class="fas fa-sync-alt mr-1"></i>
                    Diperbarui otomatis setiap 10 detik
                </p>
            </div>
        </div>
    </div>
</div>
