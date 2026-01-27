@props([
    'item' => [],
    'accessible' => true,
    'children' => [],
])

@php
// Check if any child is accessible
$hasAccessibleChildren = collect($children)->contains('accessible', true);

// Parent is accessible if it has at least one accessible child
$parentAccessible = $accessible || $hasAccessibleChildren;

// Base classes for dropdown button
$baseClasses = 'w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2';
$activeClasses = 'bg-indigo-50 text-indigo-700';
$inactiveClasses = 'text-gray-700 hover:bg-gray-100 hover:text-gray-900';
$lockedClasses = 'text-gray-400 cursor-not-allowed opacity-60';

// Submenu link classes
$submenuBaseClasses = 'block px-3 py-2 text-sm rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2';
$submenuActiveClasses = 'bg-indigo-50 text-indigo-700 font-medium';
$submenuInactiveClasses = 'text-gray-600 hover:bg-gray-100 hover:text-gray-900';
$submenuLockedClasses = 'text-gray-400 cursor-not-allowed opacity-60';

// Build route patterns for active state detection
$routePatterns = collect($children)->map(function($child) {
    if (isset($child['active_routes']) && is_array($child['active_routes'])) {
        return $child['active_routes'];
    }
    $route = $child['route'] ?? null;
    if ($route) {
        $parts = explode('.', $route);
        if (count($parts) >= 2) {
            array_pop($parts);
            return [implode('.', $parts) . '.*', $route];
        }
        return [$route];
    }
    return [];
})->flatten()->filter()->unique()->toArray();

// Check if any child route pattern is active
$isChildActive = !empty($routePatterns) && request()->routeIs($routePatterns);
@endphp

<div x-data="{ open: {{ $isChildActive ? 'true' : 'false' }} }">
    @if($parentAccessible)
        {{-- Accessible Parent - Clickable dropdown button --}}
        <button @click="open = !open" 
                type="button"
                class="{{ $baseClasses }} {{ $isChildActive ? $activeClasses : $inactiveClasses }}"
                aria-expanded="false"
                :aria-expanded="open.toString()"
                aria-controls="{{ $item['key'] ?? 'submenu' }}-submenu">
            <div class="flex items-center min-w-0">
                @if(isset($item['icon']))
                    <x-ui.icon :name="$item['icon']" class="w-5 h-5 mr-3 flex-shrink-0" />
                @endif
                <span>{{ $item['label'] ?? '' }}</span>
            </div>
            <x-ui.icon name="chevron-down" class="w-4 h-4 ml-2 flex-shrink-0 transition-transform duration-200" ::class="{ 'rotate-180': open }" />
        </button>
        
        {{-- Submenu items --}}
        <div x-show="open" 
             x-collapse 
             id="{{ $item['key'] ?? 'submenu' }}-submenu"
             class="ml-6 mt-1 space-y-1 pl-3 border-l-2 border-gray-200"
             role="menu">
            @foreach($children as $child)
                @php
                    $childAccessible = $child['accessible'] ?? false;
                    $childRoute = $child['route'] ?? null;
                    $childActiveRoutes = $child['active_routes'] ?? null;
                    $childIsActive = false;
                    
                    if ($childActiveRoutes && is_array($childActiveRoutes)) {
                        $childIsActive = request()->routeIs($childActiveRoutes);
                    } elseif ($childRoute) {
                        $childIsActive = request()->routeIs($childRoute);
                        if (!$childIsActive) {
                            $parts = explode('.', $childRoute);
                            if (count($parts) >= 2) {
                                array_pop($parts);
                                $wildcardPattern = implode('.', $parts) . '.*';
                                $childIsActive = request()->routeIs($wildcardPattern);
                            }
                        }
                    }
                @endphp
                
                @if($childAccessible)
                    <a href="{{ isset($child['route']) ? route($child['route']) : '#' }}" 
                       class="{{ $submenuBaseClasses }} {{ $childIsActive ? $submenuActiveClasses : $submenuInactiveClasses }}"
                       role="menuitem"
                       aria-current="{{ $childIsActive ? 'page' : 'false' }}">
                        {{ $child['label'] ?? '' }}
                    </a>
                @else
                    <div class="{{ $submenuBaseClasses }} {{ $submenuLockedClasses }} flex items-center justify-between"
                         role="menuitem"
                         aria-disabled="true"
                         aria-label="{{ $child['label'] ?? '' }} - Akses terkunci"
                         tabindex="0"
                         title="Anda tidak memiliki akses ke menu ini"
                         x-data
                         @click.prevent="$dispatch('show-access-denied', { menu: '{{ $child['label'] ?? '' }}' })"
                         @keydown.enter.prevent="$dispatch('show-access-denied', { menu: '{{ $child['label'] ?? '' }}' })"
                         @keydown.space.prevent="$dispatch('show-access-denied', { menu: '{{ $child['label'] ?? '' }}' })">
                        <span>{{ $child['label'] ?? '' }}</span>
                        <x-ui.icon name="lock-closed" class="w-3 h-3 ml-2 text-gray-400" aria-label="Menu terkunci" />
                    </div>
                @endif
            @endforeach
        </div>
    @else
        {{-- Locked Parent - All children are locked --}}
        <div class="{{ $baseClasses }} {{ $lockedClasses }}"
             role="button"
             aria-disabled="true"
             aria-label="{{ $item['label'] ?? '' }} - Semua submenu terkunci"
             tabindex="0"
             title="Semua submenu dalam kategori ini terkunci"
             x-data
             @click.prevent="$dispatch('show-access-denied', { menu: '{{ $item['label'] ?? '' }}' })"
             @keydown.enter.prevent="$dispatch('show-access-denied', { menu: '{{ $item['label'] ?? '' }}' })"
             @keydown.space.prevent="$dispatch('show-access-denied', { menu: '{{ $item['label'] ?? '' }}' })">
            <div class="flex items-center min-w-0">
                @if(isset($item['icon']))
                    <x-ui.icon :name="$item['icon']" class="w-5 h-5 mr-3 flex-shrink-0" />
                @endif
                <span>{{ $item['label'] ?? '' }}</span>
            </div>
            <x-ui.icon name="lock-closed" class="w-4 h-4 ml-2 text-gray-400" aria-label="Menu terkunci" />
        </div>
    @endif
</div>
