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
<div x-data="{ open: {{ request()->routeIs('admin.schedule.*') ? 'true' : 'false' }} }">
    <button @click="open = !open" 
            type="button"
            class="{{ $dropdownButtonBaseClasses }} {{ request()->routeIs('admin.schedule.*') ? $linkActiveClasses : $linkInactiveClasses }}"
            aria-expanded="{{ request()->routeIs('admin.schedule.*') ? 'true' : 'false' }}"
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
        <a href="{{ route('admin.schedule.index') }}" 
           class="{{ $submenuLinkBaseClasses }} {{ request()->routeIs('admin.schedule.index') ? $submenuLinkActiveClasses : $submenuLinkInactiveClasses }}"
           role="menuitem"
           aria-current="{{ request()->routeIs('admin.schedule.index') ? 'page' : 'false' }}">
            Kalender Jadwal
        </a>
        <a href="{{ route('admin.schedule.my-schedule') }}" 
           class="{{ $submenuLinkBaseClasses }} {{ request()->routeIs('admin.schedule.my-schedule') ? $submenuLinkActiveClasses : $submenuLinkInactiveClasses }}"
           role="menuitem"
           aria-current="{{ request()->routeIs('admin.schedule.my-schedule') ? 'page' : 'false' }}">
            Jadwal Saya
        </a>
        <a href="{{ route('admin.schedule.availability') }}" 
           class="{{ $submenuLinkBaseClasses }} {{ request()->routeIs('admin.schedule.availability') ? $submenuLinkActiveClasses : $submenuLinkInactiveClasses }}"
           role="menuitem"
           aria-current="{{ request()->routeIs('admin.schedule.availability') ? 'page' : 'false' }}">
            Ketersediaan
        </a>
        <a href="{{ route('admin.schedule.create') }}" 
           class="{{ $submenuLinkBaseClasses }} {{ request()->routeIs('admin.schedule.create') ? $submenuLinkActiveClasses : $submenuLinkInactiveClasses }}"
           role="menuitem"
           aria-current="{{ request()->routeIs('admin.schedule.create') ? 'page' : 'false' }}">
            Tambah Jadwal
        </a>
        <a href="{{ route('admin.schedule.index') }}" 
           class="{{ $submenuLinkBaseClasses }} {{ request()->routeIs('admin.schedule.edit') || request()->routeIs('admin.schedule.history') ? $submenuLinkActiveClasses : $submenuLinkInactiveClasses }}"
           role="menuitem"
           aria-current="{{ request()->routeIs('admin.schedule.edit') || request()->routeIs('admin.schedule.history') ? 'page' : 'false' }}">
            Kelola Jadwal
        </a>
    </div>
</div>

{{-- Cashier / POS --}}
<a href="{{ route('admin.cashier.pos') }}" 
   class="{{ $linkBaseClasses }} {{ request()->routeIs('admin.cashier.*') ? $linkActiveClasses : $linkInactiveClasses }}"
   aria-current="{{ request()->routeIs('admin.cashier.*') ? 'page' : 'false' }}">
    <x-ui.icon name="currency-dollar" class="w-5 h-5 mr-3 flex-shrink-0" />
    <span>Kasir / POS</span>
</a>

{{-- Products --}}
<a href="{{ route('admin.products.index') }}" 
   class="{{ $linkBaseClasses }} {{ request()->routeIs('admin.products.*') ? $linkActiveClasses : $linkInactiveClasses }}"
   aria-current="{{ request()->routeIs('admin.products.*') ? 'page' : 'false' }}">
    <x-ui.icon name="shopping-cart" class="w-5 h-5 mr-3 flex-shrink-0" />
    <span>Produk</span>
</a>

{{-- Stock --}}
<a href="{{ route('admin.stock.index') }}" 
   class="{{ $linkBaseClasses }} {{ request()->routeIs('admin.stock.*') ? $linkActiveClasses : $linkInactiveClasses }}"
   aria-current="{{ request()->routeIs('admin.stock.*') ? 'page' : 'false' }}">
    <x-ui.icon name="inbox" class="w-5 h-5 mr-3 flex-shrink-0" />
    <span>Stok</span>
</a>

{{-- Leave Requests --}}
<div x-data="{ open: {{ request()->routeIs('admin.leave.*') ? 'true' : 'false' }} }">
    <button @click="open = !open" 
            type="button"
            class="{{ $dropdownButtonBaseClasses }} {{ request()->routeIs('admin.leave.*') ? $linkActiveClasses : $linkInactiveClasses }}"
            aria-expanded="{{ request()->routeIs('admin.leave.*') ? 'true' : 'false' }}"
            aria-controls="leave-submenu">
        <div class="flex items-center min-w-0">
            <x-ui.icon name="document-text" class="w-5 h-5 mr-3 flex-shrink-0" />
            <span>Izin/Cuti</span>
        </div>
        <x-ui.icon name="chevron-down" class="w-4 h-4 ml-2 flex-shrink-0 transition-transform duration-200" ::class="{ 'rotate-180': open }" />
    </button>
    <div x-show="open" 
         x-collapse 
         id="leave-submenu"
         class="ml-8 mt-1 space-y-1"
         role="menu">
        <a href="{{ route('admin.leave.my-requests') }}" 
           class="{{ $submenuLinkBaseClasses }} {{ request()->routeIs('admin.leave.my-requests') ? $submenuLinkActiveClasses : $submenuLinkInactiveClasses }}"
           role="menuitem"
           aria-current="{{ request()->routeIs('admin.leave.my-requests') ? 'page' : 'false' }}">
            Pengajuan Saya
        </a>
        <a href="{{ route('admin.leave.create') }}" 
           class="{{ $submenuLinkBaseClasses }} {{ request()->routeIs('admin.leave.create') ? $submenuLinkActiveClasses : $submenuLinkInactiveClasses }}"
           role="menuitem"
           aria-current="{{ request()->routeIs('admin.leave.create') ? 'page' : 'false' }}">
            Ajukan Izin
        </a>
        <a href="{{ route('admin.leave.approvals') }}" 
           class="{{ $submenuLinkBaseClasses }} {{ request()->routeIs('admin.leave.approvals') ? $submenuLinkActiveClasses : $submenuLinkInactiveClasses }}"
           role="menuitem"
           aria-current="{{ request()->routeIs('admin.leave.approvals') ? 'page' : 'false' }}">
            Persetujuan
        </a>
    </div>
</div>

{{-- Swap Requests --}}
<a href="{{ route('admin.swap.index') }}" 
   class="{{ $linkBaseClasses }} {{ request()->routeIs('admin.swap.*') ? $linkActiveClasses : $linkInactiveClasses }}"
   aria-current="{{ request()->routeIs('admin.swap.*') ? 'page' : 'false' }}">
    <x-ui.icon name="arrow-right" class="w-5 h-5 mr-3 flex-shrink-0" />
    <span>Tukar Jadwal</span>
</a>

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
    </div>
</div>

{{-- Profile --}}
<a href="{{ route('admin.profile.edit') }}" 
   class="{{ $linkBaseClasses }} {{ request()->routeIs('admin.profile.*') ? $linkActiveClasses : $linkInactiveClasses }}"
   aria-current="{{ request()->routeIs('admin.profile.*') ? 'page' : 'false' }}">
    <x-ui.icon name="user" class="w-5 h-5 mr-3 flex-shrink-0" />
    <span>Profil Saya</span>
</a>
