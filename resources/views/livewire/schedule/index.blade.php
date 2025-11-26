<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
    {{-- Header --}}
    <div class="mb-4 sm:mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Manajemen Jadwal</h1>
                <p class="mt-1 text-sm text-gray-600">Kelola jadwal shift mingguan</p>
            </div>
            <x-ui.button href="{{ route('admin.schedule.create') }}" variant="primary" icon="plus" class="w-full sm:w-auto">
                Buat Jadwal Baru
            </x-ui.button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 mb-4 sm:mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select wire:model.live="filterStatus" class="input w-full">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                    <option value="archived">Archived</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                <select wire:model.live="filterMonth" class="input w-full">
                    <option value="">Semua Bulan</option>
                    @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}">{{ \Carbon\Carbon::create()->month($i)->locale('id')->monthName }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                <select wire:model.live="filterYear" class="input w-full">
                    <option value="">Semua Tahun</option>
                    @for($year = now()->year - 1; $year <= now()->year + 1; $year++)
                    <option value="{{ $year }}">{{ $year }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                <input type="text" wire:model.live.debounce.300ms="search" class="input w-full" placeholder="Cari jadwal...">
            </div>
        </div>
    </div>

    {{-- Schedule List --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($schedules->isEmpty())
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada jadwal</h3>
            <p class="text-gray-600 mb-4">Mulai dengan membuat jadwal baru</p>
            <x-ui.button href="{{ route('admin.schedule.create') }}" variant="primary" icon="plus">
                Buat Jadwal Baru
            </x-ui.button>
        </div>
        @else
        {{-- Desktop View --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statistik</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($schedules as $schedule)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($schedule->week_start_date)->locale('id')->isoFormat('D MMM') }} - 
                                {{ \Carbon\Carbon::parse($schedule->week_end_date)->locale('id')->isoFormat('D MMM YYYY') }}
                            </div>
                            @if($schedule->notes)
                            <div class="text-xs text-gray-500 mt-1">{{ Str::limit($schedule->notes, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($schedule->status === 'published')
                            <x-ui.badge variant="success">Published</x-ui.badge>
                            @elseif($schedule->status === 'draft')
                            <x-ui.badge variant="warning">Draft</x-ui.badge>
                            @else
                            <x-ui.badge variant="gray">Archived</x-ui.badge>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <span class="font-medium">{{ $schedule->assignments_count }}</span> assignments
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ number_format(($schedule->assignments_count / 12) * 100, 0) }}% coverage
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $schedule->created_at->format('d M Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $schedule->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <x-ui.button href="{{ route('admin.schedule.edit', $schedule) }}" variant="ghost" size="sm" icon="eye" title="View" />
                                @if($schedule->status === 'draft')
                                <x-ui.button wire:click="publish({{ $schedule->id }})" variant="ghost" size="sm" icon="check" title="Publish" />
                                @endif
                                <x-ui.button wire:click="delete({{ $schedule->id }})" wire:confirm="Yakin ingin menghapus jadwal ini?" variant="ghost" size="sm" icon="trash" class="text-red-600" title="Delete" />
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile View --}}
        <div class="md:hidden divide-y divide-gray-200">
            @foreach($schedules as $schedule)
            <div class="p-4">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-900">
                            {{ \Carbon\Carbon::parse($schedule->week_start_date)->locale('id')->isoFormat('D MMM') }} - 
                            {{ \Carbon\Carbon::parse($schedule->week_end_date)->locale('id')->isoFormat('D MMM YYYY') }}
                        </div>
                        @if($schedule->notes)
                        <div class="text-xs text-gray-500 mt-1 truncate">{{ $schedule->notes }}</div>
                        @endif
                    </div>
                    @if($schedule->status === 'published')
                    <x-ui.badge variant="success" class="ml-2">Published</x-ui.badge>
                    @elseif($schedule->status === 'draft')
                    <x-ui.badge variant="warning" class="ml-2">Draft</x-ui.badge>
                    @else
                    <x-ui.badge variant="gray" class="ml-2">Archived</x-ui.badge>
                    @endif
                </div>
                
                <div class="flex items-center justify-between text-sm mb-3">
                    <div>
                        <span class="font-medium text-gray-900">{{ $schedule->assignments_count }}</span>
                        <span class="text-gray-500"> assignments</span>
                    </div>
                    <div class="text-gray-500">
                        {{ number_format(($schedule->assignments_count / 12) * 100, 0) }}% coverage
                    </div>
                </div>
                
                <div class="text-xs text-gray-500 mb-3">
                    Dibuat {{ $schedule->created_at->diffForHumans() }}
                </div>
                
                <div class="flex items-center space-x-2">
                    <x-ui.button href="{{ route('admin.schedule.edit', $schedule) }}" variant="secondary" size="sm" icon="eye" class="flex-1">
                        View
                    </x-ui.button>
                    @if($schedule->status === 'draft')
                    <x-ui.button wire:click="publish({{ $schedule->id }})" variant="primary" size="sm" icon="check" class="flex-1">
                        Publish
                    </x-ui.button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($schedules->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $schedules->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
