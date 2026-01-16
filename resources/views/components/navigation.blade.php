@php
// Navigation link base classes
$linkBaseClasses = 'flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2';
$linkActiveClasses = 'bg-indigo-50 text-indigo-700';
$linkInactiveClasses = 'text-gray-700 hover:bg-gray-100 hover:text-gray-900';

// Submenu link classes
$submenuLinkBaseClasses = 'block px-3 py-2 text-sm rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2';
$submenuLinkActiveClasses = 'bg-indigo-50 text-indigo-700 font-medium';
$submenuLinkInactiveClasses = 'text-gray-600 hover:bg-gray-100 hover:text-gray-900';

// Dropdown button classes
$dropdownButtonBaseClasses = 'w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2';
@endphp

{{-- Dashboard --}}
<a href="{{ route('admin.dashboard') }}" 
   class="{{ $linkBaseClasses }} {{ request()->routeIs('admin.dashboard') ? $linkActiveClasses : $linkInactiveClasses }}"
   aria-current="{{ request()->routeIs('admin.dashboard') ? 'page' : 'false' }}">
    <x-ui.icon name="home" class="w-5 h-5 mr-3 flex-shrink-0" />
    <span>Dashboard</span>
</a>

{{-- Attendance --}}
<div x-data="{ open: {{ request()->routeIs('admin.attendance.*') ? 'true' : 'false' }} }">
    <button @click="open = !open" 
            type="button"
            class="{{ $dropdownButtonBaseClasses }} {{ request()->routeIs('admin.attendance.*') ? $linkActiveClasses : $linkInactiveClasses }}"
            aria-expanded="{{ request()->routeIs('admin.attendance.*') ? 'true' : 'false' }}"
            aria-controls="attendance-submenu">
        <div class="flex items-center min-w-0">
            <x-ui.icon name="clipboard-list" class="w-5 h-5 mr-3 flex-shrink-0" />
            <span>Absensi</span>
        </div>
        <x-ui.icon name="chevron-down" class="w-4 h-4 ml-2 flex-shrink-0 transition-transform duration-200" ::class="{ 'rotate-180': open }" />
    </button>
    <div x-show="open" 
         x-collapse 
         id="attendance-submenu"
         class="ml-8 mt-1 space-y-1"
         role="menu">
        <a href="{{ route('admin.attendance.check-in-out') }}" 
           class="{{ $submenuLinkBaseClasses }} {{ request()->routeIs('admin.attendance.check-in-out') ? $submenuLinkActiveClasses : $submenuLinkInactiveClasses }}"
           role="menuitem"
           aria-current="{{ request()->routeIs('admin.attendance.check-in-out') ? 'page' : 'false' }}">
            Check In/Out
        </a>
        <a href="{{ route('admin.attendance.index') }}" 
           class="{{ $submenuLinkBaseClasses }} {{ request()->routeIs('admin.attendance.index') ? $submenuLinkActiveClasses : $submenuLinkInactiveClasses }}"
           role="menuitem"
           aria-current="{{ request()->routeIs('admin.attendance.index') ? 'page' : 'false' }}">
            Daftar Absensi
        </a>
        <a href="{{ route('admin.attendance.history') }}" 
           class="{{ $submenuLinkBaseClasses }} {{ request()->routeIs('admin.attendance.history') ? $submenuLinkActiveClasses : $submenuLinkInactiveClasses }}"
           role="menuitem"
           aria-current="{{ request()->routeIs('admin.attendance.history') ? 'page' : 'false' }}">
            Riwayat
        </a>
    </div>
</div>

