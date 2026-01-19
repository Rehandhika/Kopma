<div class="space-y-4 sm:space-y-6">
    {{-- Header --}}
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Laporan Kehadiran</h1>
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
                    ['value' => 'present', 'label' => 'Hadir'],
                    ['value' => 'late', 'label' => 'Terlambat'],
                    ['value' => 'absent', 'label' => 'Tidak Hadir'],
                ]"
                placeholder="Semua Status"
            />
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-3 sm:p-4 text-white">
            <p class="text-blue-100 text-xs font-medium">Total</p>
            <p class="text-lg sm:text-xl font-bold mt-1">{{ number_format($this->stats->total ?? 0) }}</p>
            <p class="text-blue-200 text-xs mt-0.5">kehadiran</p>
        </div>
        
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg p-3 sm:p-4 text-white">
            <p class="text-emerald-100 text-xs font-medium">Hadir</p>
            <p class="text-lg sm:text-xl font-bold mt-1">{{ number_format($this->stats->present ?? 0) }}</p>
            <p class="text-emerald-200 text-xs mt-0.5">tepat waktu</p>
        </div>
        
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg p-3 sm:p-4 text-white">
            <p class="text-amber-100 text-xs font-medium">Terlambat</p>
            <p class="text-lg sm:text-xl font-bold mt-1">{{ number_format($this->stats->late ?? 0) }}</p>
            <p class="text-amber-200 text-xs mt-0.5">kali</p>
        </div>
        
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-3 sm:p-4 text-white">
            <p class="text-red-100 text-xs font-medium">Tidak Hadir</p>
            <p class="text-lg sm:text-xl font-bold mt-1">{{ number_format($this->stats->absent ?? 0) }}</p>
            <p class="text-red-200 text-xs mt-0.5">kali</p>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="font-semibold text-sm text-gray-900 dark:text-white">Detail Kehadiran</h3>
            <span class="text-xs text-gray-500">{{ $attendances->total() }} data</span>
        </div>

        {{-- Mobile Cards --}}
        <div class="sm:hidden divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($attendances as $attendance)
                @php
                    $statusConfig = match($attendance->status) {
                        'present' => ['label' => 'Hadir', 'class' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-400'],
                        'late' => ['label' => 'Terlambat', 'class' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400'],
                        'absent' => ['label' => 'Tidak Hadir', 'class' => 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400'],
                        'excused' => ['label' => 'Izin', 'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400'],
                        default => ['label' => $attendance->status, 'class' => 'bg-gray-100 text-gray-700']
                    };
                @endphp
                <div class="p-3 space-y-1">
                    <div class="flex justify-between items-start">
                        <span class="font-medium text-sm text-gray-900 dark:text-white">{{ $attendance->user->name ?? '-' }}</span>
                        <span class="px-1.5 py-0.5 rounded text-xs font-medium {{ $statusConfig['class'] }}">
                            {{ $statusConfig['label'] }}
                        </span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>{{ $attendance->date->format('d/m/Y') }}</span>
                        <span>{{ $attendance->check_in ?? '-' }} - {{ $attendance->check_out ?? '-' }}</span>
                    </div>
                    @if($attendance->work_hours)
                        <div class="text-xs text-gray-500">Jam kerja: {{ $attendance->work_hours }}</div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-gray-400 text-sm">Tidak ada data kehadiran</div>
            @endforelse
        </div>

        {{-- Desktop Table --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-2.5 text-left">Tanggal</th>
                        <th class="px-4 py-2.5 text-left">Nama</th>
                        <th class="px-4 py-2.5 text-center">Check In</th>
                        <th class="px-4 py-2.5 text-center">Check Out</th>
                        <th class="px-4 py-2.5 text-center">Jam Kerja</th>
                        <th class="px-4 py-2.5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($attendances as $attendance)
                        @php
                            $statusConfig = match($attendance->status) {
                                'present' => ['label' => 'Hadir', 'class' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-400'],
                                'late' => ['label' => 'Terlambat', 'class' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400'],
                                'absent' => ['label' => 'Tidak Hadir', 'class' => 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400'],
                                'excused' => ['label' => 'Izin', 'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400'],
                                default => ['label' => $attendance->status, 'class' => 'bg-gray-100 text-gray-700']
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30">
                            <td class="px-4 py-2.5 text-gray-600 dark:text-gray-400">{{ $attendance->date->format('d/m/Y') }}</td>
                            <td class="px-4 py-2.5 text-gray-900 dark:text-white font-medium">{{ $attendance->user->name ?? '-' }}</td>
                            <td class="px-4 py-2.5 text-center text-gray-600 dark:text-gray-400">{{ $attendance->check_in ?? '-' }}</td>
                            <td class="px-4 py-2.5 text-center text-gray-600 dark:text-gray-400">{{ $attendance->check_out ?? '-' }}</td>
                            <td class="px-4 py-2.5 text-center text-gray-600 dark:text-gray-400">{{ $attendance->work_hours ?? '-' }}</td>
                            <td class="px-4 py-2.5 text-center">
                                <span class="px-2 py-0.5 rounded text-xs font-medium {{ $statusConfig['class'] }}">
                                    {{ $statusConfig['label'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-400 text-sm">Tidak ada data kehadiran</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $attendances->links() }}
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
