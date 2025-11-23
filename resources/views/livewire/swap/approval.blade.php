<div class="space-y-6">
    <!-- Page Header with Stats -->
    <x-layout.page-header title="Persetujuan Tukar Shift">
        <x-slot:actions>
            <div class="flex items-center space-x-3">
                <x-ui.badge variant="warning" size="md">
                    Menunggu: <strong>{{ $stats['pending'] }}</strong>
                </x-ui.badge>
                <x-ui.badge variant="success" size="md">
                    Disetujui Hari Ini: <strong>{{ $stats['approved_today'] }}</strong>
                </x-ui.badge>
            </div>
        </x-slot:actions>
    </x-layout.page-header>

    <div class="space-y-4">
        @forelse($swaps as $swap)
            <x-ui.card>
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-4">
                            <x-ui.badge variant="info">Diterima oleh target</x-ui.badge>
                            <span class="text-sm text-gray-500">{{ $swap->created_at->diffForHumans() }}</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- From -->
                            <div class="border-r border-gray-200 pr-6">
                                <div class="text-xs font-medium text-gray-500 uppercase mb-2">Dari</div>
                                <div class="space-y-3">
                                    <div class="flex items-center space-x-3">
                                        <x-ui.avatar :name="$swap->requester->name" size="md" />
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $swap->requester->name }}</div>
                                            <div class="text-sm text-gray-600">{{ $swap->requester->nim }}</div>
                                        </div>
                                    </div>
                                    <div class="bg-primary-50 p-3 rounded-lg">
                                        <div class="text-sm font-medium text-primary-900">
                                            {{ $swap->originalSchedule->date->format('d/m/Y') }}
                                        </div>
                                        <div class="text-sm text-primary-700">
                                            Sesi {{ $swap->originalSchedule->session }} • 
                                            {{ Carbon\Carbon::parse($swap->originalSchedule->time_start)->format('H:i') }} - 
                                            {{ Carbon\Carbon::parse($swap->originalSchedule->time_end)->format('H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- To -->
                            <div class="pl-6">
                                <div class="text-xs font-medium text-gray-500 uppercase mb-2">Ke</div>
                                <div class="space-y-3">
                                    <div class="flex items-center space-x-3">
                                        <x-ui.avatar :name="$swap->target->name" size="md" />
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $swap->target->name }}</div>
                                            <div class="text-sm text-gray-600">{{ $swap->target->nim }}</div>
                                        </div>
                                    </div>
                                    @if($swap->targetSchedule)
                                        <div class="bg-secondary-50 p-3 rounded-lg">
                                            <div class="text-sm font-medium text-secondary-900">
                                                {{ $swap->targetSchedule->date->format('d/m/Y') }}
                                            </div>
                                            <div class="text-sm text-secondary-700">
                                                Sesi {{ $swap->targetSchedule->session }} • 
                                                {{ Carbon\Carbon::parse($swap->targetSchedule->time_start)->format('H:i') }} - 
                                                {{ Carbon\Carbon::parse($swap->targetSchedule->time_end)->format('H:i') }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($swap->reason)
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="text-sm font-medium text-gray-700 mb-1">Alasan:</div>
                                <div class="text-sm text-gray-900">{{ $swap->reason }}</div>
                            </div>
                        @endif
                    </div>

                    <div class="ml-6">
                        <x-ui.button 
                            wire:click="viewDetails({{ $swap->id }})" 
                            variant="primary">
                            Review
                        </x-ui.button>
                    </div>
                </div>
            </x-ui.card>
        @empty
            <x-ui.card>
                <x-layout.empty-state 
                    icon="document-text" 
                    title="Tidak ada permintaan tukar shift yang menunggu persetujuan"
                    description="Semua permintaan telah diproses" />
            </x-ui.card>
        @endforelse
    </div>

    <div>{{ $swaps->links() }}</div>

    <!-- Modal -->
    @if($showModal && $selectedSwap)
        <x-ui.modal name="swap-approval" title="Review Tukar Shift" maxWidth="2xl">
            <div class="space-y-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="text-sm text-gray-600 mb-3">Kedua pihak telah menyetujui pertukaran ini</div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs font-medium text-gray-500 uppercase mb-1">Pemohon</div>
                            <div class="flex items-center space-x-2">
                                <x-ui.avatar :name="$selectedSwap->requester->name" size="sm" />
                                <span class="font-medium text-gray-900">{{ $selectedSwap->requester->name }}</span>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 uppercase mb-1">Target</div>
                            <div class="flex items-center space-x-2">
                                <x-ui.avatar :name="$selectedSwap->target->name" size="sm" />
                                <span class="font-medium text-gray-900">{{ $selectedSwap->target->name }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <x-ui.textarea 
                    wire:model="approvalNotes"
                    label="Catatan Persetujuan (Opsional)"
                    name="approvalNotes"
                    rows="3"
                    placeholder="Tambahkan catatan jika diperlukan..." />
            </div>

            <x-slot:footer>
                <x-ui.button 
                    wire:click="$set('showModal', false)" 
                    variant="white">
                    Batal
                </x-ui.button>
                <x-ui.button 
                    wire:click="reject({{ $selectedSwap->id }})" 
                    variant="danger">
                    Tolak
                </x-ui.button>
                <x-ui.button 
                    wire:click="approve({{ $selectedSwap->id }})" 
                    variant="success">
                    Setujui & Tukar Jadwal
                </x-ui.button>
            </x-slot:footer>
        </x-ui.modal>
    @endif
</div>