{{-- Schedule --}}
<div x-data="{ open: {{ request()->routeIs('admin.schedule.*') || request()->routeIs('admin.leave.*') || request()->routeIs('admin.swap.*') ? 'true' : 'false' }} }">
    <button @click="open = !open" 
            type="button"
            class="{{ $dropdownButtonBaseClasses }} {{ request()->routeIs('admin.schedule.*') || request()->routeIs('admin.leave.*') || request()->routeIs('admin.swap.*') ? $linkActiveClasses : $linkInactiveClasses }}"
            aria-expanded="{{ request()->routeIs('admin.schedule.*') || request()->routeIs('admin.leave.*') || request()->routeIs('admin.swap.*') ? 'true' : 'false' }}"
            aria-controls="schedule-submenu">
        <div class="flex items-center min-w-0">
            <x-ui.icon name="calendar" class="w-5 h-5 mr-3 flex-shrink-0" />
            <span>Jadwal</span>
        </div>
        <x-ui.icon name="chevron-down" class="w-4 h-4 ml-2 flex-shrink-0 transition-transform duration-200" ::class="{ 'rotate-180': open }" />
    </button>
    <div x-show="open" 
         x-collapse 
         id="schedule-submenu"
         class="ml-8 mt-1 space-y-1"
         role="menu">
        {{-- Kelola Jadwal --}}
        <a href="{{ route('admin.schedule.index') }}" 
           class="{{ $submenuLinkBaseClasses }} {{ request()->routeIs('admin.schedule.index') || request()->routeIs('admin.schedule.create') || request()->routeIs('admin.schedule.edit') || request()->routeIs('admin.schedule.history') ? $submenuLinkActiveClasses : $submenuLinkInactiveClasses }}"
           role="menuitem">
            Kelola Jadwal
        </a>
        {{-- Jadwal Saya --}}
        <a href="{{ route('admin.schedule.my-schedule') }}" 
           class="{{ $submenuLinkBaseClasses }} {{ request()->routeIs('admin.schedule.my-schedule') ? $submenuLinkActiveClasses : $submenuLinkInactiveClasses }}"
           role="menuitem">
            Jadwal Saya
        </a>
        {{-- Ketersediaan --}}
        <a href="{{ route('admin.schedule.availability') }}" 
           class="{{ $submenuLinkBaseClasses }} {{ request()->routeIs('admin.schedule.availability') ? $submenuLinkActiveClasses : $submenuLinkInactiveClasses }}"
           role="menuitem">
            Ketersediaan
        </a>
        {{-- Izin/Cuti --}}
        <a href="{{ route('admin.leave.index') }}" 
           class="{{ $submenuLinkBaseClasses }} {{ request()->routeIs('admin.leave.*') ? $submenuLinkActiveClasses : $submenuLinkInactiveClasses }}"
           role="menuitem">
            Izin/Cuti
        </a>
        {{-- Perubahan Jadwal --}}
        <a href="{{ route('admin.swap.index') }}" 
           class="{{ $submenuLinkBaseClasses }} {{ request()->routeIs('admin.swap.*') ? $submenuLinkActiveClasses : $submenuLinkInactiveClasses }}"
           role="menuitem">
            Perubahan Jadwal
        </a>
    </div>
</div>

{{-- Cashier / POS --}}
<div x-data="{ open: {{ request()->routeIs('admin.cashier.*') ? 'true' : 'false' }} }">
    <button @click="open = !open" 
            type="button"
            class="{{ $dropdownButtonBaseClasses }} {{ request()->routeIs('admin.cashier.*') ? $linkActiveClasses : $linkInactiveClasses }}"
            aria-expanded="{{ request()->routeIs('admin.cashier.*') ? 'true' : 'false' }}"
            aria-controls="cashier-submenu">
        <div class="flex items-center min-w-0">
            <x-ui.icon name="currency-dollar" class="w-5 h-5 mr-3 flex-shrink-0" />
            <span>Kasir / POS</span>
        </div>
        <x-ui.icon name="chevron-down" class="w-4 h-4 ml-2 flex-shrink-0 transition-transform duration-200" ::class="{ 'rotate-180': open }" />
    </button>
    <div x-show="open" 
         x-collapse 
         id="cashier-submenu"
         class="ml-8 mt-1 space-y-1"
         role="menu">
        <a href="{{ route('admin.cashier.pos') }}" 
           class="{{ $submenuLinkBaseClasses }} {{ request()->routeIs('admin.cashier.pos') ? $submenuLinkActiveClasses : $submenuLinkInactiveClasses }}"
           role="menuitem"
           aria-current="{{ request()->routeIs('admin.cashier.pos') ? 'page' : 'false' }}">
            POS Kasir
        </a>
        @if(auth()->user()->hasAnyRole(['Super Admin', 'Ketua', 'Wakil Ketua']))
        <a href="{{ route('admin.cashier.pos-entry') }}" 
           class="{{ $submenuLinkBaseClasses }} {{ request()->routeIs('admin.cashier.pos-entry') ? $submenuLinkActiveClasses : $submenuLinkInactiveClasses }}"
           role="menuitem"
           aria-current="{{ request()->routeIs('admin.cashier.pos-entry') ? 'page' : 'false' }}">
            Entry Transaksi
        </a>
        @endif
    </div>
</div>

