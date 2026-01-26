@php
use App\Services\MenuAccessService;

// Get menu items with access state from MenuAccessService
$menuAccessService = app(MenuAccessService::class);
$menuItems = $menuAccessService->getMenuWithAccessState();
@endphp

{{-- Dynamic Menu Rendering --}}
@foreach($menuItems as $item)
    @if(isset($item['type']) && $item['type'] === 'divider')
        {{-- Divider --}}
        <div class="border-t border-gray-200 my-2" role="separator"></div>
    @elseif(isset($item['children']) && is_array($item['children']) && count($item['children']) > 0)
        {{-- Dropdown Menu with Children --}}
        <x-sidebar.menu-dropdown 
            :item="$item" 
            :accessible="$item['accessible'] ?? false" 
            :children="$item['children']" 
        />
    @else
        {{-- Single Menu Item --}}
        <x-sidebar.menu-item 
            :item="$item" 
            :accessible="$item['accessible'] ?? false" 
        />
    @endif
@endforeach
