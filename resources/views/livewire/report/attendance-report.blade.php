<div class="p-6">
    <x-layout.page-header 
        title="Laporan Kehadiran"
        description="Analisis dan statistik kehadiran karyawan"
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
            label="Total Kehadiran"
            :value="$stats['total']"
            icon="clipboard-list"
            iconColor="bg-primary-100"
            iconTextColor="text-primary-600"
        />
        <x-layout.stat-card 
            label="Hadir"
            :value="$stats['present']"
            icon="check-circle"
            iconColor="bg-success-100"
            iconTextColor="text-success-600"
        />
        <x-layout.stat-card 
            label="Terlambat"
            :value="$stats['late']"
            icon="clock"
            iconColor="bg-warning-100"
            iconTextColor="text-warning-600"
        />
        <x-layout.stat-card 
            label="Tidak Hadir"
            :value="$stats['absent']"
            icon="x-circle"
            iconColor="bg-danger-100"
            iconTextColor="text-danger-600"
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
                label="User"
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
                <option value="present">Hadir</option>
                <option value="late">Terlambat</option>
                <option value="absent">Tidak Hadir</option>
            </x-ui.select>
        </x-layout.grid>
    </x-ui.card>

    {{-- Data Table --}}
    <x-ui.card>
        <x-data.table :headers="['Tanggal', 'Nama', 'Check In', 'Check Out', 'Jam Kerja', 'Status']">
            @forelse($attendances as $attendance)
                <x-data.table-row>
                    <x-data.table-cell>{{ $attendance->date->format('d M Y') }}</x-data.table-cell>
                    <x-data.table-cell>{{ $attendance->user->name }}</x-data.table-cell>
                    <x-data.table-cell class="text-center">{{ $attendance->check_in ?? '-' }}</x-data.table-cell>
                    <x-data.table-cell class="text-center">{{ $attendance->check_out ?? '-' }}</x-data.table-cell>
                    <x-data.table-cell class="text-center">{{ $attendance->work_hours ?? '-' }}</x-data.table-cell>
                    <x-data.table-cell class="text-center">
                        <x-ui.badge 
                            :variant="match($attendance->status) {
                                'present' => 'success',
                                'late' => 'warning',
                                'absent' => 'danger',
                                'excused' => 'info',
                                default => 'gray'
                            }"
                        >
                            {{ match($attendance->status) {
                                'present' => 'Hadir',
                                'late' => 'Terlambat',
                                'absent' => 'Tidak Hadir',
                                'excused' => 'Izin',
                                default => $attendance->status
                            } }}
                        </x-ui.badge>
                    </x-data.table-cell>
                </x-data.table-row>
            @empty
                <x-data.table-row>
                    <x-data.table-cell colspan="6">
                        <x-layout.empty-state 
                            icon="clipboard-list"
                            title="Tidak ada data kehadiran"
                            description="Ubah filter atau periode waktu untuk melihat data"
                        />
                    </x-data.table-cell>
                </x-data.table-row>
            @endforelse
        </x-data.table>

        <x-slot:footer>
            {{ $attendances->links() }}
        </x-slot:footer>
    </x-ui.card>
</div>