{{-- Inventaris (Produk & Stok) --}}
<div x-data="{ open: {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.stock.*') ? 'true' : 'false' }} }">
    <button @click="open = !open" 
            type="button"
            class="{{ $dropdownButtonBaseClasses }} {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.stock.*') ? $linkActiveClasses : $linkInactiveClasses }}"
            aria-expanded="{{ request()->routeIs('admin.products.*') || request()->routeIs('admin.stock.*') ? 'true' : 'false' }}"
            aria-controls="inventory-submenu">
        <div class="flex items-center min-w-0">
            <x-ui.icon name="cube" class="w-5 h-5 mr-3 flex-shrink-0" />
            <span>Inventaris</span>
        </div>
        <x-ui.icon name="chevron-down" class="w-4 h-4 ml-2 flex-shrink-0 transition-transform duration-200" ::class="{ 'rotate-180': open }" />
    </button>
    <div x-show="open" 
         x-collapse 
         id="inventory-submenu"
         class="ml-8 mt-1 space-y-1"
         role="menu">
        <a href="{{ route('admin.products.index') }}" 
           class="{{ $submenuLinkBaseClasses }} {{ request()->routeIs('admin.products.*') ? $submenuLinkActiveClasses : $submenuLinkInactiveClasses }}"
           role="menuitem"
           aria-current="{{ request()->routeIs('admin.products.*') ? 'page' : 'false' }}">
            Daftar Produk
        </a>
        <a href="{{ route('admin.stock.index') }}" 
           class="{{ $submenuLinkBaseClasses }} {{ request()->routeIs('admin.stock.*') ? $submenuLinkActiveClasses : $submenuLinkInactiveClasses }}"
           role="menuitem"
           aria-current="{{ request()->routeIs('admin.stock.*') ? 'page' : 'false' }}">
            Manajemen Stok
        </a>
    </div>
</div>

{{-- Penalties --}}
<a href="{{ route('admin.penalties.index') }}" 
   class="{{ $linkBaseClasses }} {{ request()->routeIs('admin.penalties.*') ? $linkActiveClasses : $linkInactiveClasses }}"
   aria-current="{{ request()->routeIs('admin.penalties.*') ? 'page' : 'false' }}">
    <x-ui.icon name="exclamation-triangle" class="w-5 h-5 mr-3 flex-shrink-0" />
    <span>Sanksi</span>
</a>

{{-- Reports --}}
<div x-data="{ open: {{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }} }">
    <button @click="open = !open" 
            type="button"
            class="{{ $dropdownButtonBaseClasses }} {{ request()->routeIs('admin.reports.*') ? $linkActiveClasses : $linkInactiveClasses }}"
            aria-expanded="{{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }}"
            aria-controls="reports-submenu">
        <div class="flex items-center min-w-0">
            <x-ui.icon name="document" class="w-5 h-5 mr-3 flex-shrink-0" />
            <span>Laporan</span>
        </div>
        <x-ui.icon name="chevron-down" class="w-4 h-4 ml-2 flex-shrink-0 transition-transform duration-200" ::class="{ 'rotate-180': open }" />
    </button>
    <div x-show="open" 
         x-collapse 
         id="reports-submenu"
         class="ml-8 mt-1 space-y-1"
         role="menu">
        <a href="{{ route('admin.reports.attendance') }}" 
           class="{{ $submenuLinkBaseClasses }} {{ request()->routeIs('admin.reports.attendance') ? $submenuLinkActiveClasses : $submenuLinkInactiveClasses }}"
           role="menuitem"
           aria-current="{{ request()->routeIs('admin.reports.attendance') ? 'page' : 'false' }}">
            Laporan Absensi
        </a>
        <a href="{{ route('admin.reports.sales') }}" 
           class="{{ $submenuLinkBaseClasses }} {{ request()->routeIs('admin.reports.sales') ? $submenuLinkActiveClasses : $submenuLinkInactiveClasses }}"
           role="menuitem"
           aria-current="{{ request()->routeIs('admin.reports.sales') ? 'page' : 'false' }}">
            Laporan Penjualan
        </a>
        <a href="{{ route('admin.reports.penalties') }}" 
           class="{{ $submenuLinkBaseClasses }} {{ request()->routeIs('admin.reports.penalties') ? $submenuLinkActiveClasses : $submenuLinkInactiveClasses }}"
           role="menuitem"
           aria-current="{{ request()->routeIs('admin.reports.penalties') ? 'page' : 'false' }}">
            Laporan Sanksi
        </a>
    </div>
</div>

