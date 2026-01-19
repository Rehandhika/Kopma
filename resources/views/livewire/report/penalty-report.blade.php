<div class="space-y-4 sm:space-y-6">
    {{-- Header --}}
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Laporan Penalti</h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
            {{ \Carbon\Carbon::parse($dateFrom)->translatedFormat('d M Y') }} - 
            {{ \Carbon\Carbon::parse($dateTo)->translatedFormat('d M Y') }}
        </p>
    </div>

    {{-- Period & Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 sm:p-4 border border-gray-200 dark:border-gray-700 space-y-3">
        {{-- Period Buttons --}}
        <div class="flex flex-wrap gap-2">
            @foreach(['today' => 'Hari Ini', 'week' => 'Minggu', 'month' => 'Bulan'] as $key => $label)
                <button wire:click="setPeriod('{{ $key }}')"
                    @class([
                        'px-3 py-1.5 text-xs sm:text-sm font-medium rounded-lg transition',
                        'bg-primary-600 text-white' => $period === $key,
                        'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300' => $period !== $key,
                    ])>
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Filters --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3">
            <input type="date" wire:model.live.debounce.500ms="dateFrom" 
                class="px-2 py-1.5 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <input type="date" wire:model.live.debounce.500ms="dateTo" 
                class="px-2 py-1.5 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            
            <x-ui.dropdown-select 
                wire="userFilter"
                :options="array_merge(
                    [['value' => 'all', 'label' => 'Semua User']],
                    collect($this->users)->map(fn($u) => ['value' => (string)$u->id, 'label' => $u->name])->toArray()
                )"
                placeholder="Semua User"
                :searchable="true"
            />
            
            <x-ui.dropdown-select 
                wire="statusFilter"
                :options="[
                    ['value' => 'all', 'label' => 'Semua Status'],
                    ['value' => 'active', 'label' => 'Aktif'],
                    ['value' => 'appealed', 'label' => 'Banding'],
                    ['value' => 'dismissed', 'label' => 'Dibatalkan'],
                    ['value' => 'expired', 'label' => 'Kadaluarsa'],
                ]"
                placeholder="Semua Status"
            />
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-3 sm:p-4 text-white">
            <p class="text-red-100 text-xs font-medium">Total Penalti</p>
            <p class="text-lg sm:text-xl font-bold mt-1">{{ number_format($this->stats->total ?? 0) }}</p>
            <p class="text-red-200 text-xs mt-0.5">kasus</p>
        </div>
        
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg p-3 sm:p-4 text-white">
            <p class="text-amber-100 text-xs font-medium">Aktif</p>
            <p class="text-lg sm:text-xl font-bold mt-1">{{ number_format($this->stats->active ?? 0) }}</p>
            <p class="text-amber-200 text-xs mt-0.5">penalti</p>
        </div>
        
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-3 sm:p-4 text-white">
            <p class="text-blue-100 text-xs font-medium">Banding</p>
            <p class="text-lg sm:text-xl font-bold mt-1">{{ number_format($this->stats->appealed ?? 0) }}</p>
            <p class="text-blue-200 text-xs mt-0.5">pengajuan</p>
        </div>
        
        <div class="bg-gradient-to-br from-violet-500 to-violet-600 rounded-lg p-3 sm:p-4 text-white">
            <p class="text-violet-100 text-xs font-medium">Total Poin</p>
            <p class="text-lg sm:text-xl font-bold mt-1">{{ number_format($this->stats->total_points ?? 0) }}</p>
            <p class="text-violet-200 text-xs mt-0.5">poin penalti</p>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="font-semibold text-sm text-gray-900 dark:text-white">Detail Penalti</h3>
            <span class="text-xs text-gray-500">{{ $penalties->total() }} data</span>
        </div>

        {{-- Mobile Cards --}}
        <div class="sm:hidden divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($penalties as $penalty)
                @php
                    $statusConfig = match($penalty->status) {
                        'active' => ['label' => 'Aktif', 'class' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400'],
                        'appealed' => ['label' => 'Banding', 'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400'],
                        'dismissed' => ['label' => 'Dibatalkan', 'class' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400'],
                        'expired' => ['label' => 'Kadaluarsa', 'class' => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-500'],
                        default => ['label' => $penalty->status, 'class' => 'bg-gray-100 text-gray-700']
                    };
                @endphp
                <div class="p-3 space-y-1">
                    <div class="flex justify-between items-start">
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-sm text-gray-900 dark:text-white truncate">{{ $penalty->user->name ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $penalty->user->nim ?? '-' }}</p>
                        </div>
                        <span class="px-1.5 py-0.5 rounded text-xs font-medium {{ $statusConfig['class'] }} shrink-0 ml-2">
                            {{ $statusConfig['label'] }}
                        </span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">{{ $penalty->date->format('d/m/Y') }}</span>
                        <span class="font-semibold text-red-600 dark:text-red-400">{{ $penalty->points }} poin</span>
                    </div>
                    <div class="text-xs">
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ $penalty->penaltyType->name ?? '-' }}</span>
                        <span class="text-gray-400">({{ $penalty->penaltyType->code ?? '-' }})</span>
                    </div>
                    @if($penalty->description)
                        <p class="text-xs text-gray-500 truncate">{{ $penalty->description }}</p>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-gray-400 text-sm">Tidak ada data penalti</div>
            @endforelse
        </div>

        {{-- Desktop Table --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-2.5 text-left">Tanggal</th>
                        <th class="px-4 py-2.5 text-left">Nama</th>
                        <th class="px-4 py-2.5 text-left">Jenis</th>
                        <th class="px-4 py-2.5 text-center">Poin</th>
                        <th class="px-4 py-2.5 text-left">Deskripsi</th>
                        <th class="px-4 py-2.5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($penalties as $penalty)
                        @php
                            $statusConfig = match($penalty->status) {
                                'active' => ['label' => 'Aktif', 'class' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400'],
                                'appealed' => ['label' => 'Banding', 'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400'],
                                'dismissed' => ['label' => 'Dibatalkan', 'class' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400'],
                                'expired' => ['label' => 'Kadaluarsa', 'class' => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-500'],
                                default => ['label' => $penalty->status, 'class' => 'bg-gray-100 text-gray-700']
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30">
                            <td class="px-4 py-2.5 text-gray-600 dark:text-gray-400">{{ $penalty->date->format('d/m/Y') }}</td>
                            <td class="px-4 py-2.5">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $penalty->user->name ?? '-' }}</p>
                                <p class="text-xs text-gray-500">{{ $penalty->user->nim ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-2.5">
                                <p class="text-gray-900 dark:text-white">{{ $penalty->penaltyType->name ?? '-' }}</p>
                                <p class="text-xs text-gray-500">{{ $penalty->penaltyType->code ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                <span class="font-semibold text-red-600 dark:text-red-400">{{ $penalty->points }}</span>
                            </td>
                            <td class="px-4 py-2.5 text-gray-600 dark:text-gray-400 max-w-xs truncate">
                                {{ $penalty->description ?? '-' }}
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                <span class="px-2 py-0.5 rounded text-xs font-medium {{ $statusConfig['class'] }}">
                                    {{ $statusConfig['label'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-400 text-sm">Tidak ada data penalti</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($penalties->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $penalties->links() }}
            </div>
        @endif
    </div>

    {{-- Loading --}}
    <div wire:loading.delay class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg px-4 py-3 shadow-lg flex items-center gap-2">
            <svg class="animate-spin h-4 w-4 text-primary-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span class="text-sm text-gray-700 dark:text-gray-300">Memuat...</span>
        </div>
    </div>
</div>
