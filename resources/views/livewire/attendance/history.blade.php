<div class="space-y-6">
    {{-- Header --}}
    <x-layout.page-header title="Riwayat Absensi">
        <x-slot:actions>
            <x-ui.button variant="secondary" wire:click="export">
                <x-ui.icon name="download" class="w-5 h-5 mr-2" />
                Export Excel
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    {{-- Filters --}}
    <x-ui.card>
        <x-layout.grid cols="4" gap="4">
            <div>
                <x-ui.input
                    type="date"
                    name="dateFrom"
                    label="Dari Tanggal"
                    wire:model="dateFrom"
                />
            </div>
            <div>
                <x-ui.input
                    type="date"
                    name="dateTo"
                    label="Sampai Tanggal"
                    wire:model="dateTo"
                />
            </div>
            <div>
                <x-ui.select
                    name="status"
                    label="Status"
                    wire:model.live="status"
                    :options="[
                        '' => 'Semua Status',
                        'present' => 'Hadir',
                        'late' => 'Terlambat',
                        'absent' => 'Tidak Hadir'
                    ]"
                />
            </div>
            <div class="flex items-end space-x-2">
                <x-ui.button variant="primary" class="flex-1" wire:click="applyFilter">
                    <x-ui.icon name="filter" class="w-5 h-5 mr-2" />
                    Filter
                </x-ui.button>
                <x-ui.button variant="white" wire:click="resetFilter">
                    Reset
                </x-ui.button>
            </div>
        </x-layout.grid>
    </x-ui.card>

    {{-- Table --}}
    <x-ui.card padding="false">
        <x-data.table 
            :headers="['Tanggal', 'Hari', 'Check-in', 'Check-out', 'Durasi', 'Status', 'Lokasi']"
            striped="true"
            hoverable="true"
        >
            @forelse($attendances as $attendance)
                <x-data.table-row>
                    <x-data.table-cell>{{ $attendance->check_in->format('d/m/Y') }}</x-data.table-cell>
                    <x-data.table-cell>{{ $attendance->check_in->locale('id')->dayName }}</x-data.table-cell>
                    <x-data.table-cell>{{ $attendance->check_in->format('H:i') }}</x-data.table-cell>
                    <x-data.table-cell>{{ $attendance->check_out ? $attendance->check_out->format('H:i') : '-' }}</x-data.table-cell>
                    <x-data.table-cell>
                        @if($attendance->check_out)
                            {{ $attendance->check_in->diffInHours($attendance->check_out) }}j 
                            {{ $attendance->check_in->diffInMinutes($attendance->check_out) % 60 }}m
                        @else
                            -
                        @endif
                    </x-data.table-cell>
                    <x-data.table-cell>
                        <x-ui.badge 
                            :variant="$attendance->status === 'present' ? 'success' : ($attendance->status === 'late' ? 'warning' : 'danger')"
                            size="sm"
                        >
                            {{ ucfirst($attendance->status) }}
                        </x-ui.badge>
                    </x-data.table-cell>
                    <x-data.table-cell>
                        @if($attendance->location_lat && $attendance->location_lng)
                            <a href="https://maps.google.com/?q={{ $attendance->location_lat }},{{ $attendance->location_lng }}" 
                               target="_blank" 
                               class="text-primary-600 hover:text-primary-800 text-sm inline-flex items-center">
                                <x-ui.icon name="map-pin" class="w-4 h-4 mr-1" />
                                Lihat Peta
                            </a>
                        @else
                            -
                        @endif
                    </x-data.table-cell>
                </x-data.table-row>
            @empty
                <x-data.table-row>
                    <x-data.table-cell class="text-center py-8 text-gray-500" colspan="7">
                        <x-layout.empty-state
                            icon="document-text"
                            title="Tidak ada data absensi"
                        />
                    </x-data.table-cell>
                </x-data.table-row>
            @endforelse
        </x-data.table>
    </x-ui.card>

    {{-- Pagination --}}
    <x-data.pagination :paginator="$attendances" />
</div>