{{-- Divider --}}
<div class="border-t border-gray-200 my-2" role="separator"></div>

{{-- Users Management --}}
<a href="{{ route('admin.users.index') }}" 
   class="{{ $linkBaseClasses }} {{ request()->routeIs('admin.users.*') ? $linkActiveClasses : $linkInactiveClasses }}"
   aria-current="{{ request()->routeIs('admin.users.*') ? 'page' : 'false' }}">
    <x-ui.icon name="user-group" class="w-5 h-5 mr-3 flex-shrink-0" />
    <span>Manajemen User</span>
</a>

{{-- Roles & Permissions --}}
<a href="{{ route('admin.roles.index') }}" 
   class="{{ $linkBaseClasses }} {{ request()->routeIs('admin.roles.*') ? $linkActiveClasses : $linkInactiveClasses }}"
   aria-current="{{ request()->routeIs('admin.roles.*') ? 'page' : 'false' }}">
    <x-ui.icon name="check-circle" class="w-5 h-5 mr-3 flex-shrink-0" />
    <span>Role & Permission</span>
</a>

{{-- Settings --}}
<div x-data="{ open: {{ request()->routeIs('admin.settings.*') ? 'true' : 'false' }} }">
    <button @click="open = !open" 
            type="button"
            class="{{ $dropdownButtonBaseClasses }} {{ request()->routeIs('admin.settings.*') ? $linkActiveClasses : $linkInactiveClasses }}"
            aria-expanded="{{ request()->routeIs('admin.settings.*') ? 'true' : 'false' }}"
            aria-controls="settings-submenu">
        <div class="flex items-center min-w-0">
            <x-ui.icon name="cog" class="w-5 h-5 mr-3 flex-shrink-0" />
            <span>Pengaturan</span>
        </div>
        <x-ui.icon name="chevron-down" class="w-4 h-4 ml-2 flex-shrink-0 transition-transform duration-200" ::class="{ 'rotate-180': open }" />
    </button>
    <div x-show="open" 
         x-collapse 
         id="settings-submenu"
         class="ml-8 mt-1 space-y-1"
         role="menu">
        <a href="{{ route('admin.settings.general') }}" 
           class="{{ $submenuLinkBaseClasses }} {{ request()->routeIs('admin.settings.general') ? $submenuLinkActiveClasses : $submenuLinkInactiveClasses }}"
           role="menuitem"
           aria-current="{{ request()->routeIs('admin.settings.general') ? 'page' : 'false' }}">
            Pengaturan Umum
        </a>
        <a href="{{ route('admin.settings.system') }}" 
           class="{{ $submenuLinkBaseClasses }} {{ request()->routeIs('admin.settings.system') ? $submenuLinkActiveClasses : $submenuLinkInactiveClasses }}"
           role="menuitem"
           aria-current="{{ request()->routeIs('admin.settings.system') ? 'page' : 'false' }}">
            Pengaturan Sistem
        </a>
        @if(auth()->user()->hasAnyRole(['Super Admin', 'Ketua', 'Wakil Ketua']))
        <a href="{{ route('admin.settings.store') }}" 
           class="{{ $submenuLinkBaseClasses }} {{ request()->routeIs('admin.settings.store') ? $submenuLinkActiveClasses : $submenuLinkInactiveClasses }}"
           role="menuitem"
           aria-current="{{ request()->routeIs('admin.settings.store') ? 'page' : 'false' }}">
            Pengaturan Toko
        </a>
        @endif
        @if(auth()->user()->hasAnyRole(['Super Admin', 'Ketua']))
        <a href="{{ route('admin.settings.banners') }}" 
           class="{{ $submenuLinkBaseClasses }} {{ request()->routeIs('admin.settings.banners') ? $submenuLinkActiveClasses : $submenuLinkInactiveClasses }}"
           role="menuitem"
           aria-current="{{ request()->routeIs('admin.settings.banners') ? 'page' : 'false' }}">
            Kelola Banner
        </a>
        @endif
    </div>
</div>

{{-- Profile --}}
<a href="{{ route('admin.profile.edit') }}" 
   class="{{ $linkBaseClasses }} {{ request()->routeIs('admin.profile.*') ? $linkActiveClasses : $linkInactiveClasses }}"
   aria-current="{{ request()->routeIs('admin.profile.*') ? 'page' : 'false' }}">
    <x-ui.icon name="user" class="w-5 h-5 mr-3 flex-shrink-0" />
    <span>Profil Saya</span>
</a>
