<div class="space-y-6">
    <x-layout.page-header 
        title="Persetujuan Cuti"
        description="Kelola persetujuan permintaan cuti anggota">
        <x-slot:actions>
            <div class="flex items-center space-x-4">
                <x-ui.badge variant="warning" size="md">
                    Menunggu: <strong>{{ $stats['pending'] }}</strong>
                </x-ui.badge>
                <x-ui.badge variant="success" size="md">
                    Disetujui Hari Ini: <strong>{{ $stats['approved_today'] }}</strong>
                </x-ui.badge>
            </div>
        </x-slot:actions>
    </x-layout.page-header>

    <x-ui.card padding="false">
        <x-data.table :headers="['Tanggal Pengajuan', 'Anggota', 'Jenis Cuti', 'Periode', 'Durasi', 'Alasan', 'Aksi']">
            @forelse($leaves as $leave)
                <x-data.table-row>
                    <x-data.table-cell>{{ $leave->created_at->format('d/m/Y H:i') }}</x-data.table-cell>
                    <x-data.table-cell>
                        <div class="font-medium">{{ $leave->user->name }}</div>
                        <div class="text-sm text-gray-500">{{ $leave->user->nim }}</div>
                    </x-data.table-cell>
                    <x-data.table-cell>{{ $leave->leaveType->name ?? '-' }}</x-data.table-cell>
                    <x-data.table-cell>
                        <div class="text-sm">
                            {{ $leave->date_from->format('d/m/Y') }} -
                            {{ $leave->date_to->format('d/m/Y') }}
                        </div>
                    </x-data.table-cell>
                    <x-data.table-cell>{{ $leave->days }} hari</x-data.table-cell>
                    <x-data.table-cell>
                        <div class="max-w-xs truncate">{{ $leave->reason }}</div>
                    </x-data.table-cell>
                    <x-data.table-cell>
                        <x-ui.button 
                            variant="ghost" 
                            size="sm"
                            wire:click="viewDetails({{ $leave->id }})">
                            Detail
                        </x-ui.button>
                    </x-data.table-cell>
                </x-data.table-row>
            @empty
                <x-data.table-row>
                    <x-data.table-cell colspan="7">
                        <x-layout.empty-state 
                            icon="clipboard-check"
                            title="Tidak ada permintaan cuti yang menunggu persetujuan"
                            description="Semua permintaan cuti telah ditinjau" />
                    </x-data.table-cell>
                </x-data.table-row>
            @endforelse
        </x-data.table>
    </x-ui.card>

    <x-data.pagination :paginator="$leaves" />

    @if($showModal && $selectedLeave)
        <x-ui.modal 
            name="leave-detail" 
            title="Detail Permintaan Cuti"
            maxWidth="2xl"
            x-data 
            x-show="true"
            @click.away="$wire.set('showModal', false)">
            
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Nama</label>
                        <div class="mt-1 text-gray-900">{{ $selectedLeave->user->name }}</div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">NIM</label>
                        <div class="mt-1 text-gray-900">{{ $selectedLeave->user->nim }}</div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Jenis Cuti</label>
                        <div class="mt-1 text-gray-900">{{ $selectedLeave->leaveType->name }}</div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Durasi</label>
                        <div class="mt-1 text-gray-900">{{ $selectedLeave->days }} hari</div>
                    </div>
                    <div class="col-span-2">
                        <label class="text-sm font-medium text-gray-500">Periode</label>
                        <div class="mt-1 text-gray-900">
                            {{ $selectedLeave->date_from->format('d/m/Y') }} - 
                            {{ $selectedLeave->date_to->format('d/m/Y') }}
                        </div>
                    </div>
                    <div class="col-span-2">
                        <label class="text-sm font-medium text-gray-500">Alasan</label>
                        <div class="mt-1 text-gray-900">{{ $selectedLeave->reason }}</div>
                    </div>
                </div>

                <x-ui.textarea 
                    name="approvalNotes"
                    label="Catatan Persetujuan (Opsional)"
                    wire:model="approvalNotes"
                    rows="3" />
            </div>

            <x-slot:footer>
                <x-ui.button 
                    variant="white" 
                    wire:click="$set('showModal', false)">
                    Batal
                </x-ui.button>
                <x-ui.button 
                    variant="danger" 
                    wire:click="reject({{ $selectedLeave->id }})">
                    Tolak
                </x-ui.button>
                <x-ui.button 
                    variant="success" 
                    wire:click="approve({{ $selectedLeave->id }})">
                    Setujui
                </x-ui.button>
            </x-slot:footer>
        </x-ui.modal>
    @endif
</div>
