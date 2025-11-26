@props([
    'date',
    'session',
    'assignments' => [],
    'status' => 'normal',
    'userCount' => 0,
    'isFull' => false,
])

@php
    // Enhanced status colors with gradients and better visual hierarchy
    $statusColors = [
        'conflict' => 'border-red-500 bg-gradient-to-br from-red-50 to-red-100 shadow-red-200',
        'warning' => 'border-yellow-500 bg-gradient-to-br from-yellow-50 to-yellow-100 shadow-yellow-200',
        'empty' => 'border-gray-300 bg-gradient-to-br from-gray-50 to-gray-100 shadow-gray-200',
        'overstaffed' => 'border-orange-500 bg-gradient-to-br from-orange-50 to-orange-100 shadow-orange-200',
        'edited' => 'border-blue-500 bg-gradient-to-br from-blue-50 to-blue-100 shadow-blue-200',
        'normal' => 'border-green-500 bg-gradient-to-br from-green-50 to-green-100 shadow-green-200',
    ];
    
    $statusIcons = [
        'conflict' => ['icon' => '❌', 'label' => 'Konflik', 'color' => 'text-red-600'],
        'warning' => ['icon' => '⚠️', 'label' => 'Peringatan', 'color' => 'text-yellow-600'],
        'empty' => ['icon' => '📭', 'label' => 'Kosong', 'color' => 'text-gray-600'],
        'overstaffed' => ['icon' => '📊', 'label' => 'Overstaffed', 'color' => 'text-orange-600'],
        'edited' => ['icon' => '✏️', 'label' => 'Diedit', 'color' => 'text-blue-600'],
        'normal' => ['icon' => '✅', 'label' => 'Normal', 'color' => 'text-green-600'],
    ];
    
    $borderColor = $statusColors[$status] ?? 'border-gray-300 bg-white';
    $statusInfo = $statusIcons[$status] ?? ['icon' => '', 'label' => '', 'color' => 'text-gray-600'];
    
    // User count badge styling
    $countBadgeColor = 'bg-blue-100 text-blue-700';
    if ($userCount === 0) {
        $countBadgeColor = 'bg-gray-100 text-gray-600';
    } elseif ($userCount >= 3) {
        $countBadgeColor = 'bg-orange-100 text-orange-700';
    } elseif ($userCount >= 1) {
        $countBadgeColor = 'bg-green-100 text-green-700';
    }
@endphp

<div 
    class="border-2 rounded-xl p-4 min-h-[160px] transition-all duration-300 hover:shadow-lg hover:scale-[1.02] {{ $borderColor }} relative overflow-hidden"
    x-data="{ expanded: false, showActions: false }"
    @mouseenter="showActions = true"
    @mouseleave="showActions = false"
    :class="{ 'ring-2 ring-blue-400 ring-opacity-50': showActions }"
>
    <!-- Status Indicator Strip -->
    <div class="absolute top-0 left-0 right-0 h-1 {{ $statusInfo['color'] }} opacity-50"></div>
    
    <!-- Header: User Count Badge & Status -->
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center space-x-2">
            <!-- User Count Badge with Icon -->
            <div class="flex items-center space-x-1.5 px-2.5 py-1 {{ $countBadgeColor }} rounded-full font-semibold text-xs shadow-sm">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                </svg>
                <span>{{ $userCount }}</span>
            </div>
            
            @if($isFull)
                <span class="px-2 py-1 bg-orange-500 text-white rounded-full text-xs font-bold shadow-sm animate-pulse">
                    PENUH
                </span>
            @endif
        </div>
        
        <!-- Status Icon with Tooltip -->
        @if($statusInfo['icon'])
            <div class="flex items-center space-x-1" x-data x-tooltip="'{{ $statusInfo['label'] }}'">
                <span class="text-lg">{{ $statusInfo['icon'] }}</span>
            </div>
        @endif
    </div>

    <!-- Users List with Enhanced Design -->
    @if($userCount > 0)
        <div class="space-y-1.5 mb-3">
            @foreach($assignments as $index => $assignment)
                @if($index < 3 || $userCount <= 3)
                    <x-schedule.user-badge 
                        :assignment="$assignment"
                        :showRemove="true"
                    />
                @endif
            @endforeach
            
            @if($userCount > 3)
                <button 
                    @click="expanded = !expanded"
                    class="w-full text-xs text-blue-600 hover:text-blue-800 hover:bg-blue-50 py-2 text-center rounded-lg transition-all duration-200 font-medium"
                >
                    <div class="flex items-center justify-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!expanded">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="expanded" x-cloak>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                        <span x-show="!expanded">Tampilkan {{ $userCount - 3 }} lainnya</span>
                        <span x-show="expanded" x-cloak>Sembunyikan</span>
                    </div>
                </button>
                
                <div x-show="expanded" x-cloak x-transition class="space-y-1.5 mt-1">
                    @foreach($assignments as $index => $assignment)
                        @if($index >= 3)
                            <x-schedule.user-badge 
                                :assignment="$assignment"
                                :showRemove="true"
                            />
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    @else
        <div class="text-xs text-gray-500 mb-3 text-center py-6">
            <div class="text-3xl mb-2 opacity-50">📭</div>
            <div class="font-medium text-gray-600">Slot Kosong</div>
            <div class="text-gray-400 mt-1">Belum ada user ditugaskan</div>
        </div>
    @endif

    <!-- Enhanced Actions Slot with Hover Effect -->
    <div 
        class="transition-all duration-300"
        x-bind:class="showActions ? 'opacity-100' : 'opacity-70'"
    >
        {{ $slot }}
    </div>
</div>
