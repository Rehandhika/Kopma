<div class="p-6">
    <x-layout.page-header 
        title="Laporan Penalti"
        description="Analisis dan statistik penalti karyawan"
    >
        <x-slot:actions>
            <x-ui.button variant="white" icon="download">
                Export Excel
            </x-ui.button>
            <x-ui.button variant="primary" icon="printer">
                Cetak
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    {{-- Stats Cards --}}
    <x-layout.grid cols="4" class="mb-6">
        <x-layout.stat-card 
            label="Total Penalti"
            :value="$stats->total ?? 0"
            icon="exclamation-triangle"
            iconColor="bg-danger-100"
            iconTextColor="text-danger-600"
        />
        <x-layout.stat-card 
            label="Aktif"
            :value="$stats->active ?? 0"
            icon="exclamation-circle"
            iconColor="bg-warning-100"
            iconTextColor="text-warning-600"
        />
        <x-layout.stat-card 
            label="Banding"
            :value="$stats->appealed ?? 0"
            icon="information-circle"
            iconColor="bg-info-100"
            iconTextColor="text-info-600"
        />
        <x-layout.stat-card 
            label="Total Poin"
            :value="$stats->total_points ?? 0"
            subtitle="Poin Penalti"
            icon="chart-bar"
            iconColor="bg-secondary-100"
            iconTextColor="text-secondary-600"
        />
    </x-layout.grid>

    {{-- Filters --}}
    <x-ui.card class="mb-6">
        <x-layout.grid cols="4">
            <x-ui.input 
                label="Dari"
                name="dateFrom"
                type="date"
                wire:model.live="dateFrom"
            />
            <x-ui.input 
                label="Sampai"
                name="dateTo"
                type="date"
                wire:model.live="dateTo"
            />
            <x-ui.select 
                label="Karyawan"
                name="userFilter"
                wire:model.live="userFilter"
            >
                <option value="all">Semua</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </x-ui.select>
            <x-ui.select 
                label="Status"
                name="statusFilter"
                wire:model.live="statusFilter"
            >
                <option value="all">Semua</option>
                <option value="active">Aktif</option>
                <option value="appealed">Banding</option>
                <option value="dismissed">Dibatalkan</option>
                <option value="expired">Kadaluarsa</option>
            </x-ui.select>
        </x-layout.grid>
    </x-ui.card>

    {{-- Data Table --}}
    <x-ui.card>
        <x-data.table :headers="['Tanggal', 'Nama', 'NIM', 'Jenis Penalti', 'Poin', 'Deskripsi', 'Status']">
            @forelse($penalties as $penalty)
                <x-data.table-row>
                    <x-data.table-cell>{{ $penalty->date->format('d M Y') }}</x-data.table-cell>
                    <x-data.table-cell>{{ $penalty->user->name }}</x-data.table-cell>
                    <x-data.table-cell>{{ $penalty->user->nim ?? '-' }}</x-data.table-cell>
                    <x-data.table-cell>
                        <div class="flex flex-col">
                            <span class="font-medium text-gray-900">{{ $penalty->penaltyType->name }}</span>
                            <span class="text-xs text-gray-500">{{ $penalty->penaltyType->code }}</span>
                        </div>
                    </x-data.table-cell>
                    <x-data.table-cell class="text-center">
                        <span class="font-semibold text-danger-600">{{ $penalty->points }}</span>
                    </x-data.table-cell>
                    <x-data.table-cell>
                        <span class="text-sm text-gray-600">{{ $penalty->description ?? '-' }}</span>
                    </x-data.table-cell>
                    <x-data.table-cell class="text-center">
                        <x-ui.badge 
                            :variant="match($penalty->status) {
                                'active' => 'warning',
                                'appealed' => 'info',
                                'dismissed' => 'gray',
                                'expired' => 'secondary',
                                default => 'gray'
                            }"
                        >
                            {{ match($penalty->status) {
                                'active' => 'Aktif',
                                'appealed' => 'Banding',
                                'dismissed' => 'Dibatalkan',
                                'expired' => 'Kadaluarsa',
                                default => $penalty->status
                            } }}
                        </x-ui.badge>
                    </x-data.table-cell>
                </x-data.table-row>
            @empty
                <x-data.table-row>
                    <x-data.table-cell colspan="7">
                        <x-layout.empty-state 
                            icon="exclamation-triangle"
                            title="Tidak ada data penalti"
                            description="Ubah filter atau periode waktu untuk melihat data"
                        />
                    </x-data.table-cell>
                </x-data.table-row>
            @endforelse
        </x-data.table>

        <x-slot:footer>
            {{ $penalties->links() }}
        </x-slot:footer>
    </x-ui.card>
</div>
