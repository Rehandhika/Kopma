<div class="space-y-6">
    <!-- Page Header -->
    <x-layout.page-header 
        title="Penalti Saya"
        description="Lihat dan kelola penalti yang Anda terima"
    />

    <!-- Warning Alert for High Points -->
    @if($summary['total_points'] >= 50)
        <x-ui.alert variant="danger" :dismissible="true" icon>
            <div class="font-medium">Peringatan: Total Poin Penalti Tinggi</div>
            <div class="mt-1 text-sm">
                Anda memiliki {{ $summary['total_points'] }} poin penalti aktif. Harap segera menyelesaikan masalah ini untuk menghindari sanksi lebih lanjut.
            </div>
        </x-ui.alert>
    @elseif($summary['total_points'] >= 30)
        <x-ui.alert variant="warning" :dismissible="true" icon>
            <div class="font-medium">Perhatian: Poin Penalti Meningkat</div>
            <div class="mt-1 text-sm">
                Anda memiliki {{ $summary['total_points'] }} poin penalti aktif. Mohon perhatikan kehadiran dan kinerja Anda.
            </div>
        </x-ui.alert>
    @endif

    <!-- Summary Cards -->
    <x-layout.grid cols="4" gap="4">
        <x-layout.stat-card
            label="Total Poin"
            :value="$summary['total_points']"
            icon="exclamation-triangle"
            iconColor="bg-danger-100"
            iconTextColor="text-danger-600"
        />
        
        <x-layout.stat-card
            label="Aktif"
            :value="$summary['by_status']['active'] ?? 0"
            icon="exclamation-circle"
            iconColor="bg-gray-100"
            iconTextColor="text-gray-600"
        />
        
        <x-layout.stat-card
            label="Banding"
            :value="$summary['by_status']['appealed'] ?? 0"
            icon="clock"
            iconColor="bg-warning-100"
            iconTextColor="text-warning-600"
        />
        
        <x-layout.stat-card
            label="Total Penalti"
            :value="$summary['count']"
            icon="document-text"
            iconColor="bg-gray-100"
            iconTextColor="text-gray-600"
        />
    </x-layout.grid>

    <!-- Filter -->
    <x-ui.card padding="true">
        <x-ui.select 
            name="statusFilter" 
            wire:model.live="statusFilter"
            label="Filter Status">
            <option value="">Semua Status</option>
            <option value="active">Aktif</option>
            <option value="appealed">Banding</option>
            <option value="dismissed">Dibatalkan</option>
            <option value="expired">Kedaluwarsa</option>
        </x-ui.select>
    </x-ui.card>

    <!-- Penalty List -->
    <x-ui.card padding="false">
        <x-data.table :headers="['Tanggal', 'Jenis', 'Deskripsi', 'Poin', 'Status']">
            @forelse($penalties as $penalty)
                <x-data.table-row>
                    <x-data.table-cell>
                        {{ $penalty->date->format('d/m/Y') }}
                    </x-data.table-cell>
                    <x-data.table-cell>
                        {{ $penalty->penaltyType->name ?? '-' }}
                    </x-data.table-cell>
                    <x-data.table-cell>
                        <div class="max-w-xs truncate">{{ $penalty->description }}</div>
                    </x-data.table-cell>
                    <x-data.table-cell>
                        <span class="font-semibold text-danger-600">{{ $penalty->points }}</span>
                    </x-data.table-cell>
                    <x-data.table-cell>
                        <x-ui.badge 
                            :variant="match($penalty->status) {
                                'active' => 'danger',
                                'appealed' => 'warning',
                                'dismissed' => 'secondary',
                                'expired' => 'gray',
                                default => 'gray'
                            }">
                            {{ match($penalty->status) {
                                'active' => 'Aktif',
                                'appealed' => 'Banding',
                                'dismissed' => 'Dibatalkan',
                                'expired' => 'Kedaluwarsa',
                                default => ucfirst($penalty->status)
                            } }}
                        </x-ui.badge>
                    </x-data.table-cell>
                </x-data.table-row>
            @empty
                <x-data.table-row>
                    <x-data.table-cell colspan="5">
                        <x-layout.empty-state
                            icon="document-text"
                            title="Tidak ada penalti"
                            description="Anda tidak memiliki penalti saat ini"
                        />
                    </x-data.table-cell>
                </x-data.table-row>
            @endforelse
        </x-data.table>
    </x-ui.card>

    <!-- Pagination -->
    <x-data.pagination :paginator="$penalties" />
</div>
