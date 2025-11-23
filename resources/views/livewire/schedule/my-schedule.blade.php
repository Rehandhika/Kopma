<div class="p-6">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Jadwal Saya</h1>
        <p class="mt-1 text-sm text-gray-600">Lihat jadwal shift Anda minggu ini</p>
    </div>

    {{-- Week Navigation --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between gap-4">
            <x-ui.button wire:click="previousWeek" variant="white" icon="chevron-left" class="flex-shrink-0">
                Minggu Lalu
            </x-ui.button>

            <div class="text-center flex-1">
                <div class="text-lg font-semibold text-gray-900 mb-2">
                    {{ $currentWeekStart->locale('id')->format('d M') }} - {{ $currentWeekEnd->locale('id')->format('d M Y') }}
                </div>
                @if($weekOffset === 0)
                <x-ui.badge variant="info" size="sm" rounded class="inline-flex">
                    Minggu Ini
                </x-ui.badge>
                @endif
            </div>

            <x-ui.button wire:click="nextWeek" variant="white" icon="chevron-right" class="flex-shrink-0">
                Minggu Depan
            </x-ui.button>
        </div>

        @if($weekOffset !== 0)
        <div class="mt-4 pt-4 border-t border-gray-200 text-center">
            <x-ui.button wire:click="currentWeek" variant="ghost" size="sm">
                Kembali ke Minggu Ini
            </x-ui.button>
        </div>
        @endif
    </div>

    {{-- Weekly Schedule Grid --}}
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <div class="min-w-full divide-y divide-gray-200">
                @foreach($weekDays as $day)
                <div @class([
                    'p-5 transition-colors duration-150',
                    'bg-blue-50' => $day['isToday'],
                    'bg-gray-50' => $day['isPast'] && !$day['isToday'],
                ])>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-base font-semibold text-gray-900">
                                {{ $day['dayName'] }}
                            </h3>
                            <p class="text-sm text-gray-600 mt-0.5">
                                {{ $day['date']->locale('id')->format('d M Y') }}
                            </p>
                        </div>
                        @if($day['isToday'])
                        <x-ui.badge variant="info" size="sm" rounded class="flex-shrink-0">
                            Hari Ini
                        </x-ui.badge>
                        @endif
                    </div>

                    @php
                        $dateKey = $day['date']->format('Y-m-d');
                        $assignments = $mySchedules[$dateKey] ?? collect();
                    @endphp

                    @if($assignments->count() > 0)
                        <div class="space-y-3">
                            @foreach($assignments as $assignment)
                            <div @class([
                                'p-4 rounded-lg border-l-4 shadow-sm transition-all duration-200 hover:shadow-md',
                                'bg-green-50 border-green-500' => $assignment->shift === 'pagi',
                                'bg-yellow-50 border-yellow-500' => $assignment->shift === 'siang',
                                'bg-purple-50 border-purple-500' => $assignment->shift === 'sore',
                                'bg-gray-100 border-gray-400' => !in_array($assignment->shift, ['pagi', 'siang', 'sore']),
                            ])>
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <div class="font-semibold text-gray-900 capitalize text-base">
                                            Shift {{ ucfirst($assignment->shift) }}
                                        </div>
                                        @if($assignment->time_start && $assignment->time_end)
                                        <div class="text-sm text-gray-600 mt-1.5 flex items-center">
                                            <svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span>{{ Carbon\Carbon::parse($assignment->time_start)->format('H:i') }} - 
                                            {{ Carbon\Carbon::parse($assignment->time_end)->format('H:i') }}</span>
                                        </div>
                                        @endif
                                    </div>
                                    @if(!$day['isPast'])
                                    <x-ui.button href="{{ route('swap.create') }}" variant="ghost" size="sm" class="flex-shrink-0">
                                        Tukar
                                    </x-ui.button>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm font-medium">Tidak ada jadwal</p>
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Upcoming Schedules Summary --}}
    @if($upcomingSchedules->count() > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-5">Jadwal Mendatang (7 Hari)</h3>
        <div class="space-y-3">
            @foreach($upcomingSchedules as $schedule)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors duration-150">
                <div class="flex items-center gap-4">
                    <div @class([
                        'w-14 h-14 rounded-lg flex items-center justify-center text-white flex-shrink-0 shadow-sm',
                        'bg-green-500' => $schedule->shift === 'pagi',
                        'bg-yellow-500' => $schedule->shift === 'siang',
                        'bg-purple-500' => $schedule->shift === 'sore',
                        'bg-gray-500' => !in_array($schedule->shift, ['pagi', 'siang', 'sore']),
                    ])>
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-gray-900 text-base">
                            {{ $schedule->date->locale('id')->format('l, d M Y') }}
                        </div>
                        <div class="text-sm text-gray-600 mt-1 flex items-center gap-1.5">
                            <span class="font-medium">Shift {{ ucfirst($schedule->shift) }}</span>
                            @if($schedule->time_start && $schedule->time_end)
                                <span class="text-gray-400">•</span>
                                <span>{{ Carbon\Carbon::parse($schedule->time_start)->format('H:i') }} - 
                                {{ Carbon\Carbon::parse($schedule->time_end)->format('H:i') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @if($schedule->date->isFuture())
                <span class="text-sm text-gray-500 font-medium flex-shrink-0">
                    {{ $schedule->date->diffForHumans() }}
                </span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Loading State --}}
    <div wire:loading class="fixed inset-0 bg-gray-900/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 shadow-xl">
            <x-ui.spinner size="lg" class="mx-auto" />
            <p class="mt-4 text-gray-700 font-medium">Memuat jadwal...</p>
        </div>
    </div>
</div>
